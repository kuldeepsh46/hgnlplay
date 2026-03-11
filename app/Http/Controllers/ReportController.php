<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        // date filter
        $from = $request->input('from');
        $to   = $request->input('to');

        // base query
        $matchQuery = DB::table('transactions')
            ->where('user_id', $user->id)
            ->where('remarks', 'LIKE', '%Pair Bonus%')
            ->orderByDesc('id');

        $directQuery = DB::table('transactions')
            ->where('user_id', $user->id)
            ->where('remarks', 'LIKE', '%Commission%')
            ->orderByDesc('id');

        if ($from && $to) {
            $matchQuery->whereBetween('created_at', [$from, $to]);
            $directQuery->whereBetween('created_at', [$from, $to]);
        }

        $matchingIncomes = $matchQuery->paginate(10, ['*'], 'match_page');
        $directIncomes   = $directQuery->paginate(10, ['*'], 'direct_page');

        return view('reports.index', compact('user', 'matchingIncomes', 'directIncomes', 'from', 'to'));
    }

    public function export($type)
    {
        $user = Auth::user();
        $query = DB::table('transactions')->where('user_id', $user->id);

        if ($type === 'matching') {
            $query->where('remarks', 'LIKE', '%Pair Bonus%');
        } else {
            $query->where('remarks', 'LIKE', '%Commission%');
        }

        $records = $query->orderBy('id')->get();

        $response = new StreamedResponse(function () use ($records) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Date', 'Amount', 'Type', 'Remarks']);
            foreach ($records as $row) {
                fputcsv($handle, [
                    $row->created_at,
                    $row->amount,
                    $row->type,
                    $row->remarks,
                ]);
            }
            fclose($handle);
        });

        $filename = $type.'_income_'.date('Ymd_His').'.csv';
        $response->headers->set('Content-Type', 'text/csv');
        $response->headers->set('Content-Disposition', 'attachment; filename="'.$filename.'"');
        return $response;
    }
}
