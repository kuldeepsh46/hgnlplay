<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class WithdrawController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $wallet = DB::table('wallets')->where('user_id', $user->id)->first();
        $balance = $wallet->balance ?? 0;

        $withdraws = DB::table('withdraw_requests')
            ->where('user_id', $user->id)
            ->orderByDesc('id')
            ->get();

        return view('withdraw.index', compact('user', 'balance', 'withdraws'));
    }

    public function store(Request $r)
    {
        $r->validate([
            'amount' => 'required|numeric|min:1',
            'password' => 'required|string'
        ]);

        $user = Auth::user();
        $wallet = DB::table('wallets')->where('user_id', $user->id)->first();
        $balance = $wallet->balance ?? 0;

        // ✅ Password check
        if (!Hash::check($r->password, $user->password)) {
            return back()->with('error', 'Incorrect password.');
        }

        // ✅ Balance check
        if ($r->amount > $balance) {
            return back()->with('error', 'Insufficient wallet balance for withdrawal.');
        }

        // ✅ Prevent duplicate pending request
        $pending = DB::table('withdraw_requests')
            ->where('user_id', $user->id)
            ->where('status', 'pending')
            ->first();

        if ($pending) {
            return back()->with('error', 'You already have a pending withdrawal request.');
        }

        // ✅ Compute tax & net amount
        $tax = round($r->amount * 0.10, 2);
        $net = $r->amount - $tax;

        // ✅ Store request (tax + net)
        DB::table('withdraw_requests')->insert([
            'user_id' => $user->id,
            'amount' => $r->amount,
            'tax_amount' => $tax,
            'net_amount' => $net,
            'status' => 'pending',
            'created_at' => now(),
        ]);

        return back()->with('success', "Withdrawal request of ₹{$r->amount} submitted. ₹{$tax} will be deducted as tax.");
    }


    // ✅ This will be called by admin later when approving a request
   public static function markCompleted($id)
    {
        $request = DB::table('withdraw_requests')->find($id);
        if (!$request || $request->status === 'completed') return;

        $userWallet = DB::table('wallets')->where('user_id', $request->user_id)->first();
        $adminWallet = DB::table('wallets')->where('user_id', 6)->first(); // ✅ assuming admin user_id = 1

        // Deduct total withdrawal amount from user wallet
        if ($userWallet && $userWallet->balance >= $request->amount) {
            DB::table('wallets')->where('user_id', $request->user_id)->update([
                'balance' => $userWallet->balance - $request->amount,
                'updated_at' => now()
            ]);
        }

        // ✅ Add tax amount to admin wallet
        if ($adminWallet) {
            DB::table('wallets')->where('user_id', 1)->update([
                'balance' => $adminWallet->balance + $request->tax_amount,
                'updated_at' => now()
            ]);
        }

        // ✅ Mark completed
        DB::table('withdraw_requests')->where('id', $id)->update([
            'status' => 'completed',
            'updated_at' => now()
        ]);

        // Record both transactions (optional)
        DB::table('transactions')->insert([
            ['user_id' => $request->user_id, 'type' => 'Debit', 'amount' => $request->amount, 'remarks' => 'Withdrawal processed', 'created_at' => now()],
            ['user_id' => 1, 'type' => 'Credit', 'amount' => $request->tax_amount, 'remarks' => 'Withdrawal Tax Received', 'created_at' => now()]
        ]);
    }

}
