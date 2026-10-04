<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Package;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

/*
|--------------------------------------------------------------------------
| New Admin Dashboard (read-only analytics + shortcuts to existing actions)
|--------------------------------------------------------------------------
| Everything here only READS data. Approve / reject / edit buttons post to
| the existing routes (admin.payments.*, admin.payouts.*, packages.edit,
| admin.users.edit, tree.view), so no business logic is duplicated.
|--------------------------------------------------------------------------
*/
class AdminDashboardController extends Controller
{
    // Test accounts excluded from member counts (same as the current dashboard)
    private const EXCLUDED_USER_IDS = [106, 107];

    // Transaction bonus types that are income paid to members
    private const INCOME_TYPES = [
        'direct_income' => 'Direct Income',
        'pair_bonus_normal' => 'Pair Income',
        'pair_bonus_starter' => 'Starter Pair Income',
        'pair_bonus' => 'Pair Bonus (old)',
        'pair_bonus_2000' => 'Pair Bonus 2000',
        'sponsor_binary_bonus' => 'Sponsor Binary Bonus',
        'level_income' => 'Level Income',
        'commission' => 'Level Commission',
        'reward' => 'Reward',
        'reward_after_full_emi' => 'EMI Completion Reward',
    ];

    private const PERIODS = [
        'today' => 'Today',
        'yesterday' => 'Yesterday',
        '7d' => 'Last 7 days',
        '30d' => 'Last 30 days',
        'month' => 'This month',
        'last_month' => 'Last month',
        'all' => 'All time',
    ];

    // Pending requests older than this are flagged
    private const STALE_HOURS = 48;

    public function index(Request $request)
    {
        abort_unless(Auth::user()?->hasRole('admin'), 403);

        [$period, $from, $to, $periodLabel] = $this->resolvePeriod($request);
        [$prevFrom, $prevTo] = $this->previousPeriod($from, $to);

        $metrics = $this->metrics($from, $to);
        $previous = $prevFrom ? $this->metrics($prevFrom, $prevTo) : null;

        return view('admin.dashboard-new', [
            'period' => $period,
            'periods' => self::PERIODS,
            'periodLabel' => $periodLabel,
            'from' => $from,
            'to' => $to,
            'metrics' => $metrics,
            'previous' => $previous,
            'attention' => $this->attention(),
            'series' => $this->series($from, $to),
            'incomeBreakdown' => $this->incomeBreakdown($from, $to),
            'packages' => $this->packagePerformance($from, $to),
            'topEarners' => $this->topEarners($from, $to),
            'topSponsors' => $this->topSponsors($from, $to),
            'topWallets' => $this->topWallets(),
            'recentOrders' => $this->recentOrders(),
            'recentMembers' => $this->recentMembers(),
            'recentIncome' => $this->recentIncome(),
            'network' => $this->network(),
            'search' => trim((string) $request->input('q', '')),
            'searchResults' => $this->searchMembers(trim((string) $request->input('q', ''))),
            'inr' => fn($n, $decimals = 0) => self::inr($n, $decimals),
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Period handling
    |--------------------------------------------------------------------------
    */
    private function resolvePeriod(Request $request): array
    {
        $now = now();
        $period = $request->input('period', '30d');

        $isDate = fn($v) => is_string($v) && preg_match('/^\d{4}-\d{2}-\d{2}$/', $v);

        if ($period === 'custom' && $isDate($request->input('from')) && $isDate($request->input('to'))) {
            try {
                $from = Carbon::createFromFormat('Y-m-d', $request->input('from'))->startOfDay();
                $to = Carbon::createFromFormat('Y-m-d', $request->input('to'))->endOfDay();
                if ($from->gt($to)) {
                    [$from, $to] = [$to->copy()->startOfDay(), $from->copy()->endOfDay()];
                }
                return ['custom', $from, $to, $from->format('d M Y') . ' – ' . $to->format('d M Y')];
            } catch (\Throwable $e) {
                $period = '30d';
            }
        }

        [$from, $to] = match ($period) {
            'today' => [$now->copy()->startOfDay(), $now->copy()->endOfDay()],
            'yesterday' => [$now->copy()->subDay()->startOfDay(), $now->copy()->subDay()->endOfDay()],
            '7d' => [$now->copy()->subDays(6)->startOfDay(), $now->copy()->endOfDay()],
            'month' => [$now->copy()->startOfMonth(), $now->copy()->endOfDay()],
            'last_month' => [$now->copy()->subMonthNoOverflow()->startOfMonth(), $now->copy()->subMonthNoOverflow()->endOfMonth()],
            'all' => [null, null],
            default => [$now->copy()->subDays(29)->startOfDay(), $now->copy()->endOfDay()],
        };

        if (!array_key_exists($period, self::PERIODS)) {
            $period = '30d';
        }

        return [$period, $from, $to, self::PERIODS[$period]];
    }

    // Same-length period right before the selected one (none for "All time")
    private function previousPeriod(?Carbon $from, ?Carbon $to): array
    {
        if (!$from) {
            return [null, null];
        }

        $seconds = $from->diffInSeconds($to) + 1;

        return [$from->copy()->subSeconds($seconds), $from->copy()->subSecond()];
    }

    private function between($query, string $column, ?Carbon $from, ?Carbon $to)
    {
        return $from ? $query->whereBetween($column, [$from, $to]) : $query;
    }

    /*
    |--------------------------------------------------------------------------
    | Key numbers for a period
    |--------------------------------------------------------------------------
    */
    private function metrics(?Carbon $from, ?Carbon $to): array
    {
        $newMembers = $this->between(DB::table('users')->whereNotIn('id', self::EXCLUDED_USER_IDS), 'created_at', $from, $to)->count();

        $orders = $this->between(DB::table('orders')->where('status', 'completed'), 'created_at', $from, $to);
        $topups = (clone $orders)->count();
        $business = (float) (clone $orders)->sum('amount');

        // First-ever order of a member falls in the period = activation
        $firstOrders = DB::table('orders')->selectRaw('user_id, MIN(created_at) as first_at')->where('status', 'completed')->groupBy('user_id');
        $activations = $this->between(DB::query()->fromSub($firstOrders, 'f'), 'first_at', $from, $to)->count();

        $repurchases = $this->between(
            DB::table('orders as o1')
                ->where('o1.status', 'completed')
                ->whereExists(fn($q) => $q->from('orders as o2')->whereColumn('o2.user_id', 'o1.user_id')->whereColumn('o2.id', '<', 'o1.id')),
            'o1.created_at', $from, $to
        )->count();

        $fundsAdded = (float) $this->between(DB::table('fund_requests')->where('status', 'completed'), 'updated_at', $from, $to)->sum('amount');
        $payouts = $this->between(DB::table('withdraw_requests')->where('status', 'completed'), 'updated_at', $from, $to);
        $payoutsPaid = (float) (clone $payouts)->sum('amount');
        $payoutsNet = (float) (clone $payouts)->sum('net_amount');

        $incomePaid = (float) $this->between(DB::table('transactions')->whereIn('bonus_type', array_keys(self::INCOME_TYPES)), 'created_at', $from, $to)->sum('amount');

        return [
            'new_members' => $newMembers,
            'activations' => $activations,
            'topups' => $topups,
            'repurchases' => $repurchases,
            'business' => $business,
            'avg_ticket' => $topups ? $business / $topups : 0,
            'funds_added' => $fundsAdded,
            'payouts_paid' => $payoutsPaid,
            'payouts_net' => $payoutsNet,
            'income_paid' => $incomePaid,
            'payout_ratio' => $business > 0 ? $incomePaid / $business * 100 : 0,
            'net_cash' => $fundsAdded - $payoutsNet,
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Needs attention (always current, not period based)
    |--------------------------------------------------------------------------
    */
    private function attention(): array
    {
        $stale = now()->subHours(self::STALE_HOURS);

        $fundRequests = DB::table('fund_requests as f')
            ->leftJoin('users as u', 'u.id', '=', 'f.user_id')
            ->where('f.status', 'pending')
            ->orderBy('f.created_at')
            ->get(['f.*', 'u.member_id', 'u.name', 'u.username']);

        $withdrawals = DB::table('withdraw_requests as w')
            ->leftJoin('users as u', 'u.id', '=', 'w.user_id')
            ->leftJoin('wallets as wl', 'wl.user_id', '=', 'w.user_id')
            ->where('w.status', 'pending')
            ->orderBy('w.created_at')
            ->get(['w.*', 'u.member_id', 'u.name', 'u.username', 'u.bank_name', 'u.account_number', 'u.ifsc_code', 'wl.balance as wallet_balance']);

        $support = DB::table('queries as q')
            ->leftJoin('users as u', 'u.id', '=', 'q.user_id')
            ->where('q.status', 'Yet to review')
            ->orderBy('q.created_at')
            ->get(['q.id', 'q.subject', 'q.created_at', 'u.member_id', 'u.name']);

        $inactive = DB::table('users as u')
            ->whereNotIn('u.id', self::EXCLUDED_USER_IDS)
            ->where('u.created_at', '<', now()->subDays(7))
            ->whereNotExists(fn($q) => $q->from('orders')->whereColumn('orders.user_id', 'u.id'))
            ->whereExists(fn($q) => $q->from('model_has_roles as m')->join('roles as r', 'r.id', '=', 'm.role_id')->whereColumn('m.model_id', 'u.id')->where('r.name', 'customer'))
            ->count();

        return [
            'fund_requests' => $fundRequests,
            'fund_total' => (float) $fundRequests->sum('amount'),
            'fund_stale' => $fundRequests->filter(fn($r) => Carbon::parse($r->created_at)->lt($stale))->count(),
            'withdrawals' => $withdrawals,
            'withdraw_total' => (float) $withdrawals->sum('amount'),
            'withdraw_stale' => $withdrawals->filter(fn($r) => Carbon::parse($r->created_at)->lt($stale))->count(),
            'withdraw_short' => $withdrawals->filter(fn($r) => (float) $r->wallet_balance < (float) $r->amount)->count(),
            'support' => $support,
            'inactive_members' => $inactive,
            'lucky_active' => DB::table('lucky_cycles')->where('status', 'active')->count(),
            'negative_wallets' => DB::table('wallets')->where('balance', '<', 0)->count(),
            'stale_hours' => self::STALE_HOURS,
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Trend chart: daily (≤ 92 days) or monthly buckets
    |--------------------------------------------------------------------------
    */
    private function series(?Carbon $from, ?Carbon $to): array
    {
        $start = $from ?? Carbon::parse(DB::table('users')->min('created_at') ?? now())->startOfMonth();
        $end = $to ?? now()->endOfDay();
        $monthly = $start->diffInDays($end) > 92;

        $format = $monthly ? '%Y-%m' : '%Y-%m-%d';
        $bucket = fn($column) => DB::raw("DATE_FORMAT($column, '$format') as bucket");

        $collect = function ($query, string $column, string $aggregate) use ($bucket, $start, $end) {
            return $query->select($bucket($column), DB::raw("$aggregate as total"))
                ->whereBetween($column, [$start, $end])
                ->groupBy('bucket')
                ->pluck('total', 'bucket');
        };

        $business = $collect(DB::table('orders')->where('status', 'completed'), 'created_at', 'SUM(amount)');
        $members = $collect(DB::table('users')->whereNotIn('id', self::EXCLUDED_USER_IDS), 'created_at', 'COUNT(*)');
        $income = $collect(DB::table('transactions')->whereIn('bonus_type', array_keys(self::INCOME_TYPES)), 'created_at', 'SUM(amount)');
        $funds = $collect(DB::table('fund_requests')->where('status', 'completed'), 'updated_at', 'SUM(amount)');
        $payouts = $collect(DB::table('withdraw_requests')->where('status', 'completed'), 'updated_at', 'SUM(net_amount)');

        $labels = [];
        $keys = [];
        $cursor = $start->copy();
        while ($cursor->lte($end)) {
            $keys[] = $cursor->format($monthly ? 'Y-m' : 'Y-m-d');
            $labels[] = $cursor->format($monthly ? 'M Y' : 'd M');
            $monthly ? $cursor->addMonthNoOverflow()->startOfMonth() : $cursor->addDay();
        }

        $pick = fn($data) => array_map(fn($k) => round((float) ($data[$k] ?? 0), 2), $keys);

        return [
            'monthly' => $monthly,
            'labels' => $labels,
            'business' => $pick($business),
            'members' => $pick($members),
            'income' => $pick($income),
            'funds' => $pick($funds),
            'payouts' => $pick($payouts),
        ];
    }

    private function incomeBreakdown(?Carbon $from, ?Carbon $to): array
    {
        $rows = $this->between(DB::table('transactions')->whereIn('bonus_type', array_keys(self::INCOME_TYPES)), 'created_at', $from, $to)
            ->selectRaw('bonus_type, COUNT(*) as entries, SUM(amount) as total, COUNT(DISTINCT user_id) as earners')
            ->groupBy('bonus_type')
            ->orderByDesc('total')
            ->get();

        return $rows->map(fn($r) => [
            'type' => $r->bonus_type,
            'label' => self::INCOME_TYPES[$r->bonus_type] ?? $r->bonus_type,
            'entries' => (int) $r->entries,
            'earners' => (int) $r->earners,
            'total' => (float) $r->total,
        ])->all();
    }

    /*
    |--------------------------------------------------------------------------
    | Packages: sales in period + all time, with their current settings
    |--------------------------------------------------------------------------
    */
    private function packagePerformance(?Carbon $from, ?Carbon $to): array
    {
        $period = $this->between(DB::table('orders')->where('status', 'completed'), 'created_at', $from, $to)
            ->selectRaw('package_id, COUNT(*) as qty, SUM(amount) as revenue, COUNT(DISTINCT user_id) as buyers')
            ->groupBy('package_id')
            ->get()
            ->keyBy('package_id');

        $allTime = DB::table('orders')->where('status', 'completed')
            ->selectRaw('package_id, COUNT(*) as qty, SUM(amount) as revenue, COUNT(DISTINCT user_id) as buyers')
            ->groupBy('package_id')
            ->get()
            ->keyBy('package_id');

        $periodRevenue = (float) $period->sum('revenue');

        return Package::orderBy('id')->get()->map(function ($p) use ($period, $allTime, $periodRevenue) {
            $now = $period[$p->id] ?? null;
            $all = $allTime[$p->id] ?? null;
            $isStarter = Package::isStarter($p);

            return [
                'id' => $p->id,
                'name' => $p->name,
                'price' => (float) $p->actual_amount,
                'qty' => (int) ($now->qty ?? 0),
                'revenue' => (float) ($now->revenue ?? 0),
                'buyers' => (int) ($now->buyers ?? 0),
                'share' => $periodRevenue > 0 ? ($now->revenue ?? 0) / $periodRevenue * 100 : 0,
                'all_qty' => (int) ($all->qty ?? 0),
                'all_revenue' => (float) ($all->revenue ?? 0),
                'direct' => Package::formatBonus($p->direct_bonus, $p->direct_bonus_type),
                'pair' => Package::formatBonus($p->pair_bonus, $p->pair_bonus_type),
                'reg_fee' => (bool) $p->charges_registration_fee,
                'pairs' => $isStarter
                    ? trim('₹' . number_format($p->actual_amount) . ': ' . ($p->pairingSummary('first') ?: '—') . ' · ₹' . number_format($p->discounted_amount ?: 1000) . ': ' . ($p->pairingSummary('repeat') ?: '—'))
                    : ($p->pairingSummary() ?: '—'),
            ];
        })->all();
    }

    /*
    |--------------------------------------------------------------------------
    | Leaderboards
    |--------------------------------------------------------------------------
    */
    private function topEarners(?Carbon $from, ?Carbon $to)
    {
        return $this->between(DB::table('transactions as t')->join('users as u', 'u.id', '=', 't.user_id')->whereIn('t.bonus_type', array_keys(self::INCOME_TYPES)), 't.created_at', $from, $to)
            ->selectRaw('u.id, u.member_id, u.name, SUM(t.amount) as total, COUNT(*) as entries')
            ->groupBy('u.id', 'u.member_id', 'u.name')
            ->orderByDesc('total')
            ->limit(10)
            ->get();
    }

    private function topSponsors(?Carbon $from, ?Carbon $to)
    {
        return $this->between(DB::table('users as d')->join('users as s', 's.id', '=', 'd.sponsor_id')->whereNotIn('d.id', self::EXCLUDED_USER_IDS), 'd.created_at', $from, $to)
            ->selectRaw('s.id, s.member_id, s.name, COUNT(*) as directs')
            ->addSelect(['business' => DB::table('orders as o')->join('users as x', 'x.id', '=', 'o.user_id')->whereColumn('x.sponsor_id', 's.id')->where('o.status', 'completed')->selectRaw('COALESCE(SUM(o.amount), 0)')])
            ->groupBy('s.id', 's.member_id', 's.name')
            ->orderByDesc('directs')
            ->limit(10)
            ->get();
    }

    private function topWallets()
    {
        return DB::table('wallets as w')->join('users as u', 'u.id', '=', 'w.user_id')
            ->where('w.balance', '>', 0)
            ->orderByDesc('w.balance')
            ->limit(10)
            ->get(['u.id', 'u.member_id', 'u.name', 'w.balance']);
    }

    /*
    |--------------------------------------------------------------------------
    | Recent activity
    |--------------------------------------------------------------------------
    */
    private function recentOrders()
    {
        return DB::table('orders as o')
            ->leftJoin('users as u', 'u.id', '=', 'o.user_id')
            ->leftJoin('users as b', 'b.id', '=', 'o.from_user_id')
            ->leftJoin('packages as p', 'p.id', '=', 'o.package_id')
            ->orderByDesc('o.id')
            ->limit(10)
            ->get(['o.id', 'o.amount', 'o.payment_by', 'o.created_at', 'u.id as user_id', 'u.member_id', 'u.name', 'b.member_id as paid_by', 'p.name as package']);
    }

    private function recentMembers()
    {
        return DB::table('users as u')
            ->leftJoin('users as s', 's.id', '=', 'u.sponsor_id')
            ->whereNotIn('u.id', self::EXCLUDED_USER_IDS)
            ->orderByDesc('u.id')
            ->limit(8)
            ->get(['u.id', 'u.member_id', 'u.name', 'u.position', 'u.created_at', 'u.investment_count', 's.member_id as sponsor']);
    }

    private function recentIncome()
    {
        return DB::table('transactions as t')
            ->leftJoin('users as u', 'u.id', '=', 't.user_id')
            ->whereIn('t.bonus_type', array_keys(self::INCOME_TYPES))
            ->orderByDesc('t.id')
            ->limit(10)
            ->get(['t.amount', 't.bonus_type', 't.created_at', 'u.id as user_id', 'u.member_id', 'u.name'])
            ->map(function ($t) {
                $t->label = self::INCOME_TYPES[$t->bonus_type] ?? $t->bonus_type;
                return $t;
            });
    }

    /*
    |--------------------------------------------------------------------------
    | Network snapshot (current)
    |--------------------------------------------------------------------------
    */
    private function network(): array
    {
        $members = DB::table('users as u')
            ->whereNotIn('u.id', self::EXCLUDED_USER_IDS)
            ->whereExists(fn($q) => $q->from('model_has_roles as m')->join('roles as r', 'r.id', '=', 'm.role_id')->whereColumn('m.model_id', 'u.id')->where('r.name', 'customer'));

        $total = (clone $members)->count();
        $paid = (clone $members)->whereExists(fn($q) => $q->from('orders')->whereColumn('orders.user_id', 'u.id'))->count();

        return [
            'total' => $total,
            'paid' => $paid,
            'inactive' => $total - $paid,
            'wallet_total' => (float) DB::table('wallets')->sum('balance'),
            'wallets_funded' => DB::table('wallets')->where('balance', '>', 0)->count(),
            'emi_completed' => (clone $members)->where('u.emi_status', 'completed')->count(),
            'emi_ongoing' => (clone $members)->where('u.emi_status', 'ongoing')->whereExists(fn($q) => $q->from('orders')->whereColumn('orders.user_id', 'u.id'))->count(),
            'states' => (clone $members)
                ->selectRaw("COALESCE(NULLIF(TRIM(u.state), ''), 'Not set') as state, COUNT(*) as total")
                ->groupBy('state')
                ->orderByDesc('total')
                ->limit(8)
                ->pluck('total', 'state'),
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Member lookup
    |--------------------------------------------------------------------------
    */
    private function searchMembers(string $term)
    {
        if (mb_strlen($term) < 2) {
            return collect();
        }

        $like = '%' . str_replace(['%', '_'], ['\%', '\_'], $term) . '%';

        return DB::table('users as u')
            ->leftJoin('wallets as w', 'w.user_id', '=', 'u.id')
            ->leftJoin('users as s', 's.id', '=', 'u.sponsor_id')
            ->where(fn($q) => $q->where('u.member_id', 'like', $like)->orWhere('u.name', 'like', $like)->orWhere('u.username', 'like', $like)->orWhere('u.mobile', 'like', $like)->orWhere('u.email', 'like', $like))
            ->orderBy('u.id')
            ->limit(15)
            ->get(['u.id', 'u.member_id', 'u.name', 'u.mobile', 'u.email', 'u.created_at', 'u.investment_count', 'u.position', 'w.balance', 's.member_id as sponsor'])
            ->map(function ($u) {
                $u->invested = (float) DB::table('orders')->where('user_id', $u->id)->where('status', 'completed')->sum('amount');
                $u->income = (float) DB::table('transactions')->where('user_id', $u->id)->whereIn('bonus_type', array_keys(self::INCOME_TYPES))->sum('amount');
                return $u;
            });
    }

    // Indian number format: 12,34,567
    public static function inr($number, int $decimals = 0): string
    {
        $number = round((float) $number, $decimals);
        $negative = $number < 0;
        $parts = explode('.', number_format(abs($number), $decimals, '.', ''));
        $int = $parts[0];

        if (strlen($int) > 3) {
            $last3 = substr($int, -3);
            $rest = substr($int, 0, -3);
            $rest = preg_replace('/\B(?=(\d{2})+(?!\d))/', ',', $rest);
            $int = $rest . ',' . $last3;
        }

        return ($negative ? '-' : '') . $int . (isset($parts[1]) ? '.' . $parts[1] : '');
    }
}
