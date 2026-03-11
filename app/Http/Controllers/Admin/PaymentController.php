<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PaymentController extends Controller
{
    public function index(Request $r)
    {
        $query = DB::table('fund_requests')
            ->join('users', 'fund_requests.user_id', '=', 'users.id')
            ->select(
                'fund_requests.*',
                'users.username',
                'users.email'
            );

        if ($r->from && $r->to) {
            $query->whereBetween(DB::raw('DATE(fund_requests.created_at)'), [$r->from, $r->to]);
        }

        $all = (clone $query)->orderByDesc('fund_requests.id')->get();
        $pending = (clone $query)->where('fund_requests.status', 'pending')->get();
        $completed = (clone $query)->where('fund_requests.status', 'completed')->get();
        $rejected = (clone $query)->where('fund_requests.status', 'rejected')->get();

        return view('admin.manage-payments', compact('all', 'pending', 'completed', 'rejected', 'r'));
    }

    public function approve($id)
    {
        $payment = DB::table('fund_requests')->where('id', $id)->first();

        if (!$payment || $payment->status !== 'pending') {
            return back()->with('error', 'Invalid or already processed payment.');
        }

        DB::transaction(function() use ($payment) {
            // ✅ Mark payment completed
            DB::table('fund_requests')->where('id', $payment->id)->update([
                'status' => 'completed',
                'updated_at' => now(),
            ]);

            // ✅ Add amount to wallet
            DB::table('wallets')->updateOrInsert(
                ['user_id' => $payment->user_id],
                [
                    'balance' => DB::raw('COALESCE(balance, 0) + '.$payment->amount),
                    'updated_at' => now(),
                ]
            );

            // ✅ Log transaction (optional)
            DB::table('transactions')->insert([
                'user_id' => $payment->user_id,
                'type' => 'Credit',
                'amount' => $payment->amount,
                'remarks' => 'Fund request approved by admin',
                'created_at' => now(),
            ]);
        });

        return back()->with('success', 'Payment approved successfully and added to wallet.');
    }

    public function reject($id)
    {
        DB::table('fund_requests')->where('id', $id)->update([
            'status' => 'rejected',
            'updated_at' => now(),
        ]);

        return back()->with('error', 'Payment rejected successfully.');
    }
}
