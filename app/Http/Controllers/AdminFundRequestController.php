<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FundRequest;
use App\Models\Wallet;
use Illuminate\Http\Request;

class AdminFundRequestController extends Controller
{
    public function approve($id)
    {
        $fund = FundRequest::findOrFail($id);

        // Only approve pending requests
        if ($fund->status === 'pending') {
            // Update fund request status
            $fund->update(['status' => 'completed']);

            // Find or create wallet
            $wallet = Wallet::firstOrCreate(
                ['user_id' => $fund->user_id],
                ['balance' => 0]
            );

            // Add amount to wallet balance
            $wallet->balance += $fund->amount;
            $wallet->save();
        }

        return redirect()->back()->with('success', 'Fund Request Approved and Wallet Updated!');
    }

    public function reject($id)
    {
        $fund = FundRequest::findOrFail($id);
        $fund->update(['status' => 'rejected']);

        return redirect()->back()->with('info', 'Fund Request Rejected.');
    }
}
