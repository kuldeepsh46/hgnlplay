<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Database\Query\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

/*
|--------------------------------------------------------------------------
| Sponsor Binary Bonus ledger (admin)
|--------------------------------------------------------------------------
| Read-only view of sponsor_binary_bonus_payouts: for every payout, which
| downline's pair income it came from, which sponsor received it, the
| income day, and when it was credited.
|--------------------------------------------------------------------------
*/
class SponsorBonusController extends Controller
{
    private const PER_PAGE = 25;

    public function index(Request $request)
    {
        abort_unless(Auth::user()?->hasRole('admin'), 403);

        $filters = $this->filters($request);
        $query = $this->query($filters);

        // select() replaces the row columns, so only the totals are queried
        $summary = (clone $query)->reorder()->select(DB::raw(
            'COUNT(*) as payouts, COALESCE(SUM(p.bonus_amount), 0) as total_bonus, COALESCE(SUM(p.binary_income), 0) as total_income, COUNT(DISTINCT p.sponsor_id) as sponsors, COUNT(DISTINCT p.source_user_id) as downlines'
        ))->first();

        $rows = $query->paginate(self::PER_PAGE)->appends(array_filter($filters));

        return view('admin.sponsor-bonus', compact('rows', 'summary', 'filters'));
    }

    public function export(Request $request)
    {
        abort_unless(Auth::user()?->hasRole('admin'), 403);

        $filters = $this->filters($request);
        $query = $this->query($filters);

        $filename = 'sponsor_binary_bonus_' . ($filters['from'] ?: 'start') . '_to_' . ($filters['to'] ?: 'today') . '.csv';

        return response()->stream(function () use ($query) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['Income Date', 'Credited On', 'Sponsor ID', 'Sponsor Name', 'From Member ID', 'From Member Name', 'Pair Income', 'Counted (max 5000)', 'Bonus (10%)', 'Transaction ID']);

            $total = 0;
            foreach ($query->cursor() as $row) {
                fputcsv($file, [
                    $row->income_date,
                    $row->credited_at,
                    $row->sponsor_member_id,
                    $row->sponsor_name,
                    $row->source_member_id,
                    $row->source_name,
                    $row->binary_income,
                    $row->capped_income,
                    $row->bonus_amount,
                    $row->transaction_id,
                ]);
                $total += $row->bonus_amount;
            }

            fputcsv($file, ['Total', '', '', '', '', '', '', '', number_format($total, 2, '.', ''), '']);
            fclose($file);
        }, 200, [
            'Content-type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ]);
    }

    private function filters(Request $request): array
    {
        $validated = $request->validate([
            'from' => ['nullable', 'date_format:Y-m-d'],
            'to' => ['nullable', 'date_format:Y-m-d', 'after_or_equal:from'],
            'member' => ['nullable', 'string', 'max:100'],
        ]);

        return [
            'from' => $validated['from'] ?? null,
            'to' => $validated['to'] ?? null,
            'member' => isset($validated['member']) ? trim($validated['member']) : null,
        ];
    }

    private function query(array $filters): Builder
    {
        $query = DB::table('sponsor_binary_bonus_payouts as p')
            ->leftJoin('users as s', 's.id', '=', 'p.sponsor_id')
            ->leftJoin('users as d', 'd.id', '=', 'p.source_user_id')
            ->select(
                'p.id',
                'p.income_date',
                'p.created_at as credited_at',
                'p.binary_income',
                'p.capped_income',
                'p.bonus_amount',
                'p.transaction_id',
                's.member_id as sponsor_member_id',
                's.name as sponsor_name',
                'd.member_id as source_member_id',
                'd.name as source_name'
            )
            ->orderByDesc('p.income_date')
            ->orderByDesc('p.id');

        if ($filters['from']) {
            $query->where('p.income_date', '>=', $filters['from']);
        }

        if ($filters['to']) {
            $query->where('p.income_date', '<=', $filters['to']);
        }

        // One box finds a member on either side: as the sponsor who
        // received the bonus, or as the downline it came from.
        if ($filters['member']) {
            $like = '%' . $filters['member'] . '%';
            $query->where(function ($q) use ($like) {
                $q->where('s.member_id', 'like', $like)
                    ->orWhere('s.name', 'like', $like)
                    ->orWhere('s.username', 'like', $like)
                    ->orWhere('d.member_id', 'like', $like)
                    ->orWhere('d.name', 'like', $like)
                    ->orWhere('d.username', 'like', $like);
            });
        }

        return $query;
    }
}
