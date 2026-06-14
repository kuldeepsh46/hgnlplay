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
    /**
     * IMPORTANT:
     * $topupUser = actual user/node for whom package/product is purchased.
     * $payer = user who made payment. It can be parent/admin/self.
     *
     * Matrix commission always starts from $topupUser as Node 0.
     */
    public function processCommission(User $topupUser, ?User $payer = null)
    {
        try {
            Log::info('MatrixService started', [
                'topup_user_id' => $topupUser->id,
                'topup_member_id' => $topupUser->member_id ?? null,
                'topup_username' => $topupUser->username ?? null,
                'topup_rank_level' => $topupUser->rank_level ?? null,
                'topup_sponsor_id' => $topupUser->sponsor_id ?? null,

                'payer_id' => $payer?->id,
                'payer_member_id' => $payer?->member_id,
                'payer_username' => $payer?->username,
            ]);

            if (!$topupUser->sponsor_id) {
                Log::warning('MatrixService stopped: topup user has no sponsor', [
                    'topup_user_id' => $topupUser->id,
                    'topup_member_id' => $topupUser->member_id ?? null,
                ]);

                return;
            }

            /**
             * TOPUP USER LEVEL DECIDES COMMISSION MATRIX
             */
            $topupUserRank = (int) ($topupUser->rank_level ?? 1);

            if ($topupUserRank < 1) {
                $topupUserRank = 1;
            }

            if ($topupUserRank > 3) {
                $topupUserRank = 3;
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

            Log::info('Topup user rank resolved', [
                'topup_user_id' => $topupUser->id,
                'topup_rank_used' => $topupUserRank,
            ]);

            /**
             * FIND UP-LINE FROM ACTUAL TOPUP USER
             *
             * Node 0 = $topupUser
             * Tier 1 = $topupUser sponsor
             * Tier 2 = sponsor's sponsor
             * Tier 3 = third upline
             */
            $l1 = User::find($topupUser->sponsor_id);
            $l2 = $l1?->sponsor_id ? User::find($l1->sponsor_id) : null;
            $l3 = $l2?->sponsor_id ? User::find($l2->sponsor_id) : null;

            $upline = [
                1 => $l1,
                2 => $l2,
                3 => $l3,
            ];

            Log::info('Upline resolved from topup user', [
                'topup_user_id' => $topupUser->id,
                'topup_member_id' => $topupUser->member_id ?? null,
                'tier_1_user_id' => $l1?->id,
                'tier_1_member_id' => $l1?->member_id,
                'tier_2_user_id' => $l2?->id,
                'tier_2_member_id' => $l2?->member_id,
                'tier_3_user_id' => $l3?->id,
                'tier_3_member_id' => $l3?->member_id,
            ]);

            foreach ($upline as $tier => $receiver) {
                Log::info('Processing upline tier', [
                    'topup_user_id' => $topupUser->id,
                    'topup_member_id' => $topupUser->member_id ?? null,
                    'tier' => $tier,
                    'receiver_user_id' => $receiver?->id,
                    'receiver_member_id' => $receiver?->member_id ?? null,
                ]);

                if (!$receiver) {
                    Log::warning('Skipped tier: receiver/upline user not found', [
                        'topup_user_id' => $topupUser->id,
                        'tier' => $tier,
                    ]);

                    continue;
                }

                /**
                 * CHECK RECEIVER EMI STATUS
                 */
                $emisData = DB::table('orders')
                    ->where('user_id', $receiver->id)
                    ->where('status', 'completed')
                    ->selectRaw('MIN(created_at) as activation_date, COUNT(*) as total_emis_paid')
                    ->first();

                Log::info('EMI data fetched for receiver', [
                    'tier' => $tier,
                    'receiver_user_id' => $receiver->id,
                    'receiver_member_id' => $receiver->member_id ?? null,
                    'activation_date' => $emisData->activation_date ?? null,
                    'total_emis_paid' => $emisData->total_emis_paid ?? null,
                ]);

                if (!$emisData || !$emisData->activation_date) {
                    Log::warning('Skipped tier: receiver has no completed order / activation date', [
                        'tier' => $tier,
                        'receiver_user_id' => $receiver->id,
                        'receiver_member_id' => $receiver->member_id ?? null,
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
                    'receiver_user_id' => $receiver->id,
                    'receiver_member_id' => $receiver->member_id ?? null,
                    'total_emis_paid' => $totalEmisPaid,
                    'total_emis_supposed_to_pay' => $totalEmisSupposedToPay,
                ]);

                if ($totalEmisPaid < $totalEmisSupposedToPay) {
                    Log::warning('Skipped tier: receiver EMI pending', [
                        'tier' => $tier,
                        'receiver_user_id' => $receiver->id,
                        'receiver_member_id' => $receiver->member_id ?? null,
                        'paid' => $totalEmisPaid,
                        'due' => $totalEmisSupposedToPay,
                    ]);

                    continue;
                }

                /**
                 * GET OR CREATE MATRIX PROGRESS FOR RECEIVER
                 */
                $progress = UserMatrixProgress::firstOrCreate(
                    [
                        'user_id' => $receiver->id,
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
                    'receiver_user_id' => $receiver->id,
                    'receiver_member_id' => $receiver->member_id ?? null,
                    'progress_id' => $progress->id,
                    'tier_field' => $tierField,
                    'current_count' => $progress->$tierField,
                    'max_limit' => $maxLimit,
                ]);

                DB::transaction(function () use (
                    $receiver,
                    $progress,
                    $tierField,
                    $tier,
                    $maxLimit,
                    $commissionMatrix,
                    $topupUserRank,
                    $topupUser,
                    $payer
                ) {
                    /**
                     * LOCK ROW TO PREVENT RACE CONDITION
                     */
                    $progress = UserMatrixProgress::where('id', $progress->id)
                        ->lockForUpdate()
                        ->first();

                    if (!$progress) {
                        Log::error('Matrix progress missing after lockForUpdate', [
                            'receiver_user_id' => $receiver->id,
                            'tier' => $tier,
                        ]);

                        return;
                    }

                    Log::info('Matrix progress locked', [
                        'tier' => $tier,
                        'receiver_user_id' => $receiver->id,
                        'receiver_member_id' => $receiver->member_id ?? null,
                        'current_count' => $progress->$tierField,
                        'max_limit' => $maxLimit,
                    ]);

                    /**
                     * STOP ONLY THIS TIER IF LIMIT REACHED
                     */
                    if ($progress->$tierField >= $maxLimit) {
                        Log::warning('Skipped payout: tier limit already reached', [
                            'tier' => $tier,
                            'receiver_user_id' => $receiver->id,
                            'receiver_member_id' => $receiver->member_id ?? null,
                            'tier_field' => $tierField,
                            'current_count' => $progress->$tierField,
                            'max_limit' => $maxLimit,
                        ]);

                        return;
                    }

                    if (!isset($commissionMatrix[$topupUserRank][$tier])) {
                        Log::error('Commission amount not found in matrix', [
                            'topup_user_id' => $topupUser->id,
                            'topup_rank' => $topupUserRank,
                            'tier' => $tier,
                        ]);

                        return;
                    }

                    /**
                     * CALCULATE LEVEL INCOME
                     */
                    $amount = $commissionMatrix[$topupUserRank][$tier];

                    Log::info('Commission amount calculated', [
                        'topup_user_id' => $topupUser->id,
                        'topup_member_id' => $topupUser->member_id ?? null,
                        'topup_rank' => $topupUserRank,
                        'tier' => $tier,
                        'receiver_user_id' => $receiver->id,
                        'receiver_member_id' => $receiver->member_id ?? null,
                        'amount' => $amount,
                    ]);

                    /**
                     * UPDATE WALLET
                     */
                    $walletUpdated = DB::table('wallets')
                        ->where('user_id', $receiver->id)
                        ->increment('balance', $amount);

                    Log::info('Wallet increment attempted', [
                        'receiver_user_id' => $receiver->id,
                        'receiver_member_id' => $receiver->member_id ?? null,
                        'amount' => $amount,
                        'wallet_update_result' => $walletUpdated,
                    ]);

                    if (!$walletUpdated) {
                        Log::error('Wallet update failed or wallet row missing', [
                            'receiver_user_id' => $receiver->id,
                            'receiver_member_id' => $receiver->member_id ?? null,
                            'amount' => $amount,
                        ]);

                        throw new \Exception("Wallet not found or not updated for user ID {$receiver->id}");
                    }

                    $payerText = $payer
                        ? " | Paid By: " . ($payer->member_id ?? $payer->id)
                        : "";

                    $remarks = "Level Income (Tier {$tier})"
                        . " | Product User: " . ($topupUser->member_id ?? $topupUser->id)
                        . " | Receiver: " . ($receiver->member_id ?? $receiver->id)
                        . " | Topup Rank: {$topupUserRank}"
                        . $payerText;

                    /**
                     * INSERT TRANSACTION
                     */
                    DB::table('transactions')->insert([
                        'user_id' => $receiver->id,
                        'amount' => $amount,
                        'type' => 'credit',
                        'bonus_type' => BonusType::LevelIncome->value,
                        'remarks' => $remarks,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);

                    Log::info('Level income transaction inserted', [
                        'topup_user_id' => $topupUser->id,
                        'topup_member_id' => $topupUser->member_id ?? null,
                        'payer_id' => $payer?->id,
                        'payer_member_id' => $payer?->member_id,
                        'receiver_user_id' => $receiver->id,
                        'receiver_member_id' => $receiver->member_id ?? null,
                        'tier' => $tier,
                        'amount' => $amount,
                        'bonus_type' => BonusType::LevelIncome->value,
                    ]);

                    /**
                     * INCREMENT MATRIX COUNT
                     */
                    $progress->increment($tierField);

                    Log::info('Matrix tier count incremented', [
                        'receiver_user_id' => $receiver->id,
                        'receiver_member_id' => $receiver->member_id ?? null,
                        'tier' => $tier,
                        'tier_field' => $tierField,
                    ]);
                });

                /**
                 * REFRESH AFTER TRANSACTION
                 */
                $progress->refresh();

                Log::info('Progress refreshed after transaction', [
                    'receiver_user_id' => $receiver->id,
                    'receiver_member_id' => $receiver->member_id ?? null,
                    'tier_1_count' => $progress->tier_1_count,
                    'tier_2_count' => $progress->tier_2_count,
                    'tier_3_count' => $progress->tier_3_count,
                ]);

                /**
                 * CHECK PROMOTION
                 */
                $this->checkPromotion($receiver, $progress);
            }

            Log::info('MatrixService completed successfully', [
                'topup_user_id' => $topupUser->id,
                'topup_member_id' => $topupUser->member_id ?? null,
                'payer_id' => $payer?->id,
                'payer_member_id' => $payer?->member_id,
            ]);

        } catch (Throwable $e) {
            Log::error('MatrixService failed', [
                'topup_user_id' => $topupUser->id ?? null,
                'topup_member_id' => $topupUser->member_id ?? null,
                'payer_id' => $payer?->id,
                'payer_member_id' => $payer?->member_id,
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
                    'member_id' => $user->member_id ?? null,
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
                    'member_id' => $user->member_id ?? null,
                    'new_rank_level' => $user->fresh()->rank_level,
                ]);
            }
        } catch (Throwable $e) {
            Log::error('Promotion check failed', [
                'user_id' => $user->id ?? null,
                'member_id' => $user->member_id ?? null,
                'error_message' => $e->getMessage(),
                'error_file' => $e->getFile(),
                'error_line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
            ]);

            throw $e;
        }
    }
}