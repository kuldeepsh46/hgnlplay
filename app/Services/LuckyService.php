<?php

namespace App\Services;

use DB;
use Carbon\Carbon;
use Str;

class LuckyService
{
    public static function createCycleIfNotExists($userId, $packageId)
    {
        $exists = DB::table('lucky_cycles')->where('user_id', $userId)->exists();

        if (!$exists) {
            DB::table('lucky_cycles')->insert([
                'user_id' => $userId,
                'package_id' => $packageId,
                'start_date' => now()->toDateString(),
                'current_month' => 0,
                'status' => 'active',
                'created_at' => now()
            ]);
        }
    }

    /**
     * ADMIN ACTION – RELEASE MONTHLY VOUCHERS
     */
    public static function releaseMonthlyVouchers()
    {
        // 🔒 30-day lock
        $lastRelease = DB::table('lucky_release_logs')->latest('released_at')->first();

        if ($lastRelease && Carbon::parse($lastRelease->released_at)->diffInDays(now()) < 30) {
            throw new \Exception('Vouchers already released this month.');
        }

        $cycles = DB::table('lucky_cycles')
            ->where('status', 'active')
            ->where('current_month', '<', 16)
            ->get();

        foreach ($cycles as $cycle) {

            $nextMonth = $cycle->current_month + 1;

            $voucherCount = match ($cycle->package_id) {
                4 => 4,
                5 => 8,
                default => 0,
            };

            for ($i = 1; $i <= $voucherCount; $i++) {
                DB::table('lucky_vouchers')->insert([
                    'cycle_id' => $cycle->id,
                    'user_id' => $cycle->user_id,
                    'month_no' => $nextMonth,
                    'voucher_code' => strtoupper(Str::random(10)),
                    'created_at' => now(),
                ]);
            }

            // Update cycle month
            DB::table('lucky_cycles')->where('id', $cycle->id)->update([
                'current_month' => $nextMonth,
                'updated_at' => now()
            ]);

            // 🎁 Completed 16 months – NO WIN → GOLD
            if ($nextMonth == 16) {
                self::grantGoldIfNoWin($cycle);
            }
        }

        DB::table('lucky_release_logs')->insert([
            'released_at' => now()
        ]);
    }

    private static function grantGoldIfNoWin($cycle)
    {
        $goldValue = $cycle->package_id == 4 ? 65000 : 130000;

        DB::table('lucky_cycles')->where('id', $cycle->id)->update([
            'status' => 'completed',
            'completed_at' => now()
        ]);

        DB::table('lucky_rewards')->insert([
            'user_id' => $cycle->user_id,
            'cycle_id' => $cycle->id,
            'reward_type' => 'gold',
            'reward_value' => $goldValue,
            'reward_note' => 'Gold reward after 16 months without winning',
            'created_at' => now()
        ]);
    }

    /**
     * ADMIN DECLARES WINNER
     */
    public static function declareWinner($voucherId, $adminId)
    {
        $voucher = DB::table('lucky_vouchers')->where('id', $voucherId)->first();

        if (!$voucher || $voucher->status !== 'unused') {
            throw new \Exception('Invalid voucher');
        }

        DB::table('lucky_vouchers')->where('id', $voucherId)->update([
            'status' => 'used'
        ]);

        DB::table('lucky_cycles')->where('id', $voucher->cycle_id)->update([
            'status' => 'won',
            'won_at' => now()
        ]);

        DB::table('lucky_rewards')->insert([
            'user_id' => $voucher->user_id,
            'cycle_id' => $voucher->cycle_id,
            'reward_type' => 'cash',
            'reward_note' => 'Lucky draw winner',
            'admin_id' => $adminId,
            'created_at' => now()
        ]);
    }
}
