<?php

namespace App\Services;

use App\Models\Package;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

/*
|--------------------------------------------------------------------------
| Payment dues (monthly EMIs) for a member
|--------------------------------------------------------------------------
| Same rule the rest of the app uses (team list, manage users, MatrixService):
| the first completed order is the activation date, and one EMI is expected
| for every calendar month from then on, up to TOTAL_EMIS. EMI number N is
| due on the activation day of month N (clamped to the month's last day).
|
| "Paid" is the member's investment_count, which TopupController keeps up to
| date (packages that prepay several EMIs bump it by 8 / 16).
|--------------------------------------------------------------------------
*/
class PaymentDueService
{
    public const TOTAL_EMIS = 16;

    // TopupController charges a Starter repurchase (every EMI after the first) ₹1000
    public const EMI_AMOUNT = 1000;

    // Show a reminder this many days before the next EMI falls due
    public const REMIND_DAYS_BEFORE = 5;

    private static array $cache = [];

    public static function forUser(User $user): ?array
    {
        return self::$cache[$user->id] ??= self::build($user);
    }

    private static function build(User $user): ?array
    {
        $activation = DB::table('orders')
            ->where('user_id', $user->id)
            ->where('status', 'completed')
            ->min('created_at');

        // Not activated yet, or all EMIs done: nothing to collect
        if (!$activation || $user->emi_status === 'completed') {
            return null;
        }

        $activation = Carbon::parse($activation)->startOfDay();
        $today = now()->startOfDay();

        $paid = (int) ($user->investment_count ?? 0);
        if ($paid <= 0) {
            $paid = DB::table('orders')->where('user_id', $user->id)->where('status', 'completed')->count();
        }
        $paid = min($paid, self::TOTAL_EMIS);

        if ($paid >= self::TOTAL_EMIS) {
            return null;
        }

        $monthsElapsed = ($today->year * 12 + $today->month) - ($activation->year * 12 + $activation->month);
        $expected = min($monthsElapsed + 1, self::TOTAL_EMIS);
        $dueCount = max(0, $expected - $paid);

        $schedule = [];
        for ($n = 1; $n <= self::TOTAL_EMIS; $n++) {
            $dueDate = self::dueDate($activation, $n);

            if ($n <= $paid) {
                $status = 'paid';
            } elseif ($n <= $expected) {
                $status = $dueDate->lt($today) ? 'overdue' : 'due';
            } else {
                $status = 'upcoming';
            }

            $schedule[] = [
                'number' => $n,
                'due_date' => $dueDate,
                'amount' => self::EMI_AMOUNT,
                'status' => $status,
            ];
        }

        $next = $schedule[$paid]; // first unpaid EMI (index = number - 1)
        $daysUntilNext = $today->diffInDays($next['due_date'], false);

        if ($dueCount > 0) {
            $state = collect($schedule)->contains('status', 'overdue') ? 'overdue' : 'due';
        } elseif ($daysUntilNext <= self::REMIND_DAYS_BEFORE) {
            $state = 'upcoming';
        } else {
            $state = 'clear';
        }

        $wallet = DB::table('wallets')->where('user_id', $user->id)->value('balance');

        return [
            'state' => $state,                       // overdue | due | upcoming | clear
            'activation_date' => $activation,
            'total_emis' => self::TOTAL_EMIS,
            'paid' => $paid,
            'expected' => $expected,
            'due_count' => $dueCount,
            'amount_per_emi' => self::EMI_AMOUNT,
            'total_due' => $dueCount * self::EMI_AMOUNT,
            'pay_now_amount' => max($dueCount, 1) * self::EMI_AMOUNT,
            'remaining_emis' => self::TOTAL_EMIS - $paid,
            'next' => $next,
            'days_until_next' => (int) $daysUntilNext,
            'oldest_overdue_date' => collect($schedule)->firstWhere('status', 'overdue')['due_date'] ?? null,
            'wallet_balance' => (float) ($wallet ?? 0),
            'starter_package_id' => self::starterPackageId(),
            'schedule' => $schedule,
        ];
    }

    private static function dueDate(Carbon $activation, int $emiNumber): Carbon
    {
        return $activation->copy()->addMonthsNoOverflow($emiNumber - 1);
    }

    private static function starterPackageId(): ?int
    {
        return Package::all(['id', 'name'])->first(fn ($p) => Package::isStarter($p))?->id;
    }
}
