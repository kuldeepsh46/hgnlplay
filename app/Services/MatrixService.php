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
     * Tier completion requirements.
     *
     * Tier 1 complete = 3
     * Tier 2 complete = 9
     * Tier 3 complete = 27
     *
     * These values are now used dynamically everywhere.
     */
    private array $tierLimits = [
        1 => 3,
        2 => 9,
        3 => 27,
    ];

    /**
     * Commission matrix by topup user's rank.
     *
     * Format:
     * topup_rank_level => [
     *     tier => amount
     * ]
     */
    private array $commissionMatrix = [
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
            $topupUserRank = $this->resolveRankLevel($topupUser->rank_level ?? 1);

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
            $upline = $this->resolveUpline($topupUser);

            Log::info('Upline resolved from topup user', [
                'topup_user_id' => $topupUser->id,
                'topup_member_id' => $topupUser->member_id ?? null,
                'tier_1_user_id' => $upline[1]?->id,
                'tier_1_member_id' => $upline[1]?->member_id,
                'tier_2_user_id' => $upline[2]?->id,
                'tier_2_member_id' => $upline[2]?->member_id,
                'tier_3_user_id' => $upline[3]?->id,
                'tier_3_member_id' => $upline[3]?->member_id,
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

                $tierField = $this->getTierField($tier);
                $maxLimit = $this->getTierLimit($tier);

                Log::info('Matrix progress loaded', [
                    'tier' => $tier,
                    'receiver_user_id' => $receiver->id,
                    'receiver_member_id' => $receiver->member_id ?? null,
                    'progress_id' => $progress->id,
                    'tier_field' => $tierField,
                    'current_count' => $progress->{$tierField},
                    'max_limit' => $maxLimit,
                ]);

                DB::transaction(function () use (
                    $receiver,
                    $progress,
                    $tierField,
                    $tier,
                    $maxLimit,
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
                        'current_count' => $progress->{$tierField},
                        'max_limit' => $maxLimit,
                    ]);

                    /**
                     * STOP ONLY THIS TIER IF LIMIT REACHED
                     */
                    if ($progress->{$tierField} >= $maxLimit) {
                        Log::warning('Skipped payout: tier limit already reached', [
                            'tier' => $tier,
                            'receiver_user_id' => $receiver->id,
                            'receiver_member_id' => $receiver->member_id ?? null,
                            'tier_field' => $tierField,
                            'current_count' => $progress->{$tierField},
                            'max_limit' => $maxLimit,
                        ]);

                        return;
                    }

                    /**
                     * DYNAMIC ELIGIBILITY CHECK
                     *
                     * Tier 1:
                     * - Allowed if normal EMI/progress checks pass.
                     *
                     * Tier 2:
                     * - Receiver must have completed Tier 1 dynamically.
                     * - Tier 1 complete means:
                     *   - tier_1_count >= tierLimits[1]
                     *   - receiver has enough Tier 1 level income transactions
                     *   - receiver has enough direct children with completed product orders
                     *   - receiver has received Tier 1 income from enough active direct children
                     *
                     * Tier 3:
                     * - Receiver must have completed Tier 1 and Tier 2 dynamically.
                     */
                    $eligibility = $this->getTierEligibility($receiver, $tier, $progress);

                    if (!$eligibility['eligible']) {
                        Log::warning('Skipped payout: receiver is not eligible for this tier', [
                            'tier' => $tier,
                            'receiver_user_id' => $receiver->id,
                            'receiver_member_id' => $receiver->member_id ?? null,
                            'tier_1_count' => $progress->tier_1_count,
                            'tier_2_count' => $progress->tier_2_count,
                            'tier_3_count' => $progress->tier_3_count,
                            'reasons' => $eligibility['reasons'],
                            'details' => $eligibility['details'],
                        ]);

                        return;
                    }

                    /**
                     * CALCULATE LEVEL INCOME
                     */
                    $amount = $this->getCommissionAmount($topupUserRank, $tier);

                    if (!$amount) {
                        Log::error('Commission amount not found in matrix', [
                            'topup_user_id' => $topupUser->id,
                            'topup_rank' => $topupUserRank,
                            'tier' => $tier,
                        ]);

                        return;
                    }

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

                    /**
                     * Keep Product User and Product User ID in remarks.
                     * This makes future eligibility checks more reliable.
                     */
                    $remarks = "Level Income (Tier {$tier})"
                        . " | Product User: " . ($topupUser->member_id ?? $topupUser->id)
                        . " | Product User ID: " . $topupUser->id
                        . " | Receiver: " . ($receiver->member_id ?? $receiver->id)
                        . " | Receiver ID: " . $receiver->id
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

    private function resolveRankLevel(int|string|null $rankLevel): int
    {
        $rankLevel = (int) ($rankLevel ?? 1);

        if ($rankLevel < 1) {
            return 1;
        }

        if ($rankLevel > 3) {
            return 3;
        }

        return $rankLevel;
    }

    private function resolveUpline(User $topupUser): array
    {
        $l1 = User::find($topupUser->sponsor_id);
        $l2 = $l1?->sponsor_id ? User::find($l1->sponsor_id) : null;
        $l3 = $l2?->sponsor_id ? User::find($l2->sponsor_id) : null;

        return [
            1 => $l1,
            2 => $l2,
            3 => $l3,
        ];
    }

    private function getTierField(int $tier): string
    {
        return "tier_{$tier}_count";
    }

    private function getTierLimit(int $tier): int
    {
        return $this->tierLimits[$tier] ?? 0;
    }

    private function getCommissionAmount(int $topupUserRank, int $tier): ?int
    {
        return $this->commissionMatrix[$topupUserRank][$tier] ?? null;
    }

    private function getTierEligibility(User $receiver, int $tier, UserMatrixProgress $progress): array
    {
        $reasons = [];
        $details = [];

        /**
         * Tier 1 is allowed directly.
         * Existing checks already handle:
         * - receiver exists
         * - receiver EMI is clear
         * - tier limit not reached
         */
        if ($tier === 1) {
            return [
                'eligible' => true,
                'reasons' => [],
                'details' => [
                    'tier' => 1,
                    'message' => 'Tier 1 does not require previous tier completion.',
                ],
            ];
        }

        /**
         * Tier 2 requires Tier 1 complete.
         * Tier 3 requires Tier 1 and Tier 2 complete.
         *
         * This is fully dynamic:
         * - For tier 2, loop checks completed tier 1.
         * - For tier 3, loop checks completed tier 1 and completed tier 2.
         */
        for ($requiredCompletedTier = 1; $requiredCompletedTier < $tier; $requiredCompletedTier++) {
            $check = $this->hasCompletedTierRequirement($receiver, $requiredCompletedTier, $progress);

            $details["tier_{$requiredCompletedTier}_requirement"] = $check['details'];

            if (!$check['eligible']) {
                $reasons = array_merge($reasons, $check['reasons']);
            }
        }

        return [
            'eligible' => empty($reasons),
            'reasons' => $reasons,
            'details' => $details,
        ];
    }

    private function hasCompletedTierRequirement(
        User $receiver,
        int $completedTier,
        UserMatrixProgress $progress
    ): array {
        $reasons = [];

        $requiredCount = $this->getTierLimit($completedTier);
        $tierField = $this->getTierField($completedTier);
        $currentProgressCount = (int) ($progress->{$tierField} ?? 0);

        $tierIncomeTransactionCount = $this->getReceivedTierIncomeTransactionCount($receiver, $completedTier);

        $details = [
            'completed_tier' => $completedTier,
            'tier_field' => $tierField,
            'required_count' => $requiredCount,
            'current_progress_count' => $currentProgressCount,
            'tier_income_transaction_count' => $tierIncomeTransactionCount,
        ];

        /**
         * Requirement 1:
         * Previous tier count must be completed.
         */
        if ($currentProgressCount < $requiredCount) {
            $reasons[] = "Tier {$completedTier} progress is not completed. Required {$requiredCount}, current {$currentProgressCount}.";
        }

        /**
         * Requirement 2:
         * Receiver must have actual Level Income transactions for that tier.
         *
         * No hard-coded amount check here.
         * ₹100, ₹500, ₹1000, etc. all count as valid tier income.
         */
        if ($tierIncomeTransactionCount < $requiredCount) {
            $reasons[] = "Tier {$completedTier} income transactions are not completed. Required {$requiredCount}, current {$tierIncomeTransactionCount}.";
        }

        /**
         * Special Tier 1 completion rule:
         *
         * To unlock Tier 2 and Tier 3:
         * - receiver must have required direct children
         * - those direct children must have completed product orders
         * - receiver must have received Tier 1 income from enough active direct children
         *
         * This is dynamic because required count comes from $tierLimits[1].
         */
        if ($completedTier === 1) {
            $activeDirectChildren = $this->getActiveDirectChildren($receiver);
            $activeDirectChildrenCount = $activeDirectChildren->count();

            $receivedFromActiveDirectChildrenCount = $this->countActiveDirectChildrenWithTierIncome(
                $receiver,
                $completedTier,
                $activeDirectChildren
            );

            $details['active_direct_children_count'] = $activeDirectChildrenCount;
            $details['received_from_active_direct_children_count'] = $receivedFromActiveDirectChildrenCount;

            if ($activeDirectChildrenCount < $requiredCount) {
                $reasons[] = "Active direct children requirement is not completed. Required {$requiredCount}, current {$activeDirectChildrenCount}.";
            }

            if ($receivedFromActiveDirectChildrenCount < $requiredCount) {
                $reasons[] = "Receiver has not received Tier {$completedTier} income from enough active direct children. Required {$requiredCount}, current {$receivedFromActiveDirectChildrenCount}.";
            }
        }

        return [
            'eligible' => empty($reasons),
            'reasons' => $reasons,
            'details' => $details,
        ];
    }

    private function getActiveDirectChildren(User $receiver)
    {
        return User::query()
            ->select(['id', 'member_id', 'username', 'sponsor_id'])
            ->where('sponsor_id', $receiver->id)
            ->whereExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('orders')
                    ->whereColumn('orders.user_id', 'users.id')
                    ->where('orders.status', 'completed');
            })
            ->get();
    }

    private function getReceivedTierIncomeTransactionCount(User $receiver, int $tier): int
    {
        return DB::table('transactions')
            ->where('user_id', $receiver->id)
            ->where('type', 'credit')
            ->where('bonus_type', BonusType::LevelIncome->value)
            ->where('remarks', 'LIKE', "Level Income (Tier {$tier})%")
            ->count();
    }

    private function countActiveDirectChildrenWithTierIncome(User $receiver, int $tier, $activeDirectChildren): int
    {
        $count = 0;

        foreach ($activeDirectChildren as $child) {
            $hasReceivedFromThisChild = DB::table('transactions')
                ->where('user_id', $receiver->id)
                ->where('type', 'credit')
                ->where('bonus_type', BonusType::LevelIncome->value)
                ->where('remarks', 'LIKE', "Level Income (Tier {$tier})%")
                ->where(function ($query) use ($child) {
                    if (!empty($child->member_id)) {
                        $query->orWhere('remarks', 'LIKE', "%Product User: {$child->member_id}%");
                    }

                    $query->orWhere('remarks', 'LIKE', "%Product User: {$child->id}%")
                        ->orWhere('remarks', 'LIKE', "%Product User ID: {$child->id}%");
                })
                ->exists();

            if ($hasReceivedFromThisChild) {
                $count++;
            }
        }

        return $count;
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
                $progress->tier_1_count >= $this->getTierLimit(1) &&
                $progress->tier_2_count >= $this->getTierLimit(2) &&
                $progress->tier_3_count >= $this->getTierLimit(3)
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