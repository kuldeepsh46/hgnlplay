<?php

namespace App\Services;

use App\Models\User;
use App\Models\UserMatrixProgress;
use Illuminate\Support\Facades\DB;

class MatrixService
{
    public function processCommission(User $buyer)
    {
        if (!$buyer->sponsor_id) return;

        /**
         * BUYER LEVEL DECIDES COMMISSION MATRIX
         */
        $buyerRank = $buyer->rank_level;

        $commissionMatrix = [
            1 => [1 => 100, 2 => 200, 3 => 700],
            2 => [1 => 500, 2 => 1500, 3 => 5000],
            3 => [1 => 1000, 2 => 2000, 3 => 27000],
        ];

        /**
         * FIND UP-LINE (3 LEVELS)
         */
        $l1 = User::find($buyer->sponsor_id);
        $l2 = $l1?->sponsor_id ? User::find($l1->sponsor_id) : null;
        $l3 = $l2?->sponsor_id ? User::find($l2->sponsor_id) : null;

        $upline = [
            1 => $l1,
            2 => $l2,
            3 => $l3,
        ];

        foreach ($upline as $tier => $user) {

            if (!$user) continue;

            $progress = UserMatrixProgress::firstOrCreate([
                'user_id' => $user->id
            ]);

            $tierField = "tier_{$tier}_count";

            $maxLimit = [
                1 => 3,
                2 => 9,
                3 => 27
            ][$tier];

            DB::transaction(function () use (
                $user,
                $progress,
                $tierField,
                $tier,
                $maxLimit,
                $commissionMatrix,
                $buyerRank
            ) {

                /**
                 * LOCK ROW (Prevent race condition)
                 */
                $progress = UserMatrixProgress::where('id', $progress->id)
                    ->lockForUpdate()
                    ->first();

                /**
                 * STOP if limit reached
                 */
                if ($progress->$tierField >= $maxLimit) {
                    return;
                }

                /**
                 * CALCULATE AMOUNT
                 */
                $amount = $commissionMatrix[$buyerRank][$tier];

                /**
                 * UPDATE WALLET
                 */
                DB::table('wallets')
                    ->where('user_id', $user->id)
                    ->increment('balance', $amount);

                /**
                 * INSERT TRANSACTION (NEW)
                 */
                DB::table('transactions')->insert([
                    'user_id'    => $user->id,
                    'amount'     => $amount,
                    'type'       => 'credit',
                    'remarks'    => "Pair Completion Bonus (Tier {$tier}) to {$user->member_id}",
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                /**
                 * INCREMENT MATRIX COUNT
                 */
                $progress->increment($tierField);
            });

            /**
             * REFRESH AFTER TRANSACTION
             */
            $progress->refresh();

            /**
             * CHECK PROMOTION
             */
            $this->checkPromotion($user, $progress);
        }
    }

    /**
     * PROMOTION LOGIC
     */
    private function checkPromotion(User $user, UserMatrixProgress $progress)
    {
        if (
            $progress->tier_1_count >= 3 &&
            $progress->tier_2_count >= 9 &&
            $progress->tier_3_count >= 27
        ) {

            /**
             * SAVE HISTORY BEFORE RESET
             */
            DB::table('user_matrix_rank_history')->insert([
                'user_id'       => $user->id,
                'rank_level'    => $progress->rank_level,
                'tier_1_count'  => $progress->tier_1_count,
                'tier_2_count'  => $progress->tier_2_count,
                'tier_3_count'  => $progress->tier_3_count,
                'created_at'    => now(),
                'updated_at'    => now(),
            ]);

            /**
             * PROMOTE USER
             */
            $progress->update([
                'rank_level'     => $progress->rank_level + 1,
                'tier_1_count'   => 0,
                'tier_2_count'   => 0,
                'tier_3_count'   => 0,
            ]);
        }
    }
}