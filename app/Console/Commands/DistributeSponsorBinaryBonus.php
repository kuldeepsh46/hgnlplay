<?php

namespace App\Console\Commands;

use App\Enums\BonusType;
use App\Services\WalletService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class DistributeSponsorBinaryBonus extends Command
{
    protected $signature = 'income:sponsor-binary-bonus {--date= : Day to process (Y-m-d). Defaults to yesterday.}';

    protected $description = 'Credit 10% of each user\'s daily pair/binary income (capped at ₹5,000) to their direct sponsor';

    /*
    |--------------------------------------------------------------------------
    | Rules
    |--------------------------------------------------------------------------
    | A user's binary income for the day is the sum of every pair income type
    | (BonusType::sponsorBonusSourceTypes()). It is capped at ₹5,000 before
    | taking 10%, so a sponsor gets at most ₹500 per direct downline per day.
    |--------------------------------------------------------------------------
    */
    private const DAILY_BINARY_CAP = 5000;
    private const BONUS_RATE = 0.1;

    public function handle(): int
    {
        $date = $this->option('date') ?: now()->subDay()->toDateString();

        if ($pausedBy = \App\Services\SystemSwitch::payoutsPausedBy()) {
            $this->warn("Skipped: payouts are paused by the \"{$pausedBy}\" system switch. Nothing credited for {$date}. "
                . "Once it is off, run: php artisan income:sponsor-binary-bonus --date={$date}");
            return self::SUCCESS;
        }

        $parsed = \DateTime::createFromFormat('!Y-m-d', $date);

        if (!$parsed || $parsed->format('Y-m-d') !== $date) {
            $this->error('Invalid --date. Use the format YYYY-MM-DD, e.g. --date=2026-10-03.');
            return self::FAILURE;
        }

        if ($date >= now()->toDateString()) {
            $this->error('Only a finished day can be paid; pass a date before today.');
            return self::FAILURE;
        }

        $dailyBinary = DB::table('transactions')
            ->whereIn('bonus_type', BonusType::sponsorBonusSourceTypes())
            ->whereBetween('created_at', [$date . ' 00:00:00', $date . ' 23:59:59'])
            ->groupBy('user_id')
            ->selectRaw('user_id, SUM(amount) as total')
            ->get();

        $credited = 0;
        $skipped = 0;
        $totalPaid = 0;

        foreach ($dailyBinary as $row) {
            $earner = DB::table('users')->where('id', $row->user_id)->first();

            if (!$earner || empty($earner->sponsor_id)) {
                $skipped++;
                continue;
            }

            $binaryIncome = round((float) $row->total, 2);
            $cappedIncome = min($binaryIncome, self::DAILY_BINARY_CAP);
            $bonus = round($cappedIncome * self::BONUS_RATE, 2);

            if ($bonus <= 0) {
                $skipped++;
                continue;
            }

            $paid = DB::transaction(function () use ($earner, $date, $binaryIncome, $cappedIncome, $bonus) {
                $sponsor = DB::table('users')->where('id', $earner->sponsor_id)->lockForUpdate()->first();

                if (!$sponsor) {
                    return false;
                }

                // The unique (source_user_id, income_date) key makes this a
                // no-op when the day was already paid, even if two runs overlap.
                $inserted = DB::table('sponsor_binary_bonus_payouts')->insertOrIgnore([
                    'sponsor_id' => $sponsor->id,
                    'source_user_id' => $earner->id,
                    'income_date' => $date,
                    'binary_income' => $binaryIncome,
                    'capped_income' => $cappedIncome,
                    'bonus_amount' => $bonus,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                if (!$inserted) {
                    return false;
                }


                $earnerCode = $earner->member_id ?: ('#' . $earner->id);

                $remarks = 'Sponsor Binary Bonus from ' . $earnerCode . ' (' . ($earner->username ?? $earner->name) . ') for ' . Carbon::parse($date)->format('d M Y')
                    . ': 10% of ₹' . number_format($cappedIncome, 2)
                    . ' | Pair income earned ₹' . number_format($binaryIncome, 2)
                    . ' | Daily cap ₹' . number_format(self::DAILY_BINARY_CAP, 2);

                // 90% main wallet / 10% Repurchase Wallet
                $transactionId = WalletService::creditEarning($sponsor->id, (float) $bonus, [
                    'type' => 'credit',
                    'bonus_type' => BonusType::SponsorBinaryBonus->value,
                    'remarks' => $remarks,
                ]);

                DB::table('sponsor_binary_bonus_payouts')
                    ->where('source_user_id', $earner->id)
                    ->where('income_date', $date)
                    ->update(['transaction_id' => $transactionId]);

                return true;
            });

            if ($paid) {
                $credited++;
                $totalPaid += $bonus;
            } else {
                $skipped++;
            }
        }

        $this->info("Sponsor binary bonus for {$date}: {$credited} credited (₹" . number_format($totalPaid, 2) . "), {$skipped} skipped.");

        return self::SUCCESS;
    }
}
