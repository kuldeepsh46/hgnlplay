<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Database\Query\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

/*
|--------------------------------------------------------------------------
| All Transactions (admin)
|--------------------------------------------------------------------------
| Read-only audit view of every money movement in the system:
|   - wallet ledger   (transactions: every credit/debit with its reason)
|   - top-ups         (orders: package purchases, who paid for whom)
|   - withdrawals     (withdraw_requests: amount, tax, net, status)
|   - fund requests   (fund_requests: deposits members asked to add)
| Nothing here writes to the database.
|--------------------------------------------------------------------------
*/
class TransactionLedgerController extends Controller
{
    private const PER_PAGE = 25;

    public const TABS = [
        'wallet' => 'Wallet Ledger',
        'topups' => 'Top-ups / Packages',
        'withdrawals' => 'Withdrawals',
        'funds' => 'Fund Requests',
    ];

    // Label and plain-language explanation for every bonus_type in transactions
    public const TYPES = [
        'pair_bonus_normal' => ['Pair Income', 'Binary pair income: a % of newly matched left/right business volume, from the package settings. Max ₹5,000 per member per day, shared with Starter pair income.'],
        'pair_bonus_starter' => ['Starter Pair Income', 'Flat pair bonus for Starter package matches (e.g. ₹300 per matched ₹1,600 pair). Shares the ₹5,000 daily cap with Pair Income.'],
        'pair_bonus_2000' => ['Pair Bonus 2000', 'Pair bonus from the older ₹2,000 package scheme.'],
        'pair_bonus' => ['Pair Bonus (old)', 'Old fixed pair bonus, paid once when both direct legs reached 3 investments.'],
        'sponsor_binary_bonus' => ['Sponsor Binary Bonus', '10% of a direct downline\'s pair income for one day (counted up to ₹5,000), paid to their sponsor by the nightly 00:10 IST job.'],
        'direct_income' => ['Direct Income', 'Commission to the sponsor (and indirect uplines) when a member buys or tops up a package. The % comes from the package settings.'],
        'commission' => ['Level Commission', 'Level commission (L1, L2 ...) paid up the sponsor line on a top-up.'],
        'level_income' => ['Level Income', 'Matrix level income paid when a member in the receiver\'s matrix tops up. Remarks show the product user, receiver and tier.'],
        'emi_payment' => ['EMI / Top-up Payment', 'Wallet debit used to pay an EMI or top-up for a member.'],
        'fund_request' => ['Fund Request', 'Money added to the wallet after admin approved a fund request.'],
        'fund_request_approved' => ['Fund Request Approved', 'Money added to the wallet after admin approved a fund request.'],
        'payout' => ['Payout / Withdrawal', 'Wallet debit when admin approved a withdrawal.'],
        'withdrawal' => ['Withdrawal', 'Wallet debit for a withdrawal.'],
        'reward' => ['Reward', 'Reward credit, e.g. for completing all 16 EMIs.'],
        'reward_after_full_emi' => ['EMI Completion Reward', 'Reward credited after completing all 16 EMIs.'],
        'other' => ['Other', 'Other credit or debit.'],
        '__none' => ['Unclassified', 'Older entry saved without a type. The remarks describe what it was.'],
    ];

    public function index(Request $request)
    {
        abort_unless(Auth::user()?->hasRole('admin'), 403);

        $tab = array_key_exists($request->query('tab'), self::TABS) ? $request->query('tab') : 'wallet';
        $filters = $this->filters($request);
        $query = $this->queryFor($tab, $filters);

        $summary = $this->summaryFor($tab, clone $query);
        $breakdown = $tab === 'wallet' ? $this->walletBreakdown(clone $query) : collect();

        $rows = $query->paginate(self::PER_PAGE)->appends(array_filter($filters + ['tab' => $tab], fn ($v) => $v !== null && $v !== '' && $v !== []));

        $typeOptions = $this->walletTypeOptions();

        return view('admin.transactions.index', compact('tab', 'filters', 'rows', 'summary', 'breakdown', 'typeOptions'));
    }

    public function show(int $id)
    {
        abort_unless(Auth::user()?->hasRole('admin'), 403);

        $txn = DB::table('transactions')->where('id', $id)->first();
        abort_unless($txn, 404);

        $member = DB::table('users')->where('id', $txn->user_id)->first();
        $sponsor = $member && $member->sponsor_id ? DB::table('users')->where('id', $member->sponsor_id)->first() : null;
        $placement = $member && $member->placement_id ? DB::table('users')->where('id', $member->placement_id)->first() : null;
        $walletBalance = DB::table('wallets')->where('user_id', $txn->user_id)->value('balance');

        $typeKey = $txn->bonus_type ?: '__none';
        [$typeLabel, $typeHow] = self::TYPES[$typeKey] ?? [ucwords(str_replace('_', ' ', $typeKey)), 'No description for this type yet. See the remarks.'];

        // Exact source record for sponsor bonus payouts
        $sponsorPayout = $txn->bonus_type === 'sponsor_binary_bonus'
            ? DB::table('sponsor_binary_bonus_payouts as p')
                ->leftJoin('users as d', 'd.id', '=', 'p.source_user_id')
                ->where('p.transaction_id', $txn->id)
                ->first(['p.*', 'd.member_id as source_member_id', 'd.name as source_name'])
            : null;

        // Everything else that happened to this member around the same moment
        // (a top-up writes its debit, order and commissions within seconds)
        $from = date('Y-m-d H:i:s', strtotime($txn->created_at) - 120);
        $to = date('Y-m-d H:i:s', strtotime($txn->created_at) + 120);

        $nearbyOrders = DB::table('orders as o')
            ->leftJoin('packages as p', 'p.id', '=', 'o.package_id')
            ->leftJoin('users as u', 'u.id', '=', 'o.user_id')
            ->leftJoin('users as f', 'f.id', '=', 'o.from_user_id')
            ->where(fn ($q) => $q->where('o.user_id', $txn->user_id)->orWhere('o.from_user_id', $txn->user_id))
            ->whereBetween('o.created_at', [$from, $to])
            ->orderBy('o.id')
            ->get(['o.*', 'p.name as package_name', 'u.member_id as for_member_id', 'u.name as for_name', 'f.member_id as by_member_id', 'f.name as by_name']);

        $nearbyTxns = DB::table('transactions as t')
            ->leftJoin('users as u', 'u.id', '=', 't.user_id')
            ->whereBetween('t.created_at', [$from, $to])
            ->where('t.id', '!=', $txn->id)
            ->orderBy('t.id')
            ->limit(50)
            ->get(['t.*', 'u.member_id', 'u.name']);

        $recent = DB::table('transactions')
            ->where('user_id', $txn->user_id)
            ->orderByDesc('id')
            ->limit(15)
            ->get();

        return view('admin.transactions.show', compact(
            'txn', 'member', 'sponsor', 'placement', 'walletBalance', 'typeLabel', 'typeHow',
            'sponsorPayout', 'nearbyOrders', 'nearbyTxns', 'recent'
        ));
    }

    public function export(Request $request)
    {
        abort_unless(Auth::user()?->hasRole('admin'), 403);

        $tab = array_key_exists($request->query('tab'), self::TABS) ? $request->query('tab') : 'wallet';
        $filters = $this->filters($request);
        $query = $this->queryFor($tab, $filters);

        [$headers, $map] = $this->csvColumns($tab);
        $filename = $tab . '_transactions_' . ($filters['from'] ?: 'start') . '_to_' . ($filters['to'] ?: 'today') . '.csv';

        return response()->stream(function () use ($query, $headers, $map) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $headers);
            foreach ($query->cursor() as $row) {
                fputcsv($file, $map($row));
            }
            fclose($file);
        }, 200, [
            'Content-type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ]);
    }

    public static function typeLabel(?string $bonusType): string
    {
        $key = $bonusType ?: '__none';

        return self::TYPES[$key][0] ?? ucwords(str_replace('_', ' ', $key));
    }

    private function filters(Request $request): array
    {
        $v = $request->validate([
            'from' => ['nullable', 'date_format:Y-m-d'],
            'to' => ['nullable', 'date_format:Y-m-d', 'after_or_equal:from'],
            'member' => ['nullable', 'string', 'max:100'],
            'types' => ['nullable', 'array'],
            'types.*' => ['string', 'max:64'],
            'direction' => ['nullable', 'in:credit,debit'],
            'status' => ['nullable', 'string', 'max:32'],
            'min' => ['nullable', 'numeric'],
            'max' => ['nullable', 'numeric'],
            'q' => ['nullable', 'string', 'max:200'],
        ]);

        return [
            'from' => $v['from'] ?? null,
            'to' => $v['to'] ?? null,
            'member' => isset($v['member']) ? trim($v['member']) : null,
            'types' => $v['types'] ?? [],
            'direction' => $v['direction'] ?? null,
            'status' => $v['status'] ?? null,
            'min' => $v['min'] ?? null,
            'max' => $v['max'] ?? null,
            'q' => isset($v['q']) ? trim($v['q']) : null,
        ];
    }

    private function queryFor(string $tab, array $f): Builder
    {
        return match ($tab) {
            'topups' => $this->topupsQuery($f),
            'withdrawals' => $this->withdrawalsQuery($f),
            'funds' => $this->fundsQuery($f),
            default => $this->walletQuery($f),
        };
    }

    // Old rows store type as Credit/credit/Debit (and a few as 'pair_bonus'),
    // so direction is "debit" only when type says so, otherwise credit.
    private const DEBIT_SQL = "LOWER(t.type) = 'debit'";

    private function walletQuery(array $f): Builder
    {
        $q = DB::table('transactions as t')
            ->leftJoin('users as u', 'u.id', '=', 't.user_id')
            ->select('t.id', 't.user_id', 't.type', 't.bonus_type', 't.amount', 't.remarks', 't.created_at', 'u.member_id', 'u.name', 'u.username')
            ->selectRaw('CASE WHEN ' . self::DEBIT_SQL . " THEN 'debit' ELSE 'credit' END as direction")
            ->orderByDesc('t.id');

        $this->dateFilter($q, 't.created_at', $f);
        $this->memberFilter($q, 'u', $f['member']);

        if ($f['types']) {
            $types = array_values(array_diff($f['types'], ['__none']));
            $q->where(function ($w) use ($types, $f) {
                if ($types) {
                    $w->whereIn('t.bonus_type', $types);
                }
                if (in_array('__none', $f['types'], true)) {
                    $w->orWhereNull('t.bonus_type')->orWhere('t.bonus_type', '');
                }
            });
        }

        if ($f['direction'] === 'debit') {
            $q->whereRaw(self::DEBIT_SQL);
        } elseif ($f['direction'] === 'credit') {
            $q->whereRaw('NOT (' . self::DEBIT_SQL . ')');
        }

        $this->amountFilter($q, 't.amount', $f);

        if ($f['q']) {
            $q->where('t.remarks', 'like', '%' . $f['q'] . '%');
        }

        return $q;
    }

    private function topupsQuery(array $f): Builder
    {
        $q = DB::table('orders as o')
            ->leftJoin('users as u', 'u.id', '=', 'o.user_id')
            ->leftJoin('users as b', 'b.id', '=', 'o.from_user_id')
            ->leftJoin('packages as p', 'p.id', '=', 'o.package_id')
            ->select('o.id', 'o.amount', 'o.payment_by', 'o.status', 'o.created_at', 'p.name as package_name',
                'u.id as user_id', 'u.member_id', 'u.name', 'b.member_id as by_member_id', 'b.name as by_name')
            ->orderByDesc('o.id');

        $this->dateFilter($q, 'o.created_at', $f);
        if ($f['member']) {
            $like = '%' . $f['member'] . '%';
            $q->where(fn ($w) => $w->where('u.member_id', 'like', $like)->orWhere('u.name', 'like', $like)->orWhere('u.username', 'like', $like)
                ->orWhere('b.member_id', 'like', $like)->orWhere('b.name', 'like', $like)->orWhere('b.username', 'like', $like));
        }
        if ($f['status']) {
            $q->where('o.status', $f['status']);
        }
        $this->amountFilter($q, 'o.amount', $f);
        if ($f['q']) {
            $q->where(fn ($w) => $w->where('p.name', 'like', '%' . $f['q'] . '%')->orWhere('o.payment_by', 'like', '%' . $f['q'] . '%'));
        }

        return $q;
    }

    private function withdrawalsQuery(array $f): Builder
    {
        $q = DB::table('withdraw_requests as w')
            ->leftJoin('users as u', 'u.id', '=', 'w.user_id')
            ->select('w.id', 'w.amount', 'w.tax_amount', 'w.net_amount', 'w.status', 'w.created_at', 'w.updated_at',
                'u.id as user_id', 'u.member_id', 'u.name', 'u.bank_name', 'u.account_number', 'u.ifsc_code')
            ->orderByDesc('w.id');

        $this->dateFilter($q, 'w.created_at', $f);
        $this->memberFilter($q, 'u', $f['member']);
        if ($f['status']) {
            $q->where('w.status', $f['status']);
        }
        $this->amountFilter($q, 'w.amount', $f);

        return $q;
    }

    private function fundsQuery(array $f): Builder
    {
        $q = DB::table('fund_requests as r')
            ->leftJoin('users as u', 'u.id', '=', 'r.user_id')
            ->select('r.id', 'r.amount', 'r.deposit_date', 'r.payment_mode', 'r.bank_name', 'r.account_number', 'r.transaction_remark',
                'r.attachment', 'r.status', 'r.created_at', 'r.updated_at', 'u.id as user_id', 'u.member_id', 'u.name')
            ->orderByDesc('r.id');

        $this->dateFilter($q, 'r.created_at', $f);
        $this->memberFilter($q, 'u', $f['member']);
        if ($f['status']) {
            $q->where('r.status', $f['status']);
        }
        $this->amountFilter($q, 'r.amount', $f);
        if ($f['q']) {
            $q->where(fn ($w) => $w->where('r.transaction_remark', 'like', '%' . $f['q'] . '%')->orWhere('r.payment_mode', 'like', '%' . $f['q'] . '%'));
        }

        return $q;
    }

    private function dateFilter(Builder $q, string $col, array $f): void
    {
        if ($f['from']) {
            $q->where($col, '>=', $f['from'] . ' 00:00:00');
        }
        if ($f['to']) {
            $q->where($col, '<=', $f['to'] . ' 23:59:59');
        }
    }

    private function memberFilter(Builder $q, string $alias, ?string $member): void
    {
        if (!$member) {
            return;
        }
        $like = '%' . $member . '%';
        $q->where(fn ($w) => $w->where("$alias.member_id", 'like', $like)->orWhere("$alias.name", 'like', $like)->orWhere("$alias.username", 'like', $like));
    }

    private function amountFilter(Builder $q, string $col, array $f): void
    {
        if ($f['min'] !== null && $f['min'] !== '') {
            $q->where($col, '>=', $f['min']);
        }
        if ($f['max'] !== null && $f['max'] !== '') {
            $q->where($col, '<=', $f['max']);
        }
    }

    private function summaryFor(string $tab, Builder $q): array
    {
        $q->reorder();

        if ($tab === 'wallet') {
            $r = $q->select(DB::raw('COUNT(*) as n, COALESCE(SUM(CASE WHEN ' . self::DEBIT_SQL . ' THEN 0 ELSE t.amount END), 0) as credits, COALESCE(SUM(CASE WHEN ' . self::DEBIT_SQL . ' THEN t.amount ELSE 0 END), 0) as debits, COUNT(DISTINCT t.user_id) as members'))->first();

            return ['Entries' => number_format($r->n), 'Total credited' => '₹' . number_format($r->credits, 2), 'Total debited' => '₹' . number_format($r->debits, 2), 'Net' => '₹' . number_format($r->credits - $r->debits, 2), 'Members' => number_format($r->members)];
        }

        $alias = ['topups' => 'o', 'withdrawals' => 'w', 'funds' => 'r'][$tab];
        $rows = $q->select(DB::raw("$alias.status as status, COUNT(*) as n, COALESCE(SUM($alias.amount), 0) as total"))->groupBy("$alias.status")->get();

        $out = ['Entries' => number_format($rows->sum('n')), 'Total amount' => '₹' . number_format($rows->sum('total'), 2)];
        foreach ($rows as $r) {
            $out[ucfirst($r->status ?: 'unknown')] = number_format($r->n) . ' · ₹' . number_format($r->total, 2);
        }

        return $out;
    }

    private function walletBreakdown(Builder $q)
    {
        return $q->reorder()
            ->select(DB::raw("COALESCE(NULLIF(t.bonus_type, ''), '__none') as bt, CASE WHEN " . self::DEBIT_SQL . " THEN 'debit' ELSE 'credit' END as dir, COUNT(*) as n, SUM(t.amount) as total"))
            ->groupBy('bt', 'dir')
            ->orderByDesc('total')
            ->get();
    }

    private function walletTypeOptions(): array
    {
        $present = DB::table('transactions')->selectRaw("DISTINCT COALESCE(NULLIF(bonus_type, ''), '__none') as bt")->pluck('bt')->all();
        $keys = array_unique(array_merge(array_keys(self::TYPES), $present));
        $options = [];
        foreach ($keys as $k) {
            $options[$k] = self::typeLabel($k === '__none' ? null : $k);
        }
        asort($options);

        return $options;
    }

    private function csvColumns(string $tab): array
    {
        return match ($tab) {
            'topups' => [
                ['Order ID', 'Date', 'For Member ID', 'For Member', 'Paid By ID', 'Paid By', 'Package', 'Amount', 'Payment By', 'Status'],
                fn ($r) => [$r->id, $r->created_at, $r->member_id, $r->name, $r->by_member_id, $r->by_name, $r->package_name, $r->amount, $r->payment_by, $r->status],
            ],
            'withdrawals' => [
                ['Request ID', 'Requested', 'Last Updated', 'Member ID', 'Member', 'Amount', 'Tax', 'Net', 'Status', 'Bank', 'Account', 'IFSC'],
                fn ($r) => [$r->id, $r->created_at, $r->updated_at, $r->member_id, $r->name, $r->amount, $r->tax_amount, $r->net_amount, $r->status, $r->bank_name, $r->account_number, $r->ifsc_code],
            ],
            'funds' => [
                ['Request ID', 'Requested', 'Last Updated', 'Member ID', 'Member', 'Amount', 'Deposit Date', 'Mode', 'Bank', 'Account', 'Remark', 'Status'],
                fn ($r) => [$r->id, $r->created_at, $r->updated_at, $r->member_id, $r->name, $r->amount, $r->deposit_date, $r->payment_mode, $r->bank_name, $r->account_number, $r->transaction_remark, $r->status],
            ],
            default => [
                ['Txn ID', 'Date', 'Member ID', 'Member', 'Type', 'Direction', 'Amount', 'Remarks (why)'],
                fn ($r) => [$r->id, $r->created_at, $r->member_id, $r->name, self::typeLabel($r->bonus_type), $r->direction, $r->amount, $r->remarks],
            ],
        };
    }
}
