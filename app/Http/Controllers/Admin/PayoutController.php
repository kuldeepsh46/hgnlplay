<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Enums\BonusType;
use App\Services\MailNotificationService;
class PayoutController extends Controller
{
    public function index(Request $r)
    {
        $query = DB::table('withdraw_requests')->join('users', 'withdraw_requests.user_id', '=', 'users.id')->join('wallets', 'users.id', '=', 'wallets.user_id')->select('withdraw_requests.*', 'users.username', 'users.name', 'users.email', 'users.account_holder_name', 'users.bank_name', 'users.account_number', 'users.ifsc_code', 'wallets.balance as wallet_balance');

        if ($r->from && $r->to) {
            $query->whereBetween(DB::raw('DATE(withdraw_requests.created_at)'), [$r->from, $r->to]);
        }

        $all = (clone $query)->orderByDesc('withdraw_requests.id')->get();
        $pending = (clone $query)->where('withdraw_requests.status', 'pending')->get();
        $completed = (clone $query)->where('withdraw_requests.status', 'completed')->get();

        return view('admin.manage-payouts', compact('r', 'all', 'pending', 'completed'));
    }

    // public function approve($id)
    // {
    //     $payout = DB::table('withdraw_requests')->where('id', $id)->first();
    //     if (!$payout || $payout->status != 'pending') {
    //         return back()->with('error', 'Invalid payout request.');
    //     }

    //     $wallet = DB::table('wallets')->where('user_id', $payout->user_id)->first();
    //     if (!$wallet || $wallet->balance < $payout->amount) {
    //         return back()->with('error', 'Insufficient wallet balance for this transaction.');
    //     }

    //     DB::transaction(function () use ($payout, $wallet) {
    //         // ✅ Deduct from wallet
    //         DB::table('wallets')->where('user_id', $payout->user_id)->update([
    //             'balance' => $wallet->balance - $payout->amount,
    //             'updated_at' => now()
    //         ]);

    //         // ✅ Mark payout as completed
    //         DB::table('withdraw_requests')->where('id', $payout->id)->update([
    //             'status' => 'completed',
    //             'updated_at' => now(),
    //         ]);

    //         // ✅ Record transaction
    //         DB::table('transactions')->insert([
    //             'user_id' => $payout->user_id,
    //             'type' => 'Debit',
    //             'bonus_type' => BonusType::Payout->value,
    //             'amount' => $payout->amount,
    //             'remarks' => 'Payout Approved by Admin',
    //             'created_at' => now(),
    //         ]);
    //     });

    //     return back()->with('success', 'Payout approved successfully and amount deducted from wallet.');
    // }

    // public function reject($id)
    // {
    //     DB::table('withdraw_requests')->where('id', $id)->update([
    //         'status' => 'rejected',
    //         'updated_at' => now(),
    //     ]);

    //     return back()->with('error', 'Payout request rejected.');
    // }

    public function approve($id)
    {
        $payout = DB::table('withdraw_requests')->where('id', $id)->first();

        if (!$payout || $payout->status != 'pending') {
            return back()->with('error', 'Invalid payout request.');
        }

        $wallet = DB::table('wallets')->where('user_id', $payout->user_id)->first();

        if (!$wallet || $wallet->balance < $payout->amount) {
            return back()->with('error', 'Insufficient wallet balance for this transaction.');
        }

        $user = DB::table('users')->where('id', $payout->user_id)->first();

        DB::transaction(function () use ($payout, $wallet) {
            DB::table('wallets')
                ->where('user_id', $payout->user_id)
                ->update([
                    'balance' => $wallet->balance - $payout->amount,
                    'updated_at' => now(),
                ]);

            DB::table('withdraw_requests')
                ->where('id', $payout->id)
                ->update([
                    'status' => 'completed',
                    'updated_at' => now(),
                ]);

            DB::table('transactions')->insert([
                'user_id' => $payout->user_id,
                'type' => 'Debit',
                'bonus_type' => BonusType::Payout->value,
                'amount' => $payout->amount,
                'remarks' => 'Payout Approved by Admin',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        });

        if ($user) {
            app(MailNotificationService::class)->send(
                $user->email ?? null,
                'Withdrawal Approved - HGNL Pay',
                'withdrawal_approved',
                [
                    'title' => 'Withdrawal Approved',
                    'badge' => 'Approved',
                    'greeting' => 'Hello ' . ($user->username ?? 'Member') . ',',
                    'message' => 'Your withdrawal request has been approved by admin.',
                    'rows' => [
                        'Member ID' => $user->member_id ?? 'N/A',
                        'Member Name' => $user->username ?? 'N/A',
                        'Amount' => '₹' . number_format($payout->amount, 2),
                        'Status' => 'Completed',
                        'Transaction Type' => 'Debit',
                        'Date' => now()->format('d M Y h:i A'),
                    ],
                    'note' => 'The approved withdrawal amount has been deducted from your HGNL Pay wallet.',
                ],
                $user->id,
            );
        }

        return back()->with('success', 'Payout approved successfully and amount deducted from wallet.');
    }
    public function reject(Request $request, $id)
    {
        $reason = trim($request->input('reject_reason', ''));

        if ($reason === '') {
            $reason = 'No specific reason provided.';
        }

        $payout = DB::table('withdraw_requests')->where('id', $id)->first();

        if (!$payout || $payout->status != 'pending') {
            return back()->with('error', 'Invalid payout request.');
        }

        $user = DB::table('users')->where('id', $payout->user_id)->first();

        DB::table('withdraw_requests')
            ->where('id', $id)
            ->update([
                'status' => 'rejected',
                // 'reject_reason' => $reason,
                'updated_at' => now(),
            ]);

        if ($user) {
            app(MailNotificationService::class)->send(
                $user->email ?? null,
                'Withdrawal Rejected - HGNL Pay',
                'withdrawal_rejected',
                [
                    'title' => 'Withdrawal Request Rejected',
                    'badge' => 'Rejected',
                    'greeting' => 'Hello ' . ($user->username ?? 'Member') . ',',
                    'message' => 'Your withdrawal request has been rejected by admin.',
                    'rows' => [
                        'Member ID' => $user->member_id ?? 'N/A',
                        'Member Name' => $user->username ?? 'N/A',
                        'Requested Amount' => '₹' . number_format($payout->amount, 2),
                        'Status' => 'Rejected',
                        'Reason' => $reason,
                        'Date' => now()->format('d M Y h:i A'),
                    ],
                    'note' => 'If you believe this was a mistake, please contact HGNL Pay Support.',
                ],
                $user->id,
            );
        }

        return back()->with('error', 'Payout request rejected.');
    }
}
