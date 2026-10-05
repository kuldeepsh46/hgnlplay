<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

/*
|--------------------------------------------------------------------------
| Superadmin kill switches
|--------------------------------------------------------------------------
| Each switch, when ON, pauses part of the site. Enforced for every web and
| API request by App\Http\Middleware\SystemSwitches, and by the nightly
| payout commands. Superadmins are never blocked, so they can turn
| switches back off.
|--------------------------------------------------------------------------
*/
class SystemSwitch
{
    // key => [label, what it does]
    public const SWITCHES = [
        'site_offline' => ['Take the whole website offline', 'Everyone except superadmins sees a "temporarily unavailable" page, including the homepage and the app. Superadmins can still log in.'],
        'freeze_all' => ['Freeze all operations', 'Pages still open, but nothing can be saved or changed by anyone: no top-ups, withdrawals, fund requests, registrations, approvals, profile or admin changes. Nightly payouts are skipped.'],
        'member_logins' => ['Block logins', 'Everyone except superadmins is logged out and cannot log in (website and app).'],
        'registrations' => ['Pause new registrations', 'No new members can register.'],
        'topups' => ['Pause top-ups', 'No package purchases or top-ups, so no direct, level or pair income is created.'],
        'withdrawals' => ['Pause withdrawal requests', 'Members cannot request withdrawals.'],
        'fund_requests' => ['Pause wallet fund requests', 'Members cannot submit wallet fund requests.'],
        'admin_approvals' => ['Pause admin approvals', 'Admins cannot approve or reject fund requests or payouts.'],
        'payout_jobs' => ['Pause automatic payouts', 'The nightly Sponsor Binary Bonus and Rank Reward jobs skip their run.'],
    ];

    // Route names (and paths, for unnamed routes) each switch blocks
    public const ROUTES = [
        'registrations' => ['register', 'member.store', 'mlm.register.store'],
        'topups' => ['member.topup.store', 'order.store'],
        'withdrawals' => ['withdraw.store'],
        'fund_requests' => ['wallet.fund.store'],
        'admin_approvals' => ['admin.payments.approve', 'admin.payments.reject', 'admin.payouts.approve', 'admin.payouts.reject'],
    ];

    public const PATHS = [
        'registrations' => ['register'],
    ];

    private static ?array $states = null;

    /**
     * All switch rows keyed by switch key. Read once per request; if the
     * table does not exist yet (before migrating) every switch is off.
     */
    public static function states(bool $fresh = false): array
    {
        if (self::$states !== null && !$fresh) {
            return self::$states;
        }

        try {
            self::$states = DB::table('system_switches')->get()->keyBy('key')->all();
        } catch (\Throwable $e) {
            self::$states = [];
        }

        return self::$states;
    }

    public static function isOn(string $key): bool
    {
        return (bool) (self::states()[$key]->is_on ?? false);
    }

    public static function message(string $key): ?string
    {
        return self::states()[$key]->message ?? null;
    }

    /** Keys of every switch that is currently ON */
    public static function active(): array
    {
        return array_keys(array_filter(self::states(), fn($row) => (bool) $row->is_on));
    }

    public static function set(string $key, bool $on, ?string $message, ?int $userId, ?string $ip = null): void
    {
        abort_unless(isset(self::SWITCHES[$key]), 404);
        $message = $message !== null && trim($message) !== '' ? mb_substr(trim($message), 0, 500) : null;

        DB::transaction(function () use ($key, $on, $message, $userId, $ip) {
            DB::table('system_switches')->updateOrInsert(
                ['key' => $key],
                ['is_on' => $on, 'message' => $message, 'updated_by' => $userId, 'updated_at' => now()]
            );
            DB::table('system_switch_logs')->insert([
                'key' => $key,
                'is_on' => $on,
                'message' => $message,
                'user_id' => $userId,
                'ip' => $ip,
                'created_at' => now(),
            ]);
        });

        self::$states = null;
    }

    /**
     * The switch that stops the nightly payout jobs right now, if any.
     */
    public static function payoutsPausedBy(): ?string
    {
        foreach (['site_offline', 'freeze_all', 'payout_jobs'] as $key) {
            if (self::isOn($key)) {
                return $key;
            }
        }

        return null;
    }
}
