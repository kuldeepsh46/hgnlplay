<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\LuckyService;
use Illuminate\Http\Request;

class LuckyController extends Controller
{
    public function release()
    {
        try {
            LuckyService::releaseMonthlyVouchers();
            return back()->with('success', 'Monthly vouchers released successfully.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function declareWinner(Request $r)
    {
        LuckyService::declareWinner($r->voucher_id, auth()->id());
        return back()->with('success', 'Winner declared successfully.');
    }
}
