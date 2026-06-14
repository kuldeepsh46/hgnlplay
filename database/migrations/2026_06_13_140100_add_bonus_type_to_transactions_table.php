<?php

use App\Enums\BonusType;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    private string $table = 'wallet_transactions';

    public function up(): void
    {
        /*
         |--------------------------------------------------------------------------
         | Important order:
         |--------------------------------------------------------------------------
         | 1. Direct commission before generic commission
         | 2. Pair normal before pair tier/level income
         | 3. Only update rows where bonus_type is NULL
         */

        // 10% Direct Commission from userright (₹1,000)
        $this->updateBonusTypeByRemarks([
            '%direct commission%',
        ], BonusType::DirectIncome->value);

        // L8 Commission (0.25%) from userright (₹50,000)
        // L7 Commission (0.25%) from userleft (₹50,000)
        $this->updateBonusTypeByRemarks([
            '%commission%from%',
        ], BonusType::commission->value);

        // Pair Completion Bonus: Matched ₹1,000 volume (10% Bonus)
        $this->updateBonusTypeByRemarks([
            '%pair completion bonus:%matched%volume%',
            '%matched%volume%bonus%',
        ], BonusType::PairBonusNormal->value);

        // EMI payment for userright (HGNL1592)
        $this->updateBonusTypeByRemarks([
            '%emi payment for%',
        ], BonusType::EmiPayment->value);

        // Pair Completion Bonus (Tier 1)
        // Pair Completion Bonus (Tier 2)
        $this->updateBonusTypeByRemarks([
            '%pair completion bonus%tier%',
        ], BonusType::LevelIncome->value);

        // Reward for completing all 16 EMIs
        $this->updateBonusTypeByRemarks([
            '%reward for completing all 16 emis%',
            '%reward%completing%16%emis%',
        ], BonusType::reward->value);

        // Fund request approved by admin
        $this->updateBonusTypeByRemarks([
            '%fund request approved by admin%',
            '%fund request approved%',
        ], BonusType::FundRequest->value);
    }

    public function down(): void
    {
        /*
         | Data backfill rollback is intentionally empty.
         | Reason: resetting bonus_type to NULL may remove valid corrected data.
         */
    }

    private function updateBonusTypeByRemarks(array $patterns, string $bonusType): void
    {
        DB::table($this->table)
            ->whereNull('bonus_type')
            ->where(function ($query) use ($patterns) {
                foreach ($patterns as $pattern) {
                    $query->orWhereRaw('LOWER(remarks) LIKE ?', [
                        strtolower($pattern),
                    ]);
                }
            })
            ->update([
                'bonus_type' => $bonusType,
                'updated_at' => now(),
            ]);
    }
};