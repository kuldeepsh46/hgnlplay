<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use App\Services\PaymentDueService;
use Illuminate\Support\Facades\Auth;

class PaymentDueController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $due = PaymentDueService::forUser($user);

        $upi_qr = Setting::where('id', 1)->value('qr_scanner_img');
        $usdt_qr = Setting::where('id', 2)->value('qr_scanner_img');

        return view('payments.due', compact('user', 'due', 'upi_qr', 'usdt_qr'));
    }
}
