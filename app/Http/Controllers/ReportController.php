<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\StreamedResponse;
use App\Enums\BonusType;
class ReportController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $from = $request->input('from');
        $to = $request->input('to');
        $matchQuery = DB::table('transactions')
            ->where('user_id', $user->id)
            ->whereIn('bonus_type', [BonusType::PairBonusNormal->value])
            ->orderByDesc('id');

        // $directQuery = DB::table('transactions')->where('user_id', $user->id)->where('remarks', 'LIKE', '%Commission%')->orderByDesc('id');
        $directQuery = DB::table('transactions')->where('user_id', $user->id)->where('bonus_type', BonusType::DirectIncome->value)->orderByDesc('id');
        $levelIncomeQuery = DB::table('transactions')->where('user_id', $user->id)->where('bonus_type', BonusType::LevelIncome->value)->orderByDesc('id');

        // Date Filters (Single or Range)
        if ($from && $to) {
            $matchQuery->whereBetween('created_at', [$from . ' 00:00:00', $to . ' 23:59:59']);
            $directQuery->whereBetween('created_at', [$from . ' 00:00:00', $to . ' 23:59:59']);
            $levelIncomeQuery->whereBetween('created_at', [$from . ' 00:00:00', $to . ' 23:59:59']);
        } elseif ($from) {
            $matchQuery->whereDate('created_at', $from);
            $directQuery->whereDate('created_at', $from);
            $levelIncomeQuery->whereDate('created_at', $from);
        }

        $matchingIncomes = $matchQuery->paginate(10, ['*'], 'match_page')->appends($request->all());
        $directIncomes = $directQuery->paginate(10, ['*'], 'direct_page')->appends($request->all());
        $levelIncomes = $levelIncomeQuery->paginate(10, ['*'], 'level_page')->appends($request->all());

        return view('reports.index', compact('user', 'matchingIncomes', 'directIncomes', 'levelIncomes', 'from', 'to'));
    }

    public function export(Request $request, $type)
    {
        $user = Auth::user();
        $from = $request->get('from'); // Use get() to be sure
        $to = $request->get('to');
        // dd($from, $to);
        $query = DB::table('transactions')->where('user_id', $user->id);
        if ($type === 'matching') {
            $query->whereIn('bonus_type', [
                BonusType::PairBonusNormal->value,
                // BonusType::PairBonus2000->value,
                // BonusType::PairBonus->value,
            ]);

            $label = 'matching';
        } else if ($type === 'level') {
            $query->where('bonus_type', BonusType::LevelIncome->value);

            $label = 'level_income';
        } else if ($type === 'direct') {
            $query->where('bonus_type', BonusType::DirectIncome->value);

            $label = 'direct_income';
        } else {
            abort(404);
        } 

        // APPLY FILTERS TO THE EXPORT
        if ($from && $to) {
            $query->whereBetween('created_at', [$from . ' 00:00:00', $to . ' 23:59:59']);
            $filename = "{$label}_report_{$from}_to_{$to}.csv";
        } elseif ($from) {
            $query->whereDate('created_at', $from);
            $filename = "{$label}_report_{$from}.csv";
        } else {
            $filename = "{$label}_report_all_time.csv";
        }

        $data = $query->orderByDesc('id')->get();
        // CSV Generation Logic
        $headers = [
            'Content-type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=$filename",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        $callback = function () use ($data) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['Date', 'Amount', 'Remarks']);
            foreach ($data as $row) {
                fputcsv($file, [date('d M Y, h:i A', strtotime($row->created_at)), $row->amount, $row->remarks]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
