<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use App\Enums\BonusType;
class TopupController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $packages = \App\Models\Package::orderBy('id')->get();
        $wallet = DB::table('wallets')->where('user_id', $user->id)->first();

        // ✅ User’s wallet-done topups (new table below)
        $walletTransactions = DB::table('orders')
            ->where('from_user_id', $user->id)
            ->where('payment_by', 'Wallet')
            ->orderByDesc('id')
            ->paginate(5, ['*'], 'wallet_page');

        return view('member-topup', compact('user', 'packages', 'wallet', 'walletTransactions'));
    }
    public function store(Request $r)
    {
        $r->validate([
            'member_id' => 'required|string|exists:users,member_id',
            'package_id' => 'required|integer|exists:packages,id',
            'payment_by' => 'required|string',
        ]);

        $currentUser = Auth::user();
        $memberId = strtoupper(trim($r->member_id));

        $receiver = \App\Models\User::where('member_id', $memberId)->first();
        $package = DB::table('packages')->where('id', $r->package_id)->first();

        if (!$receiver) {
            return back()->with('error', 'Member not found.');
        }

        if (!$package) {
            return back()->with('error', 'Package not found.');
        }

        // Prevent duplicate/double-click submissions for the same user from
        // running this method concurrently and creating duplicate orders.
        $lock = \Illuminate\Support\Facades\Cache::lock("topup-lock-user-{$currentUser->id}", 15);

        if (!$lock->get()) {
            return back()->with('error', 'Your previous top-up is still processing. Please wait a moment before retrying.');
        }

        try {
            $packageName = strtoupper(trim((string) $package->name));
            $isRepurchaseBooster = $packageName === 'REPURCHASE BOOSTER PACKAGE';
            $isStarterPackage = $packageName === 'STARTER PACKAGE';
            // dd($packageName, $isStarterPackage);

            /*
    |--------------------------------------------------------------------------
    | Final Amount Calculation
    |--------------------------------------------------------------------------
    */
            $isFirstPurchase = !\App\Models\Order::where('user_id', $receiver->id)->exists();

            $baseAmount = $isFirstPurchase ? $package->actual_amount ?? $package->amount : $package->actual_amount ?? $package->amount;
            // dd($package->discounted_amount);

            $currentCount = $receiver->investment_count ?? 0;

            // ₹100 registration fee on a member's first purchase, only for
            // packages set to charge it (admin/packages)
            $registrationFee = $currentCount == 0 && !empty($package->charges_registration_fee) ? 100 : 0;
            // dd($baseAmount, $registrationFee);
            $finalAmount = (float) $baseAmount + $registrationFee;
            if ($isStarterPackage && $currentCount > 0) {
                $finalAmount = 1000;
            }
// dd($finalAmount);
            /*
    |--------------------------------------------------------------------------
    | Investment Count Increment
    |--------------------------------------------------------------------------
    */
            $packageId = (int) $r->package_id;

            $incrementValue = match ($packageId) {
                1 => 1,
                2 => 8,
                3 => 16,
                default => 1,
            };

            $newTotal = $currentCount + $incrementValue;

            /*
    |--------------------------------------------------------------------------
    | Lucky Service Logic
    |--------------------------------------------------------------------------
    */
            if (in_array($packageId, [4, 5])) {
                \App\Services\LuckyService::createCycleIfNotExists($receiver->id, $packageId);
            }

            DB::beginTransaction();

            try {
                /*
        |--------------------------------------------------------------------------
        | Wallet Balance Validation (locked to prevent race with concurrent requests)
        |--------------------------------------------------------------------------
        */
                $wallet = DB::table('wallets')->where('user_id', $currentUser->id)->lockForUpdate()->first();

                if (!$wallet || $wallet->balance < $finalAmount) {
                    DB::rollBack();
                    return back()->with('error', "Insufficient wallet balance. Need ₹{$finalAmount} to perform this top-up.");
                }

                /*
        |--------------------------------------------------------------------------
        | Deduct Wallet Balance
        |--------------------------------------------------------------------------
        */
                DB::table('wallets')
                    ->where('user_id', $currentUser->id)
                    ->update([
                        'balance' => $wallet->balance - $finalAmount,
                        'updated_at' => now(),
                    ]);

                /*
        |--------------------------------------------------------------------------
        | Record Debit Transaction
        |--------------------------------------------------------------------------
        */
                DB::table('transactions')->insert([
                    'user_id' => $currentUser->id,
                    'type' => 'Debit',
                    'amount' => $finalAmount,
                    'bonus_type' => BonusType::EmiPayment->value,
                    'remarks' => 'EMI payment for ' . $receiver->username . " ({$memberId})",
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                /*
        |--------------------------------------------------------------------------
        | Record Order
        |--------------------------------------------------------------------------
        */
                DB::table('orders')->insert([
                    'user_id' => $receiver->id,
                    'from_user_id' => $currentUser->id,
                    'package_id' => $package->id,
                    'amount' => $finalAmount,
                    'payment_by' => $r->payment_by,
                    'status' => 'completed',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                /*
        |--------------------------------------------------------------------------
        | Update Investment Count And EMI Status
        |--------------------------------------------------------------------------
        */
                DB::table('users')
                    ->where('id', $receiver->id)
                    ->update([
                        'investment_count' => $newTotal,
                        'emi_status' => $newTotal >= 16 ? 'completed' : 'ongoing',
                        'updated_at' => now(),
                    ]);

                /*
        |--------------------------------------------------------------------------
        | Binary / Pair Income
        |--------------------------------------------------------------------------
        | Conditions:
        | - No pair income for REPURCHASE BOOSTER PACKAGE.
        | - No pair income for packages 50,000 and above.
        | - Traverse upward from receiver's placement parent.
        | - Pay the package's pair bonus (% of newly matched business, or a
        |   fixed amount per matched package amount — set in admin/packages).
        | - Matching pool = this package + the packages it can pair with.
        | - Daily cap: 5,000 per user per day (excess is flushed, never paid
        |   on a later day — see checkAndDistributePairCompletionBonus()).
        | - STARTER PACKAGE pairs in two parts, each with its own pairings:
        |   ₹1600 first purchase (pays Starter's pair bonus) and ₹1000
        |   EMI/repurchase (pays the pair bonus of the package it pairs with).
        |--------------------------------------------------------------------------
        */
                $packageBusinessAmount = (float) ($package->amount ?? ($package->actual_amount ?? $finalAmount));

                // Starter package pair-income must reflect what was actually
                // paid THIS event (₹1600 first purchase vs ₹1000 EMI), not the
                // static catalog price — otherwise EMI repurchases would keep
                // being treated as a ₹1600 first purchase forever.
                if ($isStarterPackage) {
                    $packageBusinessAmount = $finalAmount;
                }

                if (!$isRepurchaseBooster && $packageBusinessAmount < 50000) {
                    $this->processBinaryPairIncomeForTopup($receiver, $packageBusinessAmount, $package);
                }

                /*
        |--------------------------------------------------------------------------
        | Reward Logic
        |--------------------------------------------------------------------------
        */
                if ($newTotal >= 16 && method_exists($this, 'rewardAfterFullEmi')) {
                    $this->rewardAfterFullEmi($receiver);
                }

                /*
        |--------------------------------------------------------------------------
        | Direct Commission
        |--------------------------------------------------------------------------
        | REPURCHASE BOOSTER PACKAGE should not give direct income.
        | Direct commission runs only on first purchase for normal packages.
        | Amount comes from the package's direct bonus (% or fixed — set in
        | admin/packages). EMI/repurchase never reaches here (it only runs
        | when $currentCount == 0).
        |--------------------------------------------------------------------------
        */
                if ($currentCount == 0 && !$isRepurchaseBooster) {
                    $this->distributeCommission($receiver->id, $packageBusinessAmount, $package);
                }

                DB::commit();

                /*
        |--------------------------------------------------------------------------
        | Matrix Service
        |--------------------------------------------------------------------------
        | REPURCHASE BOOSTER PACKAGE should only trigger MatrixService.
        |--------------------------------------------------------------------------
        */
                if ($isRepurchaseBooster) {
                    $matrixService = new \App\Services\MatrixService();
                    $matrixService->processCommission($receiver, $currentUser);
                }

                $successMessage = match ($packageId) {
                    4, 5 => "Congratulations! You have successfully paid ₹{$finalAmount} for Member {$memberId}. Vouchers have been issued.",
                    default => "Top-up successful! ₹{$finalAmount} deducted from your wallet for Member {$memberId}.",
                };

                return back()->with('success', $successMessage);
            } catch (\Exception $e) {
                if (DB::transactionLevel() > 0) {
                    DB::rollBack();
                }

                return back()->with('error', 'Something went wrong: ' . $e->getMessage());
            }
        } finally {
            $lock->release();
        }
    }

    private function processBinaryPairIncomeForTopup($receiver, float $packageAmount, $package): void
    {
        if (!$receiver || empty($receiver->placement_id)) {
            return;
        }

        $parentId = $receiver->placement_id;
        $visited = [];

        while ($parentId) {
            // Safety guard against wrong circular placement data
            if (in_array($parentId, $visited)) {
                break;
            }

            $visited[] = $parentId;

            $parent = DB::table('users')->where('id', $parentId)->first();

            if (!$parent) {
                break;
            }

            $this->checkAndDistributePairCompletionBonus($parent, $packageAmount, 'Normal Package', $package);

            $parentId = $parent->placement_id ?? null;
        }
    }

    private function checkAndDistributePairCompletionBonus($sponsor, $amount, $packageType, $package)
    {
        /*
    |--------------------------------------------------------------------------
    | Basic Restrictions
    |--------------------------------------------------------------------------
    */
        if (!$sponsor || (float) $amount >= 50000) {
            return;
        }

        /*
    |--------------------------------------------------------------------------
    | Lock Sponsor Row
    |--------------------------------------------------------------------------
    | Reduces duplicate payout risk if multiple top-ups happen at same time.
    |--------------------------------------------------------------------------
    */
        $sponsor = DB::table('users')->where('id', $sponsor->id)->lockForUpdate()->first();

        if (!$sponsor) {
            return;
        }

        /*
    |--------------------------------------------------------------------------
    | Get Complete Left And Right Subtree
    |--------------------------------------------------------------------------
    */
        $leftUsers = $this->getFullSubtreeUsers($sponsor->id, 'left');
        $rightUsers = $this->getFullSubtreeUsers($sponsor->id, 'right');

        if (empty($leftUsers) || empty($rightUsers)) {
            return;
        }

        $leftUserIds = collect($leftUsers)->pluck('id')->filter()->unique()->values()->toArray();

        $rightUserIds = collect($rightUsers)->pluck('id')->filter()->unique()->values()->toArray();

        if (empty($leftUserIds) || empty($rightUserIds)) {
            return;
        }

        /*
    |--------------------------------------------------------------------------
    | Shared Daily Cap Pool (Normal + Starter)
    |--------------------------------------------------------------------------
    | Each user can receive max ₹5,000/day combined across normal pair
    | income and starter pair income. Whatever matched volume can't be paid
    | out today because the cap is hit is FLUSHED below (marked as
    | processed regardless of payout) so it never gets paid on a later day.
    | Tomorrow's counter naturally starts at 0 because this query only
    | looks at today's transactions.
    |--------------------------------------------------------------------------
    */
        $dailyCap = 5000;

        $todayNormalPairIncome = (float) DB::table('transactions')
            ->where('user_id', $sponsor->id)
            ->where('bonus_type', BonusType::PairBonusNormal->value)
            ->whereDate('created_at', now()->toDateString())
            ->sum('amount');

        $todayStarterPairIncome = (float) DB::table('transactions')
            ->where('user_id', $sponsor->id)
            ->where('bonus_type', BonusType::PairBonusStarter->value)
            ->whereDate('created_at', now()->toDateString())
            ->sum('amount');

        $remainingCap = max(0, $dailyCap - $todayNormalPairIncome - $todayStarterPairIncome);

        /*
    |--------------------------------------------------------------------------
    | Matching Pool + Pair Bonus Rule (from admin/packages)
    |--------------------------------------------------------------------------
    | Pool = the bought package + the packages it can pair with. Each leg's
    | available volume = pool volume not yet used for pair income (tracked
    | per leg, per package), so the same volume is never paid twice even
    | when different packages have different pairings.
    |--------------------------------------------------------------------------
    */
        $pool = $this->resolvePairPool($package, (float) $amount);

        if (!$pool || $pool['bonus'] <= 0) {
            return;
        }

        $legs = [
            'left' => $this->getLegClassVolumes($leftUserIds),
            'right' => $this->getLegClassVolumes($rightUserIds),
        ];
        $this->seedLegacyConsumption($sponsor, ['left' => $leftUserIds, 'right' => $rightUserIds]);
        $consumed = $this->getConsumedVolumes($sponsor->id);

        $available = [];
        foreach ($legs as $leg => $volumes) {
            $available[$leg] = 0;
            foreach ($pool['classes'] as $class) {
                $available[$leg] += ($volumes[$class] ?? 0) - ($consumed[$leg][$class] ?? 0);
            }
        }

        $newVolume = max(0, min($available['left'], $available['right']));

        if ($pool['type'] === 'fixed') {
            /*
        |--------------------------------------------------------------------------
        | Fixed Pair Bonus
        |--------------------------------------------------------------------------
        | Flat bonus per matched pair, one pair = one package amount of
        | matched volume on both legs (e.g. Starter: ₹300 per ₹1600).
        |--------------------------------------------------------------------------
        */
            if ($pool['unit'] <= 0) {
                return;
            }

            $matchedPairs = floor($newVolume / $pool['unit']);

            if ($matchedPairs < 1) {
                return;
            }

            $volumeToFlush = $matchedPairs * $pool['unit'];
            $calculatedPairBonus = $matchedPairs * $pool['bonus'];
        } else {
            /*
        |--------------------------------------------------------------------------
        | Percentage Pair Bonus
        |--------------------------------------------------------------------------
        | Pay X% of newly matched business volume.
        |--------------------------------------------------------------------------
        */
            if ($newVolume < 1000) {
                return;
            }

            $volumeToFlush = $newVolume;
            $calculatedPairBonus = $newVolume * ($pool['bonus'] / 100);
        }

        $pairBonus = min($calculatedPairBonus, $remainingCap);

        // Flush the whole matched volume now — even if the daily cap
        // reduces (or zeroes) the actual payout, this volume is
        // considered "used" and will not be re-evaluated tomorrow.
        foreach ($legs as $leg => $volumes) {
            $this->consumeVolume($sponsor->id, $leg, $volumeToFlush, $pool, $volumes, $consumed[$leg] ?? []);
        }

        if ($pairBonus <= 0) {
            return;
        }

        $bonusType = $pool['starter_first'] ? BonusType::PairBonusStarter->value : BonusType::PairBonusNormal->value;
        $matchedVolumeForRemarks = $volumeToFlush;

        /*
    |--------------------------------------------------------------------------
    | Credit Wallet
    |--------------------------------------------------------------------------
    */
        DB::table('wallets')->where('user_id', $sponsor->id)->increment('balance', $pairBonus);

        $receiverId = $sponsor->member_id ?? $sponsor->id;

        $remarks = 'Pair Completion Bonus - ' . $packageType . ': Credited ₹' . number_format($pairBonus, 2) . ' to ' . $receiverId . ' | Matched Volume ₹' . number_format($matchedVolumeForRemarks, 2);

        DB::table('transactions')->insert([
            'user_id' => $sponsor->id,
            'type' => 'credit',
            'bonus_type' => $bonusType,
            'amount' => $pairBonus,
            'remarks' => $remarks,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Resolve Matching Pool For A Purchased Package
    |--------------------------------------------------------------------------
    | Volume is grouped by "class": the package id, except Starter Package
    | which has two — "<id>:first" (₹1600 first purchase) and "<id>:repeat"
    | (₹1000 EMI/repurchase). Pool = bought class + the classes it can pair
    | with (admin/packages). Returns the pool and the pair bonus rule.
    |--------------------------------------------------------------------------
    */
    private function resolvePairPool($package, float $amount): ?array
    {
        $isStarter = \App\Models\Package::isStarter($package);
        $part = $isStarter ? ($amount == (float) $package->actual_amount ? 'first' : 'repeat') : null;
        $ownClass = \App\Models\Package::classKey($package->id, $part);

        $pairedKeys = \App\Models\Package::pairedClassKeys($package->id, $part);
        $classes = array_values(array_unique(array_merge([$ownClass], $pairedKeys)));

        // Starter ₹1000 EMI/repurchase pays the pair rule of the first
        // package it is paired with; no pairing = no pair income.
        $rulePackage = $package;
        if ($part === 'repeat') {
            $rulePackage = null;
            foreach ($pairedKeys as $key) {
                [$id] = \App\Models\Package::parseClassKey($key);
                if ($id != $package->id) {
                    $rulePackage = DB::table('packages')->find($id);
                    break;
                }
            }
        }

        if (!$rulePackage) {
            return null;
        }

        return [
            'own' => $ownClass,
            'classes' => $classes,
            'starter_first' => $part === 'first',
            'bonus' => (float) $rulePackage->pair_bonus,
            'type' => $rulePackage->pair_bonus_type,
            'unit' => (float) ($part === 'first' ? $package->actual_amount : ($rulePackage->amount ?? $rulePackage->actual_amount)),
        ];
    }

    // Orders of a leg that count for pair income, with their class and
    // business volume. Repurchase Booster and packages of ₹50,000+ never
    // count for pair income.
    private function legOrdersQuery(array $userIds)
    {
        return DB::table('orders as o')
            ->join('packages as p', 'p.id', '=', 'o.package_id')
            ->whereIn('o.user_id', $userIds)
            ->where('o.status', 'completed')
            ->whereRaw('UPPER(TRIM(p.name)) != ?', ['REPURCHASE BOOSTER PACKAGE'])
            ->whereRaw('COALESCE(p.amount, p.actual_amount, o.amount, 0) < ?', [50000])
            ->selectRaw("
                CASE
                    WHEN UPPER(TRIM(p.name)) = 'STARTER PACKAGE'
                        THEN CONCAT(p.id, IF(o.amount = p.actual_amount, ':first', ':repeat'))
                    ELSE CAST(p.id AS CHAR)
                END as class_key,
                CASE
                    WHEN UPPER(TRIM(p.name)) = 'STARTER PACKAGE' THEN o.amount
                    ELSE COALESCE(p.amount, p.actual_amount, o.amount, 0)
                END as volume
            ");
    }

    // Business volume of a leg, per class.
    private function getLegClassVolumes(array $userIds): array
    {
        if (empty($userIds)) {
            return [];
        }

        $volumes = [];
        foreach ($this->legOrdersQuery($userIds)->get() as $row) {
            $volumes[$row->class_key] = ($volumes[$row->class_key] ?? 0) + (float) $row->volume;
        }

        return $volumes;
    }

    /*
    |--------------------------------------------------------------------------
    | Carry Over Earlier Processed Pair Volume (once per user)
    |--------------------------------------------------------------------------
    | Earlier, processed volume was stored per pool: user_pair_volumes
    | (pool_key "1,2,3,8" / "starter:1"), or before that one number for the
    | normal pool and one for Starter first purchases (users table). The
    | first time a user's pair income is evaluated, spread those amounts
    | over each leg's orders, oldest first, so paid volume is never paid
    | again.
    |--------------------------------------------------------------------------
    */
    private function seedLegacyConsumption($sponsor, array $legUserIds): void
    {
        if (DB::table('user_pair_consumptions')->where('user_id', $sponsor->id)->exists()) {
            return;
        }

        $starterId = DB::table('packages')->whereRaw('UPPER(TRIM(name)) = ?', ['STARTER PACKAGE'])->value('id');

        // Each bucket: [classes it covered, processed amount]
        $buckets = [];

        if (Schema::hasTable('user_pair_volumes')) {
            foreach (DB::table('user_pair_volumes')->where('user_id', $sponsor->id)->orderBy('id')->get() as $row) {
                if (str_starts_with($row->pool_key, 'starter:')) {
                    $classes = [substr($row->pool_key, 8) . ':first'];
                } else {
                    // Starter in these pools only ever meant its ₹1000 repurchase
                    $classes = array_map(fn($id) => $id == $starterId ? $id . ':repeat' : $id, explode(',', $row->pool_key));
                }
                $buckets[] = [$classes, (float) $row->processed_volume];
            }
        } else {
            $buckets[] = [null, (float) ($sponsor->normal_pair_processed_volume ?? 0)];
            if ($starterId) {
                $buckets[] = [[$starterId . ':first'], (float) ($sponsor->starter_pair_processed_volume ?? 0)];
            }
        }

        $buckets = array_filter($buckets, fn($b) => $b[1] > 0);

        if (!$buckets) {
            return;
        }

        $rows = [];

        foreach ($legUserIds as $leg => $userIds) {
            $orders = empty($userIds) ? collect() : $this->legOrdersQuery($userIds)->orderBy('o.id')->get();
            $free = $orders->map(fn($o) => (float) $o->volume)->all();
            $take = [];

            foreach ($buckets as [$classes, $amount]) {
                // null = the old normal pool: every class except Starter first purchase
                $inBucket = fn($class) => $classes === null ? !str_ends_with($class, ':first') : in_array($class, $classes);
                $lastClass = null;

                foreach ($orders as $i => $order) {
                    if ($amount <= 0) {
                        break;
                    }
                    if (!$inBucket($order->class_key) || $free[$i] <= 0) {
                        continue;
                    }
                    $use = min($amount, $free[$i]);
                    $free[$i] -= $use;
                    $amount -= $use;
                    $take[$order->class_key] = ($take[$order->class_key] ?? 0) + $use;
                    $lastClass = $order->class_key;
                }

                // Volume no longer present (e.g. reversed orders) stays consumed
                if ($amount > 0) {
                    $class = $lastClass ?? ($classes[0] ?? ($starterId ? $starterId . ':repeat' : null));
                    if ($class) {
                        $take[$class] = ($take[$class] ?? 0) + $amount;
                    }
                }
            }

            foreach ($take as $class => $amount) {
                $rows[] = ['user_id' => $sponsor->id, 'leg' => $leg, 'class_key' => (string) $class, 'consumed_volume' => $amount, 'created_at' => now(), 'updated_at' => now()];
            }
        }

        if ($rows) {
            DB::table('user_pair_consumptions')->insert($rows);
        }
    }

    // Volume already used for pair income: [leg][class] => amount
    private function getConsumedVolumes($userId): array
    {
        $consumed = ['left' => [], 'right' => []];

        foreach (DB::table('user_pair_consumptions')->where('user_id', $userId)->lockForUpdate()->get() as $row) {
            $consumed[$row->leg][$row->class_key] = (float) $row->consumed_volume;
        }

        return $consumed;
    }

    // Mark $amount of a leg's pool volume as used: the bought package's own
    // volume first, then the paired packages in order.
    private function consumeVolume($userId, string $leg, float $amount, array $pool, array $volumes, array $consumed): void
    {
        $order = array_values(array_unique(array_merge([$pool['own']], $pool['classes'])));
        $take = [];

        foreach ($order as $class) {
            if ($amount <= 0) {
                break;
            }
            $free = max(0, ($volumes[$class] ?? 0) - ($consumed[$class] ?? 0));
            $use = min($free, $amount);
            if ($use > 0) {
                $take[$class] = $use;
                $amount -= $use;
            }
        }

        if ($amount > 0) {
            $take[$pool['own']] = ($take[$pool['own']] ?? 0) + $amount;
        }

        foreach ($take as $class => $use) {
            DB::table('user_pair_consumptions')->upsert(
                [['user_id' => $userId, 'leg' => $leg, 'class_key' => $class, 'consumed_volume' => ($consumed[$class] ?? 0) + $use, 'created_at' => now(), 'updated_at' => now()]],
                ['user_id', 'leg', 'class_key'],
                ['consumed_volume', 'updated_at'],
            );
        }
    }

    private function getFullSubtreeUsers($rootId, $side)
    {
        $result = [];

        if (!$rootId || !in_array($side, ['left', 'right'])) {
            return [];
        }

        $start = DB::table('users')->where('placement_id', $rootId)->where('position', $side)->first();

        if (!$start) {
            return [];
        }

        $queue = [$start];
        $visited = [];

        while (!empty($queue)) {
            $node = array_shift($queue);

            if (!$node || in_array($node->id, $visited)) {
                continue;
            }

            $visited[] = $node->id;
            $result[] = $node;

            $children = DB::table('users')->where('placement_id', $node->id)->get();

            foreach ($children as $child) {
                if (!in_array($child->id, $visited)) {
                    $queue[] = $child;
                }
            }
        }

        return $result;
    }

    private function pickExtremeAtDepth($users, $depth, $extreme = 'leftmost')
    {
        $filtered = array_values(
            array_filter($users, function ($u) use ($depth) {
                return $u['depth'] === $depth;
            }),
        );

        if (empty($filtered)) {
            return null;
        }

        // leftmost = first in BFS order
        if ($extreme === 'leftmost') {
            return $filtered[0];
        }

        // rightmost = last in BFS order
        return $filtered[count($filtered) - 1];
    }

    private function getLegUsersByDepth($rootUserId, $side)
    {
        $result = [];

        // start from direct child on that side
        $start = DB::table('users')->where('placement_id', $rootUserId)->where('position', $side)->first();

        if (!$start) {
            return [];
        }

        $queue = [[$start, 1]]; // [userObj, depth]

        while (!empty($queue)) {
            [$node, $depth] = array_shift($queue);

            $result[] = [
                'id' => $node->id,
                'depth' => $depth,
                'username' => $node->username ?? '',
                'investment_count' => (int) ($node->investment_count ?? 0),
            ];

            // children of this node (left then right)
            $leftChild = DB::table('users')->where('placement_id', $node->id)->where('position', 'left')->first();

            $rightChild = DB::table('users')->where('placement_id', $node->id)->where('position', 'right')->first();

            if ($leftChild) {
                $queue[] = [$leftChild, $depth + 1];
            }
            if ($rightChild) {
                $queue[] = [$rightChild, $depth + 1];
            }
        }

        return $result;
    }

    private function distributeCommission($userId, $amount, $package)
    {
        $user = DB::table('users')->find($userId);

        if (!$user) {
            return;
        }

        $amount = (float) $amount;

        /*
    |--------------------------------------------------------------------------
    | Direct Commission For Normal Packages Below 50,000
    |--------------------------------------------------------------------------
    | Uses the package's direct bonus: % of amount, or a fixed amount.
    |--------------------------------------------------------------------------
    */
        if ($amount < 50000) {
            $directBonus = (float) ($package->direct_bonus ?? 0);
            $label = \App\Models\Package::formatBonus($directBonus, $package->direct_bonus_type);

            if ($package->direct_bonus_type === 'fixed') {
                $commission = $directBonus;
                $remarks = "Flat {$label} Direct Commission ({$package->name}) from {$user->username}";
            } else {
                $commission = $amount * ($directBonus / 100);
                $remarks = "{$label} Direct Commission from {$user->username}";
            }

            if ($commission > 0 && !empty($user->sponsor_id)) {
                $this->distributeCommissionDBOpr($user->sponsor_id, $commission, $remarks, $user, $amount);
            }

            return;
        }

        /*
    |--------------------------------------------------------------------------
    | Level Commission For Packages 50,000 And Above
    |--------------------------------------------------------------------------
    */
        $currentUserId = $user->sponsor_id;
        $level = 1;

        $levelPercentages = [
            1 => 0.05,
            2 => 0.01,
            3 => 0.01,
            4 => 0.0075,
            5 => 0.0075,
            6 => 0.005,
            7 => 0.0025,
            8 => 0.0025,
            9 => 0.0025,
            10 => 0.0025,
        ];

        while ($currentUserId && $level <= 10) {
            $sponsor = DB::table('users')->where('id', $currentUserId)->first();

            if (!$sponsor) {
                break;
            }

            $percentage = $levelPercentages[$level] ?? 0;

            if ($percentage > 0) {
                $levelCommission = $amount * $percentage;

                $remarks = "L{$level} Commission (" . $percentage * 100 . "%) from {$user->username}";

                $this->distributeCommissionDBOpr($sponsor->id, $levelCommission, $remarks, $user, $amount, $level);
            }

            $currentUserId = $sponsor->sponsor_id;
            $level++;
        }

        /*
    |--------------------------------------------------------------------------
    | Indirect Commission For Packages 50,000 And Above
    |--------------------------------------------------------------------------
    | Kept as your existing logic. This is separate from normal pair income.
    |--------------------------------------------------------------------------
    */
        $current = $user;
        $idxLevel = 1;

        while ($current && $idxLevel <= 10) {
            $binaryNode = DB::table('binary_nodes')->where('user_id', $current->id)->first();

            if (!$binaryNode || !$binaryNode->parent_id) {
                break;
            }

            $upline = DB::table('users')->find($binaryNode->parent_id);

            if (!$upline) {
                break;
            }

            $indirectCommission = $amount * 0.05;

            $remarks = "Indirect 5% Commission from {$user->username} - Level {$idxLevel}";

            $this->distributeCommissionDBOpr($upline->id, $indirectCommission, $remarks, $user, $amount);

            $current = $upline;
            $idxLevel++;
        }
    }
    private function distributeCommissionDBOpr($targetUserId, $commissionAmount, $remarks, $fromUser, $totalAmount, $lvl = null)
    {
        DB::transaction(function () use ($targetUserId, $commissionAmount, $remarks, $fromUser, $totalAmount) {
            // 1. Update/Insert Wallet
            DB::table('wallets')->updateOrInsert(['user_id' => $targetUserId], ['updated_at' => now()]);

            DB::table('wallets')->where('user_id', $targetUserId)->increment('balance', $commissionAmount);
            $bonusType = BonusType::DirectIncome->value;
            // 2. Insert Transaction Record
            DB::table('transactions')->insert([
                'user_id' => $targetUserId,
                'type' => 'Credit',
                'bonus_type' => $bonusType,
                'amount' => $commissionAmount,
                'remarks' => $remarks . ' (₹' . number_format($totalAmount) . ')',
                'created_at' => now(),
            ]);
        });
    }

    private static function rewardAfterFullEmi($user)
    {
        $rewardAmount = 5000; // or calculate dynamically

        // Credit reward to wallet
        DB::table('wallets')->where('user_id', $user->id)->increment('balance', $rewardAmount);
        $bonusType = BonusType::RewardAfterFullEmi->value;

        // Record credit transaction
        DB::table('transactions')->insert([
            'user_id' => $user->id,
            'type' => 'Credit',
            'bonus_type' => $bonusType,
            'amount' => $rewardAmount,
            'remarks' => 'Reward for completing all 16 EMIs',
            'created_at' => now(),
        ]);

        // Update EMI status
        DB::table('users')
            ->where('id', $user->id)
            ->update([
                'emi_status' => 'completed',
                'updated_at' => now(),
            ]);
    }

    private static function checkAndDistributePairBonus($user)
    {
        // 1️⃣ Get the parent (placement/sponsor)
        $parent = DB::table('users')->where('id', $user->placement_id)->first();
        if (!$parent) {
            return;
        }

        // 2️⃣ Get both left and right child of parent
        $leftChild = DB::table('users')->where('placement_id', $parent->id)->where('position', 'left')->first();
        $rightChild = DB::table('users')->where('placement_id', $parent->id)->where('position', 'right')->first();

        // 3️⃣ Only continue if both children exist
        if (!$leftChild || !$rightChild) {
            return;
        }

        // 4️⃣ Check condition (both reach 3 or more)
        if ($leftChild->investment_count >= 3 && $rightChild->investment_count >= 3) {
            // Check if this bonus already given
            $already = DB::table('transactions')
                ->where([
                    'user_id' => $parent->id,
                    'remarks' => 'Pair Bonus from ' . $leftChild->username . ' & ' . $rightChild->username,
                ])
                ->first();

            if (!$already) {
                // ✅ Add pair bonus
                $bonusAmount = 1000;
                DB::table('wallets')->where('user_id', $parent->id)->increment('balance', $bonusAmount);
                $bonusType = BonusType::PairBonus->value;
                DB::table('transactions')->insert([
                    'user_id' => $parent->id,
                    'type' => 'Credit',
                    'bonus_type' => $bonusType,
                    'amount' => $bonusAmount,
                    'remarks' => 'Pair Bonus from ' . $leftChild->username . ' & ' . $rightChild->username,
                    'created_at' => now(),
                ]);
            }
        }

        // 🔁 Recursive check for next level
        self::checkSubPairBonus($parent);
    }

    private static function checkSubPairBonus($user)
    {
        // Get user’s left & right children
        $leftChild = DB::table('users')->where('placement_id', $user->id)->where('position', 'left')->first();
        $rightChild = DB::table('users')->where('placement_id', $user->id)->where('position', 'right')->first();

        if (!$leftChild || !$rightChild) {
            return;
        }

        // Get left branch sub-children (second level)
        $leftGrand = DB::table('users')->where('placement_id', $leftChild->id)->get();
        // Get right branch sub-children
        $rightGrand = DB::table('users')->where('placement_id', $rightChild->id)->get();

        $eligibleSubs = [];

        foreach ($leftGrand as $lg) {
            if ($lg->investment_count >= 3) {
                $eligibleSubs[] = $lg;
            }
        }

        foreach ($rightGrand as $rg) {
            if ($rg->investment_count >= 3) {
                $eligibleSubs[] = $rg;
            }
        }

        if (count($eligibleSubs) >= 4) {
            // D,E,F,G case

            $already = DB::table('transactions')
                ->where([
                    'user_id' => $user->id,
                    'remarks' => 'Sub-Pair Bonus from second-level children',
                ])
                ->first();

            if (!$already) {
                $bonusAmount = 400; // 500 * 4
                DB::table('wallets')->where('user_id', $user->id)->increment('balance', $bonusAmount);

                DB::table('transactions')->insert([
                    'user_id' => $user->id,
                    'type' => 'Credit',
                    'amount' => $bonusAmount,
                    'remarks' => 'Sub-Pair Bonus from second-level children',
                    'created_at' => now(),
                ]);
            }
        }
    }

    public function storeeess(Request $r)
    {
        $r->validate([
            'email' => 'required|email|exists:users,email',
            'package_id' => 'required|integer|exists:packages,id',
            'payment_by' => 'required|string',
        ]);

        $currentUser = Auth::user();
        $wallet = DB::table('wallets')->where('user_id', $currentUser->id)->first();
        $package = DB::table('packages')->where('id', $r->package_id)->first();
        $receiver = DB::table('users')->where('email', $r->email)->first();

        if (!$wallet || $wallet->balance < $package->amount) {
            return back()->with('error', 'Insufficient wallet balance to perform this top-up.');
        }

        DB::beginTransaction();
        try {
            // ✅ 1. Deduct from wallet
            DB::table('wallets')
                ->where('user_id', $currentUser->id)
                ->update([
                    'balance' => $wallet->balance - $package->amount,
                    'updated_at' => now(),
                ]);

            // ✅ 2. Record wallet transaction (debit)
            DB::table('transactions')->insert([
                'user_id' => $currentUser->id,
                'type' => 'Debit',
                'amount' => $package->amount,
                'remarks' => 'Top-up for ' . $receiver->username . ' (' . $receiver->email . ')',
                'created_at' => now(),
            ]);

            // ✅ 3. Record the top-up order
            $orderId = DB::table('orders')->insertGetId([
                'user_id' => $receiver->id,
                'from_user_id' => $currentUser->id,
                'package_id' => $package->id,
                'amount' => $package->amount,
                'payment_by' => $r->payment_by,
                'status' => 'completed',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // ✅ 4. Update receiver's investment count
            DB::table('users')
                ->where('id', $receiver->id)
                ->increment('investment_count', 1, ['updated_at' => now()]);

            // ✅ 5. Generate 16 EMIs for this order
            $installmentAmount = $package->amount / 16;
            $startDate = now();

            for ($i = 1; $i <= 16; $i++) {
                DB::table('emis')->insert([
                    'user_id' => $receiver->id,
                    'order_id' => $orderId,
                    'emi_number' => $i,
                    'amount' => $installmentAmount,
                    'due_date' => $startDate->copy()->addMonths($i),
                    'status' => 'Pending',
                    'created_at' => now(),
                ]);
            }

            // ✅ 6. Direct Bonus (Sponsor)
            $sponsor = DB::table('users')->where('id', $receiver->sponsor_id)->first();
            if ($sponsor) {
                $directBonus = ($package->amount * ($package->direct_bonus ?? 10)) / 100;

                // Add to sponsor wallet
                DB::table('wallets')->where('user_id', $sponsor->id)->increment('balance', $directBonus);

                // Transaction record
                DB::table('transactions')->insert([
                    'user_id' => $sponsor->id,
                    'type' => 'Credit',
                    'amount' => $directBonus,
                    'remarks' => 'Direct bonus from ' . $receiver->username,
                    'created_at' => now(),
                ]);
            }

            // ✅ 7. Binary Pair Bonus Propagation (optional)
            self::distributeBinaryBonus($receiver, $package->pv ?? 10);

            DB::commit();

            return back()->with('success', "Top-up successful! ₹{$package->amount} has been deducted and EMIs generated for {$receiver->username}.");
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Something went wrong: ' . $e->getMessage());
        }
    }

    private static function distributeBinaryBonus($activatedUser, $pv)
    {
        $current = $activatedUser;

        while ($current->placement_id) {
            $parent = DB::table('users')->find($current->placement_id);
            if (!$parent) {
                break;
            }

            $side = $current->position; // 'left' or 'right'

            DB::table('binary_nodes')->updateOrInsert(
                ['user_id' => $parent->id],
                [
                    $side . '_pv' => DB::raw($side . '_pv + ' . $pv),
                    'updated_at' => now(),
                ],
            );

            $node = DB::table('binary_nodes')->where('user_id', $parent->id)->first();
            $matchedPV = min($node->left_pv, $node->right_pv);

            if ($matchedPV > 0) {
                $pairBonus = ($matchedPV * ($node->pair_bonus ?? 5)) / 100;

                DB::table('wallets')->where('user_id', $parent->id)->increment('balance', $pairBonus);

                DB::table('transactions')->insert([
                    'user_id' => $parent->id,
                    'type' => 'Credit',
                    'amount' => $pairBonus,
                    'remarks' => 'Pair bonus from matching PV',
                    'created_at' => now(),
                ]);

                DB::table('binary_nodes')
                    ->where('user_id', $parent->id)
                    ->update([
                        'left_pv' => $node->left_pv - $matchedPV,
                        'right_pv' => $node->right_pv - $matchedPV,
                    ]);
            }

            $current = $parent;
        }
    }

    public function storesssss(Request $r)
    {
        $r->validate([
            'email' => 'required|email|exists:users,email',
            'package_id' => 'required|integer',
            'payment_by' => 'required|string',
        ]);

        $currentUser = Auth::user();
        $wallet = DB::table('wallets')->where('user_id', $currentUser->id)->first();
        $package = DB::table('packages')->where('id', $r->package_id)->first();
        $receiver = DB::table('users')->where('email', $r->email)->first();

        if (!$wallet || $wallet->balance < $package->amount) {
            return back()->with('error', 'Insufficient wallet balance to perform this top-up.');
        }

        // Deduct from wallet
        DB::table('wallets')
            ->where('user_id', $currentUser->id)
            ->update([
                'balance' => $wallet->balance - $package->amount,
                'updated_at' => now(),
            ]);

        // Record the topup transaction
        DB::table('orders')->insert([
            'user_id' => $receiver->id,
            'from_user_id' => $currentUser->id, // ✅ Added
            'package_id' => $package->id,
            'amount' => $package->amount,
            'payment_by' => $r->payment_by,
            'status' => 'completed',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // ✅ Update investment count for the receiver
        $currentCount = DB::table('users')->where('id', $receiver->id)->value('investment_count') ?? 0;

        DB::table('users')
            ->where('id', $receiver->id)
            ->update([
                'investment_count' => $currentCount + 1,
                'updated_at' => now(),
            ]);

        return back()->with('success', "Top-up successful! ₹{$package->amount} has been deducted from your wallet.");
    }
}