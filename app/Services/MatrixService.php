<?php

namespace App\Services;

use App\Models\User;
use App\Models\UserMatrixProgress;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Enums\BonusType;
use Throwable;

class MatrixService
{
    public function processCommission(User $buyer)
    {
        try {
            Log::info('MatrixService started', [
                'buyer_id' => $buyer->id,
                'buyer_member_id' => $buyer->member_id ?? null,
                'buyer_username' => $buyer->username ?? null,
                'buyer_rank_level' => $buyer->rank_level ?? null,
                'buyer_sponsor_id' => $buyer->sponsor_id ?? null,
            ]);

            if (!$buyer->sponsor_id) {
                Log::warning('MatrixService stopped: buyer has no sponsor', [
                    'buyer_id' => $buyer->id,
                ]);
                return;
            }

            /**
             * BUYER LEVEL DECIDES COMMISSION MATRIX
             */
            $buyerRank = (int) ($buyer->rank_level ?? 1);

            if ($buyerRank < 1) {
                $buyerRank = 1;
            }

            if ($buyerRank > 3) {
                $buyerRank = 3;
            }

            $commissionMatrix = [
                1 => [
                    1 => 100,
                    2 => 200,
                    3 => 700,
                ],
                2 => [
                    1 => 500,
                    2 => 1500,
                    3 => 5000,
                ],
                3 => [
                    1 => 1000,
                    2 => 2000,
                    3 => 27000,
                ],
            ];

            Log::info('Buyer rank resolved', [
                'buyer_id' => $buyer->id,
                'buyer_rank_used' => $buyerRank,
            ]);

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

            Log::info('Upline resolved', [
                'buyer_id' => $buyer->id,
                'tier_1_user_id' => $l1?->id,
                'tier_2_user_id' => $l2?->id,
                'tier_3_user_id' => $l3?->id,
            ]);

            foreach ($upline as $tier => $user) {
                Log::info('Processing upline tier', [
                    'buyer_id' => $buyer->id,
                    'tier' => $tier,
                    'upline_user_id' => $user?->id,
                    'upline_member_id' => $user?->member_id ?? null,
                ]);

                if (!$user) {
                    Log::warning('Skipped tier: upline user not found', [
                        'buyer_id' => $buyer->id,
                        'tier' => $tier,
                    ]);
                    continue;
                }

                /**
                 * CHECK UPLINE EMI STATUS
                 */
                $emisData = DB::table('orders')
                    ->where('user_id', $user->id)
                    ->where('status', 'completed')
                    ->selectRaw('MIN(created_at) as activation_date, COUNT(*) as total_emis_paid')
                    ->first();

                Log::info('EMI data fetched for upline', [
                    'tier' => $tier,
                    'upline_user_id' => $user->id,
                    'activation_date' => $emisData->activation_date ?? null,
                    'total_emis_paid' => $emisData->total_emis_paid ?? null,
                ]);

                if (!$emisData || !$emisData->activation_date) {
                    Log::warning('Skipped tier: upline has no completed order / activation date', [
                        'tier' => $tier,
                        'upline_user_id' => $user->id,
                    ]);
                    continue;
                }

                $activationDate = \Carbon\Carbon::parse($emisData->activation_date);

                $activationMonth = ($activationDate->year * 12) + $activationDate->month;
                $currentMonth = (now()->year * 12) + now()->month;

                $totalEmisSupposedToPay = ($currentMonth - $activationMonth) + 1;
                $totalEmisPaid = $emisData->total_emis_paid ?? 0;

                Log::info('EMI status calculated', [
                    'tier' => $tier,
                    'upline_user_id' => $user->id,
                    'total_emis_paid' => $totalEmisPaid,
                    'total_emis_supposed_to_pay' => $totalEmisSupposedToPay,
                ]);

                if ($totalEmisPaid < $totalEmisSupposedToPay) {
                    Log::warning('Skipped tier: upline EMI pending', [
                        'tier' => $tier,
                        'upline_user_id' => $user->id,
                        'paid' => $totalEmisPaid,
                        'due' => $totalEmisSupposedToPay,
                    ]);
                    continue;
                }

                /**
                 * GET OR CREATE MATRIX PROGRESS
                 */
                $progress = UserMatrixProgress::firstOrCreate(
                    [
                        'user_id' => $user->id,
                    ],
                    [
                        'tier_1_count' => 0,
                        'tier_2_count' => 0,
                        'tier_3_count' => 0,
                    ]
                );

                $tierField = "tier_{$tier}_count";

                $maxLimit = [
                    1 => 3,
                    2 => 9,
                    3 => 27,
                ][$tier];

                Log::info('Matrix progress loaded', [
                    'tier' => $tier,
                    'upline_user_id' => $user->id,
                    'progress_id' => $progress->id,
                    'tier_field' => $tierField,
                    'current_count' => $progress->$tierField,
                    'max_limit' => $maxLimit,
                ]);

                DB::transaction(function () use (
                    $user,
                    $progress,
                    $tierField,
                    $tier,
                    $maxLimit,
                    $commissionMatrix,
                    $buyerRank,
                    $buyer
                ) {
                    $progress = UserMatrixProgress::where('id', $progress->id)
                        ->lockForUpdate()
                        ->first();

                    if (!$progress) {
                        Log::error('Matrix progress missing after lockForUpdate', [
                            'upline_user_id' => $user->id,
                            'tier' => $tier,
                        ]);
                        return;
                    }

                    Log::info('Matrix progress locked', [
                        'tier' => $tier,
                        'upline_user_id' => $user->id,
                        'current_count' => $progress->$tierField,
                        'max_limit' => $maxLimit,
                    ]);

                    if ($progress->$tierField >= $maxLimit) {
                        Log::warning('Skipped payout: tier limit already reached', [
                            'tier' => $tier,
                            'upline_user_id' => $user->id,
                            'tier_field' => $tierField,
                            'current_count' => $progress->$tierField,
                            'max_limit' => $maxLimit,
                        ]);
                        return;
                    }

                    if (!isset($commissionMatrix[$buyerRank][$tier])) {
                        Log::error('Commission amount not found in matrix', [
                            'buyer_id' => $buyer->id,
                            'buyer_rank' => $buyerRank,
                            'tier' => $tier,
                        ]);
                        return;
                    }

                    $amount = $commissionMatrix[$buyerRank][$tier];

                    Log::info('Commission amount calculated', [
                        'buyer_id' => $buyer->id,
                        'buyer_rank' => $buyerRank,
                        'tier' => $tier,
                        'receiver_user_id' => $user->id,
                        'amount' => $amount,
                    ]);

                    $walletUpdated = DB::table('wallets')
                        ->where('user_id', $user->id)
                        ->increment('balance', $amount);

                    Log::info('Wallet increment attempted', [
                        'receiver_user_id' => $user->id,
                        'amount' => $amount,
                        'wallet_update_result' => $walletUpdated,
                    ]);

                    if (!$walletUpdated) {
                        Log::error('Wallet update failed or wallet row missing', [
                            'receiver_user_id' => $user->id,
                            'amount' => $amount,
                        ]);

                        throw new \Exception("Wallet not found or not updated for user ID {$user->id}");
                    }

                    DB::table('transactions')->insert([
                        'user_id' => $user->id,
                        'amount' => $amount,
                        'type' => 'credit',
                        'bonus_type' => BonusType::LevelIncome->value,
                        'remarks' => "Level Income (Tier {$tier}) from {$buyer->member_id} to {$user->member_id}",
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);

                    Log::info('Level income transaction inserted', [
                        'buyer_id' => $buyer->id,
                        'receiver_user_id' => $user->id,
                        'tier' => $tier,
                        'amount' => $amount,
                        'bonus_type' => BonusType::LevelIncome->value,
                    ]);

                    $progress->increment($tierField);

                    Log::info('Matrix tier count incremented', [
                        'receiver_user_id' => $user->id,
                        'tier' => $tier,
                        'tier_field' => $tierField,
                    ]);
                });

                $progress->refresh();

                Log::info('Progress refreshed after transaction', [
                    'upline_user_id' => $user->id,
                    'tier_1_count' => $progress->tier_1_count,
                    'tier_2_count' => $progress->tier_2_count,
                    'tier_3_count' => $progress->tier_3_count,
                ]);

                $this->checkPromotion($user, $progress);
            }

            Log::info('MatrixService completed successfully', [
                'buyer_id' => $buyer->id,
            ]);

        } catch (Throwable $e) {
            Log::error('MatrixService failed', [
                'buyer_id' => $buyer->id ?? null,
                'buyer_member_id' => $buyer->member_id ?? null,
                'error_message' => $e->getMessage(),
                'error_file' => $e->getFile(),
                'error_line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
            ]);

            throw $e;
        }
    }

    private function checkPromotion(User $user, UserMatrixProgress $progress)
    {
        try {
            Log::info('Checking promotion', [
                'user_id' => $user->id,
                'member_id' => $user->member_id ?? null,
                'rank_level' => $user->rank_level ?? null,
                'tier_1_count' => $progress->tier_1_count,
                'tier_2_count' => $progress->tier_2_count,
                'tier_3_count' => $progress->tier_3_count,
            ]);

            if (
                $progress->tier_1_count >= 3 &&
                $progress->tier_2_count >= 9 &&
                $progress->tier_3_count >= 27
            ) {
                DB::table('user_matrix_rank_history')->insert([
                    'user_id' => $user->id,
                    'rank_level' => $user->rank_level ?? 1,
                    'tier_1_count' => $progress->tier_1_count,
                    'tier_2_count' => $progress->tier_2_count,
                    'tier_3_count' => $progress->tier_3_count,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                Log::info('Rank history inserted', [
                    'user_id' => $user->id,
                    'old_rank_level' => $user->rank_level ?? 1,
                ]);

                $user->increment('rank_level');

                $progress->update([
                    'tier_1_count' => 0,
                    'tier_2_count' => 0,
                    'tier_3_count' => 0,
                ]);

                Log::info('User promoted successfully', [
                    'user_id' => $user->id,
                    'new_rank_level' => $user->fresh()->rank_level,
                ]);
            }
        } catch (Throwable $e) {
            Log::error('Promotion check failed', [
                'user_id' => $user->id ?? null,
                'error_message' => $e->getMessage(),
                'error_file' => $e->getFile(),
                'error_line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
            ]);

            throw $e;
        }
    }
}