<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

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

    // public function store(Request $r)
    // {
    //     $r->validate([
    //         'email' => 'required|email|exists:users,email',
    //         'package_id' => 'required|integer|exists:packages,id',
    //         'payment_by' => 'required|string',
    //         'final_amount' => 'required|numeric|min:1',
    //     ]);

    //     $currentUser = Auth::user();
    //     $wallet = DB::table('wallets')->where('user_id', $currentUser->id)->first();
    //     $package = DB::table('packages')->where('id', $r->package_id)->first();
    //     $receiver = DB::table('users')->where('email', $r->email)->first();

    //     // ✅ Use the final_amount from form (includes registration fee if applicable)
    //     $finalAmount = floatval($r->final_amount);

    //     // ✅ Calculate increment value based on package_id
    //     $packageId = (int) $r->package_id;
    //     $incrementValue = match ($packageId) {
    //         1 => 1,
    //         2 => 8,
    //         3 => 16,
    //         default => 0,
    //     };

    //     $newCount = ($receiver->investment_count ?? 0) + $incrementValue;

    //     // ✅ 1. Check EMI/Investment limit (max 16) - AFTER calculating new count
    //     if ($receiver->emi_status === 'completed' || $newCount > 16) {
    //         $remaining = max(0, 16 - ($receiver->investment_count ?? 0));
    //         return back()->with('error', "Cannot exceed 16 EMIs limit. You can only add {$remaining} more EMI(s). This package adds {$incrementValue} EMI(s).");
    //     }

    //     // ✅ 2. Check if an EMI has already been paid this month
    //     $lastTopup = DB::table('orders')->where('user_id', $receiver->id)->orderByDesc('created_at')->first();

    //     // if ($lastTopup && \Carbon\Carbon::parse($lastTopup->created_at)->isSameMonth(now())) {
    //     //     return back()->with('error', 'Only one EMI/top-up is allowed per month.');
    //     // }

    //     // ✅ 3. Wallet balance validation
    //     if (!$wallet || $wallet->balance < $finalAmount) {
    //         return back()->with('error', 'Insufficient wallet balance to perform this top-up.');
    //     }

    //     if (in_array($packageId, [4, 5])) {
    //         \App\Services\LuckyService::createCycleIfNotExists($receiver->id, $packageId);
    //     }

    //     DB::beginTransaction();
    //     try {
    //         // ✅ 4. Deduct wallet balance (using final amount with registration fee)
    //         DB::table('wallets')
    //             ->where('user_id', $currentUser->id)
    //             ->update([
    //                 'balance' => $wallet->balance - $finalAmount,
    //                 'updated_at' => now(),
    //             ]);

    //         // ✅ 5. Record debit transaction
    //         DB::table('transactions')->insert([
    //             'user_id' => $currentUser->id,
    //             'type' => 'Debit',
    //             'amount' => $finalAmount,
    //             'remarks' => 'EMI payment for ' . $receiver->username,
    //             'created_at' => now(),
    //         ]);

    //         // ✅ 6. Record order (as EMI)
    //         DB::table('orders')->insert([
    //             'user_id' => $receiver->id,
    //             'from_user_id' => $currentUser->id,
    //             'package_id' => $package->id,
    //             'amount' => $finalAmount,
    //             'payment_by' => $r->payment_by,
    //             'status' => 'completed',
    //             'created_at' => now(),
    //             'updated_at' => now(),
    //         ]);

    //         // ✅ 7. Update investment_count
    //         DB::table('users')
    //             ->where('id', $receiver->id)
    //             ->update([
    //                 'investment_count' => $newCount,
    //                 'emi_status' => $newCount >= 16 ? 'completed' : 'ongoing',
    //                 'updated_at' => now(),
    //             ]);

    //         // ✅ Trigger pair bonus check for ALL uplines (Kapil → Isro → ...)
    //         $sponsor = DB::table('users')->where('id', $receiver->placement_id)->first();

    //         while ($sponsor) {
    //             $this->checkAndDistributePairCompletionBonus($sponsor, $package->amount);

    //             if (empty($sponsor->placement_id)) {
    //                 break;
    //             }
    //             $sponsor = DB::table('users')->where('id', $sponsor->placement_id)->first();
    //         }

    //         // self::checkAndDistributePairBonus($receiver);

    //         // ✅ 8. Trigger reward once all 16 EMIs are completed
    //         if ($newCount >= 16) {
    //             self::rewardAfterFullEmi($receiver);
    //         }

    //         // ✅ 9. Distribute 50% commission (using original package amount, not including registration fee)
    //         $this->distributeCommission($receiver->id, $package->amount);

    //         DB::commit();

    //         // ✅ Return different messages based on package type
    //         $successMessage = match ($packageId) {
    //             4, 5 => "Congratulations! You have successfully paid ₹{$finalAmount} from your wallet. You have received " . ($packageId == 4 ? '4' : '8') . ' vouchers for each month now.',
    //             default => "EMI #{$newCount} paid successfully! ₹{$finalAmount} deducted from your wallet.",
    //         };

    //         return back()->with('success', $successMessage);
    //     } catch (\Exception $e) {
    //         DB::rollBack();
    //         return back()->with('error', 'Something went wrong: ' . $e->getMessage());
    //     }
    // }

    //

    // public function store(Request $r)
    // {
    //     // 1. Clean and Validate
    //     $memberId = strtoupper(trim($r->member_id));
    //     $r->validate([
    //         'member_id' => 'required|string|exists:users,member_id',
    //         'package_id' => 'required|integer|exists:packages,id',
    //         'payment_by' => 'required|string',
    //     ]);
    //     $member = \App\Models\User::where('member_id', $r->member_id)->first();

    //     if (!$member) {
    //         return back()
    //             ->withInput()
    //             ->withErrors(['member_id' => 'The provided Member ID is invalid or does not exist.']);
    //     }

    //     $currentUser = Auth::user();
    //     $receiver = DB::table('users')->where('member_id', $memberId)->first();
    //     $package = DB::table('packages')->where('id', $r->package_id)->first();
    //     $wallet = DB::table('wallets')->where('user_id', $currentUser->id)->first();

    //     if (!$receiver) {
    //         return back()->with('error', 'Member not found.');
    //     }

    //     // 2. Safe Calculation
    //     // Use '0' if the column is missing or null to prevent crashes
    //     $currentCount = $receiver->investment_count ?? 0;
    //     $registrationFee = $currentCount == 0 ? 100 : 0;
    //     $finalAmount = (float) $package->amount + $registrationFee;

    //     // 3. Wallet Check
    //     if (!$wallet || $wallet->balance < $finalAmount) {
    //         return back()->with('error', "Insufficient balance. Need: ₹{$finalAmount}");
    //     }

    //     DB::beginTransaction();
    //     try {
    //         // A. Deduct from Wallet
    //         DB::table('wallets')->where('user_id', $currentUser->id)->decrement('balance', $finalAmount);

    //         DB::table('transactions')->insert([
    //             'user_id' => $currentUser->id,
    //             'type' => 'Debit',
    //             'amount' => $finalAmount,
    //             'remarks' => 'EMI payment for ' . $receiver->username,
    //             'created_at' => now(),
    //         ]);
    //         // B. Create Order
    //         DB::table('orders')->insert([
    //             'user_id' => $receiver->id,
    //             'from_user_id' => $currentUser->id,
    //             'package_id' => $package->id,
    //             'amount' => $finalAmount,
    //             'payment_by' => $r->payment_by,
    //             'status' => 'completed',
    //             'created_at' => now(),
    //             'updated_at' => now(),
    //         ]);

    //         // C. Update Receiver Progress
    //         $increment = match ((int) $package->id) {
    //             1 => 1,
    //             2 => 8,
    //             3 => 16,
    //             default => 1,
    //         };

    //         $newTotal = $currentCount + $increment;

    //         // Using update instead of increment for better control
    //         DB::table('users')
    //             ->where('id', $receiver->id)
    //             ->update([
    //                 'investment_count' => $newTotal,
    //                 'emi_status' => $newTotal >= 16 ? 'completed' : 'ongoing',
    //                 'updated_at' => now(),
    //             ]);

    //         // D. Commission
    //         if (method_exists($this, 'distributeCommission')) {
    //             // dd($this);
    //             $this->distributeCommission($receiver->id, $package->amount);
    //         }

    //         DB::commit();
    //         return back()->with('success', "Success! Package assigned to {$receiver->member_id}");
    //     } catch (\Exception $e) {
    //         DB::rollBack();
    //         return back()->with('error', 'Error: ' . $e->getMessage());
    //     }
    // }

    public function store(Request $r)
    {
        // 1. Validate using member_id instead of email
        $r->validate([
            'member_id' => 'required|string|exists:users,member_id',
            'package_id' => 'required|integer|exists:packages,id',
            'payment_by' => 'required|string',
        ]);

        $currentUser = Auth::user();
        $memberId = strtoupper(trim($r->member_id));

        // 2. Fetch data using member_id
        // Note: We use the Model \App\Models\User to ensure the reward functions work correctly
        $receiver = \App\Models\User::where('member_id', $memberId)->first();
        $package = DB::table('packages')->where('id', $r->package_id)->first();
        $wallet = DB::table('wallets')->where('user_id', $currentUser->id)->first();

        if (!$receiver) {
            return back()->with('error', 'Member not found.');
        }

        // 3. INTERNAL FINAL AMOUNT CALCULATION (Fixes the "Top-up not happening" issue)
        $currentCount = $receiver->investment_count ?? 0;
        $registrationFee = $currentCount == 0 ? 100 : 0;
        $finalAmount = (float) $package->amount + $registrationFee;

        // 4. Calculate increment value based on package_id
        $packageId = (int) $r->package_id;

        $packageAmount = (float) $package->amount;
        $incrementValue = match ($packageAmount) {
            1000.0 => 1,
            7000.0 => 7,
            13000.0 => 13,
            50000.0 => 50,
            100000.0 => 100,
            default => floor($packageAmount / 1000),
        };

        $newTotal = $currentCount + $incrementValue;

        // --- Start: PACKAGE LIMIT & LOCK-IN LOGIC ---
        //     $currentCount = $receiver->investment_count ?? 0;
        // $packageAmount = (float) $package->amount;

        // // 1. Normalize Existing Order Amount (Subtract 100 if it was the first payment)
        // $existingOrder = DB::table('orders')
        //     ->where('user_id', $receiver->id)
        //     ->first();

        // if ($existingOrder) {
        //     $existingBaseAmount = (float) $existingOrder->amount;

        //     // If the first order was 1100, 7100, etc., treat it as 1000, 7000 for comparison
        //     $normalizedExisting = ($existingBaseAmount % 1000 == 100)
        //         ? $existingBaseAmount - 100
        //         : $existingBaseAmount;

        //     if ($normalizedExisting != $packageAmount) {
        //         return back()->with('error', "Member is locked to the ₹{$normalizedExisting} package. Cannot top up with ₹{$packageAmount}.");
        //     }
        // }

        // // 2. Define Max Top-ups for each specific Amount (Base Amounts)
        // $maxLimit = match ($packageAmount) {
        //     1000.0  => 16,
        //     7000.0  => 2,
        //     13000.0 => 1,
        //     50000.0 => 1,
        //     100000.0 => 1,
        //     default => 0,
        // };

        // if ($maxLimit === 0) {
        //     return back()->with('error', "Invalid package. Only 1k (16x), 7k (2x), 13k (1x), 50k (1x), or 100k (1x) are allowed.");
        // }

        // // 3. Calculate Increment (Package 1=1, 2=8, 3=16)
        // $packageId = (int) $package->id;
        // $incrementValue = match ($packageId) {
        //     1 => 1,
        //     2 => 8,
        //     3 => 16,
        //     default => 1,
        // };

        // $newTotal = $currentCount + $incrementValue;

        // // 4. Enforce the specific Limit Check
        // if ($newTotal > $maxLimit) {
        //     $remaining = max(0, $maxLimit - $currentCount);
        //     return back()->with('error', "Limit reached for ₹{$packageAmount}. Remaining: {$remaining}. This selection adds {$incrementValue}.");
        // }
        // --- END: PACKAGE LIMIT & LOCK-IN LOGIC ---

        // ✅ 1. Check EMI/Investment limit (max 16)
        $amount = $r->input('amount');
        // $receiver = User::find($r->input('user_id'));

        // 1. Define the dynamic EMI limits
        if ($amount == 1000) {
            $limit = 16;
        } elseif ($amount == 7000) {
            $limit = 2;
        } elseif (in_array($amount, [13000, 50000, 100000])) {
            $limit = 1;
        } else {
            $limit = 1; // Default fallback
        }

        $currentCount = $receiver->emi_status ?? 0;
        $incrementValue = 1; // Assuming 1 package = 1 EMI entry
        $newTotal = $currentCount + $incrementValue;

        // 2. Updated Validation Condition
        if ($newTotal > $limit) {
            $remaining = max(0, $limit - $currentCount);

            // Dynamic error message based on the specific package limit
            return back()->with('error', "Limit Exceeded for ₹{$amount} package. " . "This package allows a maximum of {$limit} EMI(s). " . "You can only add {$remaining} more.");
        }

        // ✅ 2. Wallet balance validation
        if (!$wallet || $wallet->balance < $finalAmount) {
            return back()->with('error', "Insufficient wallet balance. Need ₹{$finalAmount} to perform this top-up.");
        }

        // ✅ 3. Lucky Service logic for specialized packages
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

            // ✅ 5. Record debit transaction
            DB::table('transactions')->insert([
                'user_id' => $currentUser->id,
                'type' => 'Debit',
                'amount' => $finalAmount,
                'remarks' => 'EMI payment for ' . $receiver->username . " ({$memberId})",
                'created_at' => now(),
            ]);

            // ✅ 6. Record order (as EMI)
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
            DB::table('users')
                ->where('id', $receiver->id)
                ->update([
                    'investment_count' => $newTotal,
                    'emi_status' => $newTotal >= 16 ? 'completed' : 'ongoing',
                    'updated_at' => now(),
                ]);

            // ✅ 8. Trigger pair bonus check for ALL uplines (The part missing in the new function)
            $sponsor = DB::table('users')->where('id', $receiver->placement_id)->first();

            while ($sponsor) {
                if (method_exists($this, 'checkAndDistributePairCompletionBonus')) {
                    $this->checkAndDistributePairCompletionBonus($sponsor, $package->amount);
                }

                if (empty($sponsor->placement_id)) {
                    break;
                }
                $sponsor = DB::table('users')->where('id', $sponsor->placement_id)->first();
            }

            // ✅ 9. Trigger reward once all 16 EMIs are completed
            if ($newTotal >= 16 && method_exists($this, 'rewardAfterFullEmi')) {
                $this->rewardAfterFullEmi($receiver);
            }

            // ✅ 10. Distribute 50% commission
            if ($currentCount == 0) {
                if (method_exists($this, 'distributeCommission')) {
                    $this->distributeCommission($receiver->id, $package->amount);
                }
            }

            DB::commit();

            // ✅ 11. Success messages
            $successMessage = match ($packageId) {
                4, 5 => "Congratulations! You have successfully paid ₹{$finalAmount} for Member {$memberId}. Vouchers have been issued.",
                default => "EMI #{$newTotal} paid successfully! ₹{$finalAmount} deducted from your wallet for Member {$memberId}.",
            };

            return back()->with('success', $successMessage);
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Something went wrong: ' . $e->getMessage());
        }
    }
private function checkAndDistributePairCompletionBonus($sponsor, $amount)
{
    if (!$sponsor) return;

    // 1. Get TOTAL Volume from both sides
    // Even if they only have 1 package, we sum the subtree in case there are many users
    $leftUsers = $this->getFullSubtreeUsers($sponsor->id, 'left');
    $rightUsers = $this->getFullSubtreeUsers($sponsor->id, 'right');

    if (empty($leftUsers) || empty($rightUsers)) return;

//     $leftTotalVolume = collect($leftUsers)->sum('package_amount'); 
// $rightTotalVolume = collect($rightUsers)->sum('package_amount');
// 1. Get the IDs of all users in the left and right subtrees
$leftUserIds = collect($leftUsers)->pluck('id')->toArray();
$rightUserIds = collect($rightUsers)->pluck('id')->toArray();

// 2. Sum the 'amount' from the orders table for those specific users
// This ensures we get ₹50,000 if that is what they actually paid.
$leftTotalVolume = DB::table('orders')
    ->whereIn('user_id', $leftUserIds)
    ->sum('amount');

$rightTotalVolume = DB::table('orders')
    ->whereIn('user_id', $rightUserIds)
    ->sum('amount');
    // 2. The Matching Math
    // If Left Child has 50k and Right Child has 50k, Match = 50,000
    $currentMaxMatch = min($leftTotalVolume, $rightTotalVolume);
    // dd($currentMaxMatch);

    // 3. Subtract what was ALREADY paid to this sponsor
    $totalPaidBonus = DB::table('transactions')
        ->where('user_id', $sponsor->id)
        ->where('remarks', 'like', 'Pair Completion Bonus%')
        ->sum('amount');

    $alreadyMatchedVolume = $totalPaidBonus * 10;

    // 4. Calculate the Difference
    $newVolumeToPay = $currentMaxMatch - $alreadyMatchedVolume;

    // 5. Pay the 10% Bonus
    if ($newVolumeToPay >= 1000) { 
        $pairBonus = $newVolumeToPay * 0.10;

        DB::transaction(function () use ($sponsor, $pairBonus, $newVolumeToPay) {
            DB::table('wallets')->where('user_id', $sponsor->id)->increment('balance', $pairBonus);

            DB::table('transactions')->insert([
                'user_id' => $sponsor->id,
                'type' => 'Credit',
                'amount' => $pairBonus,
                'remarks' => "Pair Completion Bonus: Matched ₹" . number_format($newVolumeToPay) . " volume (10% Bonus)",
                'created_at' => now(),
            ]);
        });
    }
}
    // private function checkAndDistributePairCompletionBonus($sponsor, $amount)
    // {
    //     // dd($sponsor, $amount);
    //     DB::table('wallets')->updateOrInsert(['user_id' => $sponsor->id], ['updated_at' => now()]);

    //     // 🔹 Get FULL left subtree
    //     $leftUsers = $this->getFullSubtreeUsers($sponsor->id, 'left');

    //     // 🔹 Get FULL right subtree
    //     $rightUsers = $this->getFullSubtreeUsers($sponsor->id, 'right');
    //     // dd($leftUsers, $rightUsers);
    //     if (empty($leftUsers) || empty($rightUsers)) {
    //         return;
    //     }
    //     // 🔹 1. Calculate Total Points in each subtree (1 point per ₹1000)
    //     $leftPoints = collect($leftUsers)->sum('investment_count');
    //     $rightPoints = collect($rightUsers)->sum('investment_count');
    //     // dd($leftPoints, $rightPoints);
    //     // 🔹 2. Calculate Pairs and Check History
    //     $totalPairsPossible = min($leftPoints, $rightPoints);
    //     // dd($totalPairsPossible);
    //     // Check total bonus already paid (Total Bonus Amount / 100)
    //     // 10% of 1000 = 100, so each matched point = ₹100 bonus
    //     $totalBonusPaid = DB::table('transactions')->where('user_id', $sponsor->id)->where('remarks', 'like', 'Pair Completion Bonus%')->sum('amount');
    //     // dd($totalBonusPaid);
    //     $alreadyPaidPoints = $totalBonusPaid / 100;
    //     // dd($totalPairsPossible, $alreadyPaidPoints);
    //     // 🔹 3. Distribute New Points (One transaction for all new volume)
    //     $leftVolume = collect($leftUsers)->sum('investment_count') * 1000;
    //     $rightVolume = collect($rightUsers)->sum('investment_count') * 1000;

    //     // 4. Determine the Match (Example: Left 13k vs Right 1k = 1k Match)
    //     $totalMatchedPossible = min($leftVolume, $rightVolume);

    //     // 5. Calculate Volume already paid out
    //     // Since Bonus = 10% of Volume, then Volume = Bonus Paid * 10
    //     $totalBonusPaid = DB::table('transactions')->where('user_id', $sponsor->id)->where('remarks', 'like', 'Pair Completion Bonus%')->sum('amount');

    //     $alreadyMatchedVolume = $totalBonusPaid * 10;
    //     if ($totalPairsPossible > $alreadyPaidPoints) {
    //         // $newPoints = $totalPairsPossible - $alreadyPaidPoints;
    //         // $totalPairBonus = $newPoints * 100;

    //         $newVolumeToMatch = $totalMatchedPossible - $alreadyMatchedVolume;

    //         // Bonus is exactly 10% of the new match
    //         $pairBonus = $newVolumeToMatch * 0.1;

    //         DB::transaction(function () use ($sponsor, $pairBonus, $newVolumeToMatch) {
    //             // Update Wallet
    //             DB::table('wallets')->where('user_id', $sponsor->id)->increment('balance', $pairBonus);

    //             // Record Transaction
    //             DB::table('transactions')->insert([
    //                 'user_id' => $sponsor->id,
    //                 'type' => 'Credit',
    //                 'amount' => $pairBonus,
    //                 'remarks' => 'Pair Completion Bonus: Matched ₹' . number_format($newVolumeToMatch) . ' volume (10% Bonus)',
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

    private function OLDcheckAndDistributePairCompletionBonusMAYANKOLD($sponsor, $amount)
    {
        // Get left & right direct children
        $left = DB::table('users')->where('placement_id', $sponsor->id)->where('position', 'left')->first();

        $right = DB::table('users')->where('placement_id', $sponsor->id)->where('position', 'right')->first();

        // Both legs must exist
        if (!$left || !$right) {
            return;
        }

        // Both must have paid SAME EMI number
        if ($left->investment_count != $right->investment_count) {
            return;
        }

        $emiNumber = $left->investment_count;

        // Prevent duplicate pair bonus for same EMI
        $alreadyPaid = DB::table('transactions')
            ->where([
                'user_id' => $sponsor->id,
                'remarks' => "Pair Bonus EMI #{$emiNumber}",
            ])
            ->first();

        if ($alreadyPaid) {
            return;
        }

        // Calculate pair bonus (10%)
        $pairBonus = $amount * 0.1;

        // Credit sponsor wallet
        DB::table('wallets')->where('user_id', $sponsor->id)->increment('balance', $pairBonus);

        // Record transaction
        DB::table('transactions')->insert([
            'user_id' => $sponsor->id,
            'type' => 'Credit',
            'amount' => $pairBonus,
            'remarks' => "Pair Completion Bonus from {$leftChild->username} and {$rightChild->username} (₹{$amount})",
            'created_at' => now(),
        ]);
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

    private function checkAndDistributePairCompletionBonusOOOOLLDD($sponsor, $amount)
    {
        // ✅ Ensure wallet exists
        DB::table('wallets')->updateOrInsert(['user_id' => $sponsor->id], ['balance' => DB::raw('COALESCE(balance,0)'), 'updated_at' => now()]);

        // Get left leg & right leg users with depth
        $leftUsers = $this->getLegUsersByDepth($sponsor->id, 'left');
        $rightUsers = $this->getLegUsersByDepth($sponsor->id, 'right');

        if (empty($leftUsers) || empty($rightUsers)) {
            return;
        }

        // Max depth available in either side
        $maxDepthLeft = max(array_column($leftUsers, 'depth'));
        $maxDepthRight = max(array_column($rightUsers, 'depth'));
        $maxDepth = min($maxDepthLeft, $maxDepthRight);

        for ($depth = 1; $depth <= $maxDepth; $depth++) {
            // ✅ Pick outermost users at this depth
            $leftPick = $this->pickExtremeAtDepth($leftUsers, $depth, 'leftmost');
            $rightPick = $this->pickExtremeAtDepth($rightUsers, $depth, 'rightmost');

            if (!$leftPick || !$rightPick) {
                continue;
            }

            // Must have EMI paid (investment_count > 0)
            if ($leftPick['investment_count'] <= 0 || $rightPick['investment_count'] <= 0) {
                continue;
            }

            // ✅ Total EMIs that can be paired at this depth
            $possiblePairsForDepth = min($leftPick['investment_count'], $rightPick['investment_count']);

            // ✅ Already paid pairs for this sponsor+depth
            $alreadyPaidPairs = DB::table('transactions')
                ->where('user_id', $sponsor->id)
                ->where('remarks', 'like', "Pair Completion Bonus Depth {$depth} EMI #%")
                ->count();

            // ✅ New pairs to pay
            $newPairs = $possiblePairsForDepth - $alreadyPaidPairs;
            if ($newPairs <= 0) {
                continue;
            }

            // Pay each EMI separately (so your remarks & reports stay correct)
            for ($emi = $alreadyPaidPairs + 1; $emi <= $possiblePairsForDepth; $emi++) {
                $pairBonus = $amount * 0.1;

                DB::table('wallets')->where('user_id', $sponsor->id)->increment('balance', $pairBonus);

                DB::table('transactions')->insert([
                    'user_id' => $sponsor->id,
                    'type' => 'Credit',
                    'amount' => $pairBonus,
                    'remarks' => "Pair Completion Bonus Depth {$depth} EMI #{$emi} from {$leftPick['username']} & {$rightPick['username']} (₹{$amount})",
                    'created_at' => now(),
                ]);
            }
        }
    }

    /**
     * Distribute Binary MLM Commission (10% Direct + 10% Indirect)
     * + Pair Completion Bonus (10% when both legs activate)
     */
    private function distributeCommission($userId, $amount)
    {
        $commission = $amount * 0.1; // 10% commission
        if ($amount >= 50000) {
            $commission = $amount * 0.05; // 10% commission
        }

        $user = DB::table('users')->find($userId);
        // dd($user);
        if (!$user) {
            return;
        }

        // // ✅ 1️⃣ DIRECT COMMISSION (10%) - To immediate sponsor
        // $sponsor = DB::table('users')->where('id', $user->placement_id)->first();
        // if ($sponsor) {
        //     // Ensure wallet exists
        //     DB::table('wallets')->updateOrInsert(
        //         ['user_id' => $sponsor->id],
        //         ['updated_at' => now()]
        //     );

        //     // Add 10% commission to sponsor wallet
        //     DB::table('wallets')->where('user_id', $sponsor->id)
        //         ->increment('balance', $commission);

        //     // Record transaction
        //     DB::table('transactions')->insert([
        //         'user_id' => $sponsor->id,
        //         'type' => 'Credit',
        //         'amount' => $commission,
        //         'remarks' => "Direct 10% Commission from {$user->username} (₹{$amount})",
        //         'created_at' => now(),
        //     ]);

        //     // ✅ 3️⃣ CHECK FOR PAIR COMPLETION BONUS
        //     $this->checkAndDistributePairCompletionBonus($sponsor, $amount);
        // }
        // ✅ DIRECT COMMISSION — ONLY ON FIRST EMI
        // dd($user);
        if ($user->investment_count >= 1) {
            $sponsor = DB::table('users')->where('id', $user->sponsor_id)->first();
            // dd($sponsor);
            $this->checkAndDistributePairCompletionBonus($sponsor, $amount);
            if ($sponsor) {
                // $commission = $amount * 0.1;
                $commission = $amount * 0.1; // 10% commission
                if ($amount >= 50000) {
                    $commission = $amount * 0.05; // 10% commission
                }

                DB::table('wallets')->updateOrInsert(['user_id' => $sponsor->id], ['updated_at' => now()]);

                DB::table('wallets')->where('user_id', $sponsor->id)->increment('balance', $commission);

                DB::table('transactions')->insert([
                    'user_id' => $sponsor->id,
                    'type' => 'Credit',
                    'amount' => $commission,
                    'remarks' => "Direct 10% Commission from {$user->username} (₹{$amount})",
                    'created_at' => now(),
                ]);
                $this->checkAndDistributePairCompletionBonus($sponsor, $amount);
            }
        }

        // ✅ 2️⃣ INDIRECT COMMISSION (10%) - To upline chain through binary tree
        $current = $user;
        $level = 1;

        while ($current && $level <= 10) {
            // Limit to 10 levels
            // Get parent/upline from binary structure
            $binaryNode = DB::table('binary_nodes')->where('user_id', $current->id)->first();
            if (!$binaryNode || !$binaryNode->parent_id) {
                break;
            }

            $upline = DB::table('users')->find($binaryNode->parent_id);
            if (!$upline) {
                break;
            }

            // 10% commission for indirect sales
            // $indirectCommission = $amount * 0.1;
            $indirectCommission = $amount * 0.1; // 10% indirectCommission
            if ($amount >= 50000) {
                $indirectCommission = $amount * 0.05; // 10% indirectCommission
            }

            // Ensure wallet exists
            DB::table('wallets')->updateOrInsert(['user_id' => $upline->id], ['updated_at' => now()]);

            // Add commission to upline wallet
            DB::table('wallets')->where('user_id', $upline->id)->increment('balance', $indirectCommission);

            // Record transaction
            DB::table('transactions')->insert([
                'user_id' => $upline->id,
                'type' => 'Credit',
                'amount' => $indirectCommission,
                'remarks' => "Indirect 10% Commission from {$user->username} - Level {$level} (₹{$amount})",
                'created_at' => now(),
            ]);

            $current = $upline;
            $level++;
        }
    }

    /**
     * Pair Completion Bonus - When both left and right legs have paid
     */
    private function checkAndDistributePairCompletionBonusTOOOLD($sponsor, $amount)
    {
        $left = DB::table('users')->where('placement_id', $sponsor->id)->where('position', 'left')->first();

        $right = DB::table('users')->where('placement_id', $sponsor->id)->where('position', 'right')->first();

        if (!$left || !$right) {
            return;
        }

        // ❗ BOTH must have paid SAME EMI
        if ($left->investment_count != $right->investment_count) {
            return;
        }

        $emiNumber = $left->investment_count;

        // ❗ Check if this EMI pair bonus already paid
        $alreadyPaid = DB::table('transactions')
            ->where([
                'user_id' => $sponsor->id,
                'remarks' => "Pair Bonus EMI #{$emiNumber}",
            ])
            ->first();

        if ($alreadyPaid) {
            return;
        }

        // ✅ PAY PAIR BONUS
        $pairBonus = $amount * 0.1;

        DB::table('wallets')->where('user_id', $sponsor->id)->increment('balance', $pairBonus);

        DB::table('transactions')->insert([
            'user_id' => $sponsor->id,
            'type' => 'Credit',
            'amount' => $pairBonus,
            'remarks' => "Pair Completion Bonus from {$leftChild->username} and {$rightChild->username} (₹{$amount})",
            'created_at' => now(),
        ]);
    }

    private function checkAndDistributePairCompletionBonusOLD($sponsor, $amount)
    {
        // Get both legs of the sponsor
        $leftChild = DB::table('users')->where('placement_id', $sponsor->id)->where('position', 'left')->first();

        $rightChild = DB::table('users')->where('placement_id', $sponsor->id)->where('position', 'right')->first();

        // Check if BOTH legs exist AND both have made at least 1 investment
        if (!$leftChild || !$rightChild) {
            return;
        }
        if ($leftChild->investment_count < 1 || $rightChild->investment_count < 1) {
            return;
        }

        // ✅ Check if pair bonus already given (to avoid duplicate payments)
        $alreadyGiven = DB::table('transactions')
            ->where([
                'user_id' => $sponsor->id,
            ])
            ->where('remarks', 'like', 'Pair Completion Bonus%')
            ->first();

        if ($alreadyGiven) {
            return;
        }

        // ✅ Pay pair bonus (10% of current EMI amount)
        $pairBonus = $amount * 0.1;

        DB::table('wallets')->where('user_id', $sponsor->id)->increment('balance', $pairBonus);

        DB::table('transactions')->insert([
            'user_id' => $sponsor->id,
            'type' => 'Credit',
            'amount' => $pairBonus,
            'remarks' => "Pair Completion Bonus from {$leftChild->username} and {$rightChild->username} (₹{$amount})",
            'created_at' => now(),
        ]);
    }

    private static function rewardAfterFullEmi($user)
    {
        $rewardAmount = 5000; // or calculate dynamically

        // Credit reward to wallet
        DB::table('wallets')->where('user_id', $user->id)->increment('balance', $rewardAmount);

        // Record credit transaction
        DB::table('transactions')->insert([
            'user_id' => $user->id,
            'type' => 'Credit',
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

                DB::table('transactions')->insert([
                    'user_id' => $parent->id,
                    'type' => 'Credit',
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
