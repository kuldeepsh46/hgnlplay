<?php

namespace App\Http\Controllers;

use App\Enums\BonusType;
use Illuminate\Database\Query\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    private const PER_PAGE = 10;

    public function index(Request $request)
    {
        $user = Auth::user();
        [$from, $to] = $this->validatedDates($request);

        $matchQuery       = $this->baseQuery($user->id)->whereIn('bonus_type', BonusType::pairTypes());
        $directQuery      = $this->baseQuery($user->id)->where('bonus_type', BonusType::DirectIncome->value);
        $levelIncomeQuery = $this->baseQuery($user->id)->where('bonus_type', BonusType::LevelIncome->value);

        foreach ([$matchQuery, $directQuery, $levelIncomeQuery] as $query) {
            $this->applyDateFilter($query, $from, $to);
        }

        // Summed from clones before paginating. Summing the paginator instead
        // would only ever total the current page's rows.
        $matchingTotal = (clone $matchQuery)->sum('amount');
        $directTotal   = (clone $directQuery)->sum('amount');
        $levelTotal    = (clone $levelIncomeQuery)->sum('amount');
        $grandTotal    = $matchingTotal + $directTotal + $levelTotal;

        // Shows which pair types make up the total, so a type that stops
        // contributing is visible instead of silently missing.
        $pairBreakdown = (clone $matchQuery)
            ->reorder()
            ->select('bonus_type', DB::raw('SUM(amount) as total'))
            ->groupBy('bonus_type')
            ->pluck('total', 'bonus_type');

        // Only the real filters — appending the whole request would carry the
        // page keys across and make the three paginators fight each other.
        $filters = array_filter(['from' => $from, 'to' => $to]);

        $matchingIncomes = $matchQuery->paginate(self::PER_PAGE, ['*'], 'match_page')->appends($filters);
        $directIncomes   = $directQuery->paginate(self::PER_PAGE, ['*'], 'direct_page')->appends($filters);
        $levelIncomes    = $levelIncomeQuery->paginate(self::PER_PAGE, ['*'], 'level_page')->appends($filters);

        return view('reports.index', compact(
            'user',
            'matchingIncomes', 'directIncomes', 'levelIncomes',
            'matchingTotal', 'directTotal', 'levelTotal', 'grandTotal',
            'pairBreakdown',
            'from', 'to'
        ));
    }

    public function export(Request $request, $type)
    {
        $user = Auth::user();
        [$from, $to] = $this->validatedDates($request);

        $query = $this->baseQuery($user->id);

        if ($type === 'matching') {
            $query->whereIn('bonus_type', BonusType::pairTypes());
            $label = 'matching';
        } elseif ($type === 'level') {
            $query->where('bonus_type', BonusType::LevelIncome->value);
            $label = 'level_income';
        } elseif ($type === 'direct') {
            $query->where('bonus_type', BonusType::DirectIncome->value);
            $label = 'direct_income';
        } else {
            abort(404);
        }

        // Same filter helper as the screen, so the CSV can never disagree
        // with what the user is looking at.
        $this->applyDateFilter($query, $from, $to);

        if ($from && $to) {
            $filename = "{$label}_report_{$from}_to_{$to}.csv";
        } elseif ($from || $to) {
            $filename = "{$label}_report_" . ($from ?: $to) . ".csv";
        } else {
            $filename = "{$label}_report_all_time.csv";
        }

        $headers = [
            'Content-type'        => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Pragma'              => 'no-cache',
            'Cache-Control'       => 'must-revalidate, post-check=0, pre-check=0',
            'Expires'             => '0',
        ];

        $callback = function () use ($query) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['Date', 'Amount', 'Remarks']);

            $total = 0;

            // cursor() streams row by row instead of loading the whole
            // result set into memory before the response starts.
            foreach ($query->cursor() as $row) {
                fputcsv($file, [
                    date('d M Y, h:i A', strtotime($row->created_at)),
                    $row->amount,
                    $row->remarks,
                ]);
                $total += $row->amount;
            }

            fputcsv($file, ['Total', number_format($total, 2), '']);
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    private function baseQuery(int $userId): Builder
    {
        return DB::table('transactions')
            ->where('user_id', $userId)
            ->orderByDesc('id');
    }

    private function validatedDates(Request $request): array
    {
        $validated = $request->validate([
            'from' => ['nullable', 'date_format:Y-m-d'],
            'to'   => ['nullable', 'date_format:Y-m-d', 'after_or_equal:from'],
        ]);

        return [$validated['from'] ?? null, $validated['to'] ?? null];
    }

    private function applyDateFilter(Builder $query, ?string $from, ?string $to): void
    {
        if ($from && $to) {
            $query->whereBetween('created_at', [$from . ' 00:00:00', $to . ' 23:59:59']);
        } elseif ($from) {
            $query->whereDate('created_at', $from);
        } elseif ($to) {
            // Previously a "to" with no "from" was ignored entirely, so the
            // page showed all-time data while looking filtered.
            $query->whereDate('created_at', $to);
        }
    }
}