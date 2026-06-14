<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Enums\BonusType;
class TopupController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $packages = DB::table('packages')->get();
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
        // dd($r->all());
        // 1. Validate using member_id instead of email
        $r->validate([
            'member_id' => 'required|string|exists:users,member_id',
            'package_id' => 'required|integer|exists:packages,id',
            'payment_by' => 'required|string',
        ]);

        $currentUser = Auth::user();
        $memberId = strtoupper(trim($r->member_id));

        // 2. Fetch data using member_id
        $receiver = \App\Models\User::where('member_id', $memberId)->first();
        $package = DB::table('packages')->where('id', $r->package_id)->first();
        // if ($package->amount == 2000) {
        //         $matrixService = new \App\Services\MatrixService();
        //         $matrixService->processCommission($currentUser);
        //     }
        // die;
        // dd($package);
        $wallet = DB::table('wallets')->where('user_id', $currentUser->id)->first();

        if (!$receiver) {
            return back()->with('error', 'Member not found.');
        }

        // 3. INTERNAL FINAL AMOUNT CALCULATION
        $isFirstPurchase = !\App\Models\Order::where('user_id', $receiver->id)->exists();
        // dd($isFirstPurchase);

        $finalAmount = $isFirstPurchase ? $package->discounted_amount ?? $package->actual_amount : $package->actual_amount;
        // dd($finalAmount);

        $currentCount = $receiver->investment_count ?? 0;
        $registrationFee = $currentCount == 0 ? 100 : 0;
        $finalAmount = (float) $finalAmount + $registrationFee;

        // 4. Calculate increment value based on package ID
        // We keep this to track total investment progress, but we no longer block based on it
        $packageId = (int) $r->package_id;
        $incrementValue = match ($packageId) {
            1 => 1,
            2 => 8,
            3 => 16,
            default => 1,
        };

        $newTotal = $currentCount + $incrementValue;

        // ✅ Wallet balance validation
        if (!$wallet || $wallet->balance < $finalAmount) {
            return back()->with('error', "Insufficient wallet balance. Need ₹{$finalAmount} to perform this top-up.");
        }

        // ✅ Lucky Service logic for specialized packages
        if (in_array($packageId, [4, 5])) {
            \App\Services\LuckyService::createCycleIfNotExists($receiver->id, $packageId);
        }

        DB::beginTransaction();
        try {
            // ✅ 4. Deduct wallet balance
            DB::table('wallets')
                ->where('user_id', $currentUser->id)
                ->update([
                    'balance' => $wallet->balance - $finalAmount,
                    'updated_at' => now(),
                ]);
            // dd($wallet->balance, $finalAmount);
            $bonusType = BonusType::EmiPayment->value;
            // ✅ 5. Record debit transaction
            DB::table('transactions')->insert([
                'user_id' => $currentUser->id,
                'type' => 'Debit',
                'amount' => $finalAmount,
                'bonus_type' => $bonusType,
                'remarks' => 'EMI payment for ' . $receiver->username . " ({$memberId})",
                'created_at' => now(),
            ]);
            // dd('Transaction recorded with bonus type: ' . $bonusType);

            // ✅ 6. Record order
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

            // ✅ 7. Update investment_count and status
            // We set status to completed ONLY if they hit 16 or more, but we don't block them if they go over.
            DB::table('users')
                ->where('id', $receiver->id)
                ->update([
                    'investment_count' => $newTotal,
                    'emi_status' => $newTotal >= 16 ? 'completed' : 'ongoing',
                    'updated_at' => now(),
                ]);

            // ✅ 8. Trigger pair bonus check for ALL uplines
            // (ONLY for standard packages < 50,000)
            $sponsor = DB::table('users')->where('id', $receiver->placement_id)->first();
            $emisData = \DB::table('orders')->where('user_id', $sponsor->id)->where('status', 'completed')->selectRaw('MIN(created_at) as activation_date, COUNT(*) as total_emis_paid')->first();
            // dd($emisData);
            // if (!$emisData || !$emisData->activation_date) {
            //     return;
            // }

            $activationDate = \Carbon\Carbon::parse($emisData->activation_date);

            $activationMonth = $activationDate->year * 12 + $activationDate->month;
            $currentMonth = now()->year * 12 + now()->month;

            $totalEmisSupposedToPay = $currentMonth - $activationMonth + 1;

            $totalEmisPaid = $emisData->total_emis_paid ?? 0;
            // dd($activationDate, $totalEmisPaid, $totalEmisSupposedToPay);
            // if ($totalEmisPaid >= $totalEmisSupposedToPay) {
            if ($package->amount < 50000) {
                // $sponsor = DB::table('users')->where('id', $receiver->placement_id)->first();
                while ($sponsor) {
                    if (method_exists($this, 'checkAndDistributePairCompletionBonus')) {
                        // dd($sponsor, $package->amount);
                        $this->checkAndDistributePairCompletionBonus($sponsor, $package->amount);
                    }
                    if (empty($sponsor->placement_id)) {
                        break;
                    }
                    $sponsor = DB::table('users')->where('id', $sponsor->placement_id)->first();
                }
            }
            // ✅ 9. Reward logic (unchanged)
            if ($newTotal >= 16 && method_exists($this, 'rewardAfterFullEmi')) {
                $this->rewardAfterFullEmi($receiver);
            }

            // ✅ 10. Distribute Commissions (Only for first time investment)
            if ($currentCount == 0) {
                if (method_exists($this, 'distributeCommission')) {
                    $this->distributeCommission($receiver->id, $package->amount);
                }
            }
            // }

            DB::commit();

            $successMessage = match ($packageId) {
                4, 5 => "Congratulations! You have successfully paid ₹{$finalAmount} for Member {$memberId}. Vouchers have been issued.",
                default => "Top-up successful! ₹{$finalAmount} deducted from your wallet for Member {$memberId}.",
            };
            if ($package->amount == 2000) {
                $matrixService = new \App\Services\MatrixService();
                // $matrixService->processCommission($currentUser);
                $matrixService->processCommission($receiver, $currentUser);
            }
            return back()->with('success', $successMessage);
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Something went wrong: ' . $e->getMessage());
        }
    }

    // private function checkAndDistributePairCompletionBonus($sponsor, $amount)
    // {
    //     // dd($sponsor, $amount);
    //     // 1. STRICT CAP: No pair income for packages 50k and above
    //     if (!$sponsor || $amount >= 50000) {
    //         return;
    //     }
    //     $leftUsers = $this->getFullSubtreeUsers($sponsor->id, 'left');
    //     $rightUsers = $this->getFullSubtreeUsers($sponsor->id, 'right');

    //     if (empty($leftUsers) || empty($rightUsers)) {
    //         return;
    //     }

    //     $leftUserIds = collect($leftUsers)->pluck('id')->toArray();
    //     $rightUserIds = collect($rightUsers)->pluck('id')->toArray();

    //     // 2. Calculate Pure Investment Volume (Excluding Reg Fees)
    //     $leftUniqueInvestors = DB::table('orders')->whereIn('user_id', $leftUserIds)->where('status', 'completed')->distinct('user_id')->count();

    //     $leftTotalVolume = DB::table('orders')->whereIn('user_id', $leftUserIds)->where('status', 'completed')->sum('amount') - $leftUniqueInvestors * 100;

    //     $rightUniqueInvestors = DB::table('orders')->whereIn('user_id', $rightUserIds)->where('status', 'completed')->distinct('user_id')->count();

    //     $rightTotalVolume = DB::table('orders')->whereIn('user_id', $rightUserIds)->where('status', 'completed')->sum('amount') - $rightUniqueInvestors * 100;

    //     // 3. Lifetime matchable ceiling
    //     $currentMaxMatch = min($leftTotalVolume, $rightTotalVolume);

    //     // 4. Already paid volume calculation
    //     $totalPaidBonus = DB::table('transactions')->where('user_id', $sponsor->id)->where('remarks', 'like', 'Pair Completion Bonus%')->sum('amount');

    //     $alreadyMatchedVolume = $totalPaidBonus * 10;

    //     $newVolumeToPay = $currentMaxMatch - $alreadyMatchedVolume;

    //     if ($newVolumeToPay < 1000) {
    //         return;
    //     }

    //     // 5. Bonus percentage logic
    //     $binaryBonusPercentage = $amount == 16000 ? 20 : 10;

    //     $pairBonus = $newVolumeToPay * ($binaryBonusPercentage / 100);

    //     // ================================
    //     // 🔥 DAILY CAP LOGIC (₹5000/day)
    //     // ================================

    //     $todayPairIncome = DB::table('transactions')
    //         ->where('user_id', $sponsor->id)
    //         ->where('remarks', 'like', 'Pair Completion Bonus%')
    //         ->whereDate('created_at', now()->toDateString())
    //         ->sum('amount');

    //     $dailyCap = 5000;
    //     $remainingCap = $dailyCap - $todayPairIncome;

    //     if ($remainingCap <= 0) {
    //         return;
    //     }

    //     // apply cap
    //     $pairBonus = min($pairBonus, $remainingCap);

    //     if ($pairBonus <= 0) {
    //         return;
    //     }

    //     // 6. Final payout
    //     DB::transaction(function () use ($sponsor, $pairBonus, $newVolumeToPay) {
    //         DB::table('wallets')->where('user_id', $sponsor->id)->increment('balance', $pairBonus);

    //         DB::table('transactions')->insert([
    //             'user_id' => $sponsor->id,
    //             'type' => 'credit',
    //             'amount' => $pairBonus,
    //             'remarks' => 'Pair Completion Bonus: Matched ₹' . number_format($newVolumeToPay) . ' volume (DAILY CAPPED)',
    //             'created_at' => now(),
    //             'updated_at' => now(),
    //         ]);
    //     });
    // }
    private function checkAndDistributePairCompletionBonus($sponsor, $amount)
    {
        // dd($sponsor, $amount);
        // 1. STRICT CAP: No pair income for packages 50k and above
        if (!$sponsor || $amount >= 50000) {
            return;
        }

        $leftUsers = $this->getFullSubtreeUsers($sponsor->id, 'left');
        $rightUsers = $this->getFullSubtreeUsers($sponsor->id, 'right');

        if (empty($leftUsers) || empty($rightUsers)) {
            return;
        }

        $leftUserIds = collect($leftUsers)->pluck('id')->toArray();
        $rightUserIds = collect($rightUsers)->pluck('id')->toArray();

        $isSpecial2000Package = (int) $amount === 2000;

        $bonusType = $isSpecial2000Package ? BonusType::PairBonus2000->value : BonusType::PairBonusNormal->value;

        // 2. Calculate Pure Investment Volume
        if ($isSpecial2000Package) {
            /*
             * SPECIAL ₹2000 PACKAGE RULE:
             * ₹2000 package matches ONLY with ₹2000 package.
             * ₹1000 + ₹1000 will NOT match with ₹2000.
             */

            $leftTotalVolume = DB::table('orders')->whereIn('user_id', $leftUserIds)->where('status', 'completed')->where('amount', 2000)->sum('amount');

            $rightTotalVolume = DB::table('orders')->whereIn('user_id', $rightUserIds)->where('status', 'completed')->where('amount', 2000)->sum('amount');
        } else {
            /*
             * NORMAL PACKAGE RULE:
             * Normal packages should NOT include ₹2000 package volume.
             */

            $leftUniqueInvestors = DB::table('orders')->whereIn('user_id', $leftUserIds)->where('status', 'completed')->where('amount', '!=', 2000)->distinct('user_id')->count();

            $leftTotalVolume = DB::table('orders')->whereIn('user_id', $leftUserIds)->where('status', 'completed')->where('amount', '!=', 2000)->sum('amount') - $leftUniqueInvestors * 100;

            $rightUniqueInvestors = DB::table('orders')->whereIn('user_id', $rightUserIds)->where('status', 'completed')->where('amount', '!=', 2000)->distinct('user_id')->count();

            $rightTotalVolume = DB::table('orders')->whereIn('user_id', $rightUserIds)->where('status', 'completed')->where('amount', '!=', 2000)->sum('amount') - $rightUniqueInvestors * 100;
        }

        // 3. Lifetime matchable ceiling
        $currentMaxMatch = min($leftTotalVolume, $rightTotalVolume);

        // 4. Already paid volume calculation using bonus_type
        $totalPaidBonus = DB::table('transactions')->where('user_id', $sponsor->id)->where('bonus_type', $bonusType)->sum('amount');

        $alreadyMatchedVolume = $totalPaidBonus * 10;

        $newVolumeToPay = $currentMaxMatch - $alreadyMatchedVolume;

        if ($newVolumeToPay < 1000) {
            return;
        }

        // 5. Bonus percentage logic
        // ₹2000 package also gives 10%
        $binaryBonusPercentage = $amount == 16000 ? 20 : 10;

        $pairBonus = $newVolumeToPay * ($binaryBonusPercentage / 100);

        // ================================
        // DAILY CAP LOGIC (₹5000/day)
        // ================================

        $todayPairIncome = DB::table('transactions')
            ->where('user_id', $sponsor->id)
            ->whereIn('bonus_type', [BonusType::PairBonusNormal->value, BonusType::PairBonus2000->value])
            ->whereDate('created_at', now()->toDateString())
            ->sum('amount');

        $dailyCap = 5000;
        $remainingCap = $dailyCap - $todayPairIncome;

        if ($remainingCap <= 0) {
            return;
        }

        // Apply cap
        $pairBonus = min($pairBonus, $remainingCap);

        if ($pairBonus <= 0) {
            return;
        }

        // 6. Final payout - ONLY ONCE
        DB::transaction(function () use ($sponsor, $pairBonus, $newVolumeToPay, $isSpecial2000Package, $bonusType, $binaryBonusPercentage, $leftTotalVolume, $rightTotalVolume, $dailyCap) {
            DB::table('wallets')->where('user_id', $sponsor->id)->increment('balance', $pairBonus);

            $receiverId = $sponsor->member_id ?? $sponsor->id;

            $packageType = $isSpecial2000Package ? '₹2000 Special Package' : 'Normal Package';

            $remarks = 'Pair Completion Bonus - ' . $packageType . ': Credited ₹' . number_format($pairBonus, 2) . ' to ' . $receiverId . ' | Matched Volume ₹' . number_format($newVolumeToPay, 2) . ' | Bonus Rate ' . $binaryBonusPercentage . '%' . ' | Left Volume ₹' . number_format($leftTotalVolume, 2) . ' | Right Volume ₹' . number_format($rightTotalVolume, 2) . ' | Daily Cap ₹' . number_format($dailyCap, 2);

            DB::table('transactions')->insert([
                'user_id' => $sponsor->id,
                'type' => 'credit',
                'bonus_type' => $bonusType,
                'amount' => $pairBonus,
                'remarks' => $remarks,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        });
    }

    // private function checkAndDistributePairCompletionBonus($sponsor, $amount)
    // {
    //     // 1. STRICT CAP: No pair income for packages 50k and above
    //     if (!$sponsor || $amount >= 50000) {
    //         return;
    //     }

    //     $leftUsers = $this->getFullSubtreeUsers($sponsor->id, 'left');
    //     $rightUsers = $this->getFullSubtreeUsers($sponsor->id, 'right');

    //     if (empty($leftUsers) || empty($rightUsers)) {
    //         return;
    //     }

    //     $leftUserIds = collect($leftUsers)->pluck('id')->toArray();
    //     $rightUserIds = collect($rightUsers)->pluck('id')->toArray();

    //     // 2. Calculate Pure Investment Volume (Excluding Reg Fees)
    //     // We count unique users because each user pays the 100 registration fee only once
    //     $leftUniqueInvestors = DB::table('orders')->whereIn('user_id', $leftUserIds)->where('status', 'completed')->distinct('user_id')->count();

    //     $leftTotalVolume = DB::table('orders')->whereIn('user_id', $leftUserIds)->where('status', 'completed')->sum('amount') - $leftUniqueInvestors * 100;

    //     $rightUniqueInvestors = DB::table('orders')->whereIn('user_id', $rightUserIds)->where('status', 'completed')->distinct('user_id')->count();
    //     $rightTotalVolume = DB::table('orders')->whereIn('user_id', $rightUserIds)->where('status', 'completed')->sum('amount') - $rightUniqueInvestors * 100;

    //     // 3. Identify the lifetime matchable ceiling
    //     $currentMaxMatch = min($leftTotalVolume, $rightTotalVolume);

    //     // 4. Calculate exactly how much volume was already paid for
    //     // Reverse calculation: if they got 100 bonus, they matched 1000 volume
    //     $totalPaidBonus = DB::table('transactions')->where('user_id', $sponsor->id)->where('remarks', 'like', 'Pair Completion Bonus%')->sum('amount');

    //     $alreadyMatchedVolume = $totalPaidBonus * 10;

    //     // 5. The difference is the new volume eligible for payout
    //     $newVolumeToPay = $currentMaxMatch - $alreadyMatchedVolume;

    //     // Set a threshold (e.g., ₹1000) to trigger payout
    //     if ($newVolumeToPay >= 1000) {
    //         $binaryBonusPercentage = 10;
    //         if ($amount == 16000) {
    //             $binaryBonusPercentage = 20; // 20% for smaller packages
    //         }
    //         $pairBonus = $newVolumeToPay * ($binaryBonusPercentage / 100); // 10%

    //         DB::transaction(function () use ($sponsor, $pairBonus, $newVolumeToPay) {
    //             DB::table('wallets')->where('user_id', $sponsor->id)->increment('balance', $pairBonus);

    //             DB::table('transactions')->insert([
    //                 'user_id' => $sponsor->id,
    //                 'type' => 'Credit',
    //                 'amount' => $pairBonus,
    //                 'remarks' => 'Pair Completion Bonus: Matched ₹' . number_format($newVolumeToPay) . ' volume (10% Bonus)',
    //                 'created_at' => now(),
    //             ]);
    //         });
    //     }
    // }

    private function getFullSubtreeUsers($rootId, $side)
    {
        $result = [];

        $start = DB::table('users')->where('placement_id', $rootId)->where('position', $side)->first();

        if (!$start) {
            return [];
        }

        $queue = [$start];

        while (!empty($queue)) {
            $node = array_shift($queue);

            $result[] = $node;

            $children = DB::table('users')->where('placement_id', $node->id)->get();

            foreach ($children as $child) {
                $queue[] = $child;
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

    private function distributeCommission($userId, $amount)
    {
        $user = DB::table('users')->find($userId);
        if (!$user) {
            return;
        }

        if ($amount < 50000) {
            // --- DIRECT COMMISSION (10%) ---
            $commPercentage = 10; // 10%

            // if ($amount  == 2000 || $amount == 16000) {
            //     $commPercentage = 20; // 5% for smaller packages
            // }
            $commission = $amount * ($commPercentage / 100);
            if ($user->sponsor_id) {
                $this->distributeCommissionDBOpr($user->sponsor_id, $commission, "{$commPercentage}% Direct Commission from {$user->username}", $user, $amount);
            }
        } else {
            // --- MULTI-LEVEL & INDIRECT (For 50k+ Packages) ---

            // 1. Level Commission (Sponsor Chain)
            $currentUserId = $user->sponsor_id;
            $level = 1;
            $levelPercentages = [1 => 0.05, 2 => 0.01, 3 => 0.01, 4 => 0.0075, 5 => 0.0075, 6 => 0.005, 7 => 0.0025, 8 => 0.0025, 9 => 0.0025, 10 => 0.0025];

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
                    // Note: Pair bonus call removed from here to prevent double-logic
                }
                $currentUserId = $sponsor->sponsor_id;
                $level++;
            }

            // 2. Indirect Commission (Binary Chain)
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
