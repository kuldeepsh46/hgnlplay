<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportController extends Controller
{
    // public function index(Request $request)
    // {
    //     $user = Auth::user();

    //     // date filter
    //     $from = $request->input('from');
    //     $to   = $request->input('to');

    //     // base query
    //     $matchQuery = DB::table('transactions')
    //         ->where('user_id', $user->id)
    //         ->where('remarks', 'LIKE', '%Pair Bonus%')
    //         ->orderByDesc('id');

    //     $directQuery = DB::table('transactions')
    //         ->where('user_id', $user->id)
    //         ->where('remarks', 'LIKE', '%Commission%')
    //         ->orderByDesc('id');

    //     if ($from && $to) {
    //         $matchQuery->whereBetween('created_at', [$from, $to]);
    //         $directQuery->whereBetween('created_at', [$from, $to]);
    //     }

    //     $matchingIncomes = $matchQuery->paginate(10, ['*'], 'match_page');
    //     $directIncomes   = $directQuery->paginate(10, ['*'], 'direct_page');

    //     return view('reports.index', compact('user', 'matchingIncomes', 'directIncomes', 'from', 'to'));
    // }

    //     public function index(Request $request)
    // {
    //     $user = Auth::user();
    //     $from = $request->input('from');
    //     $to = $request->input('to');

    //     // Base Queries
    //     $matchQuery = DB::table('transactions')
    //         ->where('user_id', $user->id)
    //         ->where('remarks', 'LIKE', '%Pair Bonus%')
    //         ->orderByDesc('id');

    //     $directQuery = DB::table('transactions')
    //         ->where('user_id', $user->id)
    //         ->where('remarks', 'LIKE', '%Commission%')
    //         ->orderByDesc('id');

    //     // Filter Logic
    //     if ($from && $to) {
    //         // Range: From Date to To Date
    //         $matchQuery->whereBetween('created_at', [$from . ' 00:00:00', $to . ' 23:59:59']);
    //         $directQuery->whereBetween('created_at', [$from . ' 00:00:00', $to . ' 23:59:59']);
    //     } elseif ($from) {
    //         // Single Date: Just the 'From' date
    //         $matchQuery->whereDate('created_at', $from);
    //         $directQuery->whereDate('created_at', $from);
    //     } elseif ($to) {
    //         // Single Date: Just the 'To' date
    //         $matchQuery->whereDate('created_at', $to);
    //         $directQuery->whereDate('created_at', $to);
    //     }

    //     // Paginate with URL persistence
    //     $matchingIncomes = $matchQuery->paginate(10, ['*'], 'match_page')->appends($request->all());
    //     $directIncomes = $directQuery->paginate(10, ['*'], 'direct_page')->appends($request->all());

    //     return view('reports.index', compact('user', 'matchingIncomes', 'directIncomes', 'from', 'to'));
    // }

    public function index(Request $request)
    {
        $user = Auth::user();
        $from = $request->input('from');
        $to = $request->input('to');

        // Updated Query: Using 'Pair%Bonus' to catch "Pair Completion Bonus"
        $matchQuery = DB::table('transactions')
            ->where('user_id', $user->id)
            ->where('remarks', 'LIKE', '%Pair%Bonus%') // Matches "Pair Completion Bonus..."
            ->orderByDesc('id');

        $directQuery = DB::table('transactions')->where('user_id', $user->id)->where('remarks', 'LIKE', '%Commission%')->orderByDesc('id');

        // Date Filters (Single or Range)
        if ($from && $to) {
            $matchQuery->whereBetween('created_at', [$from . ' 00:00:00', $to . ' 23:59:59']);
            $directQuery->whereBetween('created_at', [$from . ' 00:00:00', $to . ' 23:59:59']);
        } elseif ($from) {
            $matchQuery->whereDate('created_at', $from);
            $directQuery->whereDate('created_at', $from);
        }

        $matchingIncomes = $matchQuery->paginate(10, ['*'], 'match_page')->appends($request->all());
        $directIncomes = $directQuery->paginate(10, ['*'], 'direct_page')->appends($request->all());

        return view('reports.index', compact('user', 'matchingIncomes', 'directIncomes', 'from', 'to'));
    }

    // public function export($type)
    // {
    //     $user = Auth::user();
    //     $query = DB::table('transactions')->where('user_id', $user->id);

    //     if ($type === 'matching') {
    //         $query->where('remarks', 'LIKE', '%Pair Bonus%');
    //     } else {
    //         $query->where('remarks', 'LIKE', '%Commission%');
    //     }

    //     $records = $query->orderBy('id')->get();

    //     $response = new StreamedResponse(function () use ($records) {
    //         $handle = fopen('php://output', 'w');
    //         fputcsv($handle, ['Date', 'Amount', 'Type', 'Remarks']);
    //         foreach ($records as $row) {
    //             fputcsv($handle, [
    //                 $row->created_at,
    //                 $row->amount,
    //                 $row->type,
    //                 $row->remarks,
    //             ]);
    //         }
    //         fclose($handle);
    //     });

    //     $filename = $type.'_income_'.date('Ymd_His').'.csv';
    //     $response->headers->set('Content-Type', 'text/csv');
    //     $response->headers->set('Content-Disposition', 'attachment; filename="'.$filename.'"');
    //     return $response;
    // }

    //     public function export(Request $request, $type)
    // {
    //     $user = Auth::user();
    //     $from = $request->input('from');
    //     $to = $request->input('to');

    //     // 1. Initialize Query
    //     $query = DB::table('transactions')->where('user_id', $user->id);

    //     // 2. Set the Type Filter (Matching vs Direct)
    //     if ($type === 'matching') {
    //         $filename = "matching_income_" . ($from ?? 'all') . ".csv";
    //         // USE THE WILDCARD FIX HERE
    //         $query->where('remarks', 'LIKE', '%Pair%Bonus%');
    //     } else {
    //         $filename = "direct_income_" . ($from ?? 'all') . ".csv";
    //         $query->where('remarks', 'LIKE', '%Commission%');
    //     }

    //     // 3. APPLY DATES (This was missing in your export)
    //     if ($from && $to) {
    //         $query->whereBetween('created_at', [$from . ' 00:00:00', $to . ' 23:59:59']);
    //     } elseif ($from) {
    //         $query->whereDate('created_at', $from);
    //     }

    //     $data = $query->orderByDesc('id')->get();

    //     // 4. Generate CSV
    //     $headers = [
    //         "Content-type"        => "text/csv",
    //         "Content-Disposition" => "attachment; filename=$filename",
    //         "Pragma"              => "no-cache",
    //         "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
    //         "Expires"             => "0"
    //     ];

    //     $callback = function() use ($data) {
    //         $file = fopen('php://output', 'w');
    //         fputcsv($file, ['Date', 'Amount', 'Type', 'Remarks']);

    //         foreach ($data as $row) {
    //             fputcsv($file, [
    //                 date('d M Y, h:i A', strtotime($row->created_at)),
    //                 $row->amount,
    //                 $row->type,
    //                 $row->remarks
    //             ]);
    //         }
    //         fclose($file);
    //     };

    //     return response()->stream($callback, 200, $headers);
    // }
    public function export(Request $request, $type)
    {
        // $user = Auth::user();
        // $from = $request->input('from');
        // $to = $request->input('to');

        // $query = DB::table('transactions')->where('user_id', $user->id);

        // if ($type === 'matching') {
        //     // Using the % wildcard to catch "Pair Completion Bonus"
        //     $query->where('remarks', 'LIKE', '%Pair%Bonus%');
        //     $filename = "matching_report_" . ($from ?? 'all_time') . ".csv";
        // } else {
        //     $query->where('remarks', 'LIKE', '%Commission%');
        //     $filename = "direct_report_" . ($from ?? 'all_time') . ".csv";
        // }

        // // Apply Date Filters to the Export Query
        // if ($from && $to) {
        //     $query->whereBetween('created_at', [$from . ' 00:00:00', $to . ' 23:59:59']);
        // } elseif ($from) {
        //     $query->whereDate('created_at', $from);
        // }

        // $data = $query->orderByDesc('id')->get();
        $user = Auth::user();
        $from = $request->get('from'); // Use get() to be sure
        $to = $request->get('to');
        // dd($from, $to);
        $query = DB::table('transactions')->where('user_id', $user->id);

        if ($type === 'matching') {
            $query->where('remarks', 'LIKE', '%Pair%Bonus%');
            $label = 'matching';
        } else {
            $query->where('remarks', 'LIKE', '%Commission%');
            $label = 'direct';
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
