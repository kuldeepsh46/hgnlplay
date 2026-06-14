<?php

use App\Enums\BonusType;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private string $table = 'transactions'; // change if your table name is different

    public function up(): void
    {
        /*
         |--------------------------------------------------------------------------
         | Direct Income
         |--------------------------------------------------------------------------
         | Example:
         | 10% Direct Commission from userright (₹1,000)
         |
         | Static part:
         | Direct Commission from
         */
        $this->updateByStaticRemarks(
            [
                '%direct commission from%',
                '%direct%commission%from%',
            ],
            BonusType::DirectIncome->value
        );

        /*
         |--------------------------------------------------------------------------
         | Commission
         |--------------------------------------------------------------------------
         | Examples:
         | L8 Commission (0.25%) from userright (₹50,000)
         | L7 Commission (0.25%) from userleft (₹50,000)
         |
         | Static-ish parts:
         | Commission
         | from
         |
         | We run this after DirectIncome, so direct commission rows are already handled.
         */
        $this->updateByStaticRemarks(
            [
                'l% commission% from%',
                '% commission% from%',
            ],
            BonusType::commission->value
        );

        /*
         |--------------------------------------------------------------------------
         | Pair Bonus Normal
         |--------------------------------------------------------------------------
         | Example:
         | Pair Completion Bonus: Matched ₹1,000 volume (10% Bonus)
         |
         | Static parts:
         | Pair Completion Bonus:
         | Matched
         | volume
         */
        $this->updateByStaticRemarks(
            [
                '%pair completion bonus:%matched%volume%',
                '%pair completion bonus:%matched%',
                '%matched%volume%bonus%',
            ],
            BonusType::PairBonusNormal->value
        );

        /*
         |--------------------------------------------------------------------------
         | EMI Payment
         |--------------------------------------------------------------------------
         | Example:
         | EMI payment for OMSHIV (HGNL1566)
         |
         | Static part:
         | EMI payment for
         */
        $this->updateByStaticRemarks(
            [
                '%emi payment for%',
                '%emi%payment%for%',
            ],
            BonusType::EmiPayment->value
        );

        /*
         |--------------------------------------------------------------------------
         | Level Income
         |--------------------------------------------------------------------------
         | Examples:
         | Pair Completion Bonus (Tier 1)
         | Pair Completion Bonus (Tier 2)
         |
         | Static parts:
         | Pair Completion Bonus
         | Tier
         */
        $this->updateByStaticRemarks(
            [
                '%pair completion bonus (tier%',
                '%pair completion bonus%tier%',
            ],
            BonusType::LevelIncome->value
        );

        /*
         |--------------------------------------------------------------------------
         | Reward
         |--------------------------------------------------------------------------
         | Example:
         | Reward for completing all 16 EMIs
         |
         | Static parts:
         | Reward
         | completing
         | EMIs
         */
        $this->updateByStaticRemarks(
            [
                '%reward for completing all%emis%',
                '%reward%completing%emis%',
                '%reward%16%emis%',
            ],
            BonusType::reward->value
        );

        /*
         |--------------------------------------------------------------------------
         | Fund Request
         |--------------------------------------------------------------------------
         | Example:
         | Fund request approved by admin
         |
         | Static part:
         | Fund request approved
         */
        $this->updateByStaticRemarks(
            [
                '%fund request approved by admin%',
                '%fund request approved%',
            ],
            BonusType::FundRequest->value
        );
    }

    public function down(): void
    {
        /*
         | Keep this empty to avoid deleting corrected data.
         | This is a data-fix migration, not a schema rollback.
         */
    }

    private function updateByStaticRemarks(array $patterns, string $bonusType): void
    {
        $data = [
            'bonus_type' => $bonusType,
        ];

        if (Schema::hasColumn($this->table, 'updated_at')) {
            $data['updated_at'] = now();
        }

        DB::table($this->table)
            ->whereNull('bonus_type')
            ->whereNotNull('remarks')
            ->where(function ($query) use ($patterns) {
                foreach ($patterns as $pattern) {
                    $query->orWhereRaw('LOWER(remarks) LIKE ?', [
                        strtolower($pattern),
                    ]);
                }
            })
            ->update($data);
    }
};