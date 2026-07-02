<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Enums\BonusType;
use App\Services\MailNotificationService;

class PaymentController extends Controller
{
    public function index(Request $r)
    {
        $query = DB::table('fund_requests')->join('users', 'fund_requests.user_id', '=', 'users.id')->select('fund_requests.*', 'users.username', 'users.email');

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
        // dd($id);
        $payment = DB::table('fund_requests')->where('id', $id)->first();

        if (!$payment || $payment->status !== 'pending') {
            return back()->with('error', 'Invalid or already processed payment.');
        }

        DB::transaction(function () use ($payment) {
            // ✅ Mark payment completed
            DB::table('fund_requests')
                ->where('id', $payment->id)
                ->update([
                    'status' => 'completed',
                    'updated_at' => now(),
                ]);

            // ✅ Add amount to wallet
            DB::table('wallets')->updateOrInsert(
                ['user_id' => $payment->user_id],
                [
                    'balance' => DB::raw('COALESCE(balance, 0) + ' . $payment->amount),
                    'updated_at' => now(),
                ],
            );

            // ✅ Log transaction (optional)

            DB::table('transactions')->insert([
                'user_id' => $payment->user_id,
                'type' => 'Credit',
                'bonus_type' => BonusType::FundRequestApproved->value,
                'amount' => $payment->amount,
                'remarks' => 'Fund request approved by admin',
                'created_at' => now(),
            ]);
        });
        $member = DB::table('users')->where('id', $payment->user_id)->first();
        app(MailNotificationService::class)->send(
            $member->email ?? null,
            'Payment Successful
Payment Successful - HGNL Pay',
            'payment_successful',
            [
                'title' => 'Payment appoved by Admin',
                'badge' => 'Completed',
                'greeting' => 'Hello ' . ($member->username ?? 'Member') . ',',
                'message' => 'Your payment has been completed successfully.',
                'rows' => [
                    'Member ID' => $member->member_id ?? 'N/A',
                    // 'Package' => $package->name ?? 'N/A',
                    'Amount' => '₹' . number_format($payment->amount, 2),
                    // 'Payment By' => $r->payment_by,
                    'Paid By' => $member->username ?? 'N/A',
                    'Status' => 'Completed',
                    'Date' => now()->format('d M Y h:i A'),
                ],
                'note' => 'This is an automated confirmation from HGNL Pay.',
            ],
            $member->id,
        );
        return back()->with('success', 'Payment approved successfully and added to wallet.');
    }

    public function reject(Request $request, $id)
    {
        // dd($request->all());
        DB::table('fund_requests')
            ->where('id', $id)
            ->update([
                'status' => 'rejected',
                'updated_at' => now(),
            ]);
        $payment = DB::table('fund_requests')->where('id', $id)->first();

$member = DB::table('users')->where('id', $payment->user_id)->first();
if ($member) {
$reason = trim($request->input('reject_reason', ''));

$reason = $reason !== ''
    ? $reason
    : 'No specific reason provided.';

// dd($reason);
    app(MailNotificationService::class)->send(
        $member->email ?? null,
        'Payment Successful
Payment Unsuccessful - HGNL Pay',
        'payment_unsuccessful',
        [
            'title' => 'Payment rejected by Admin',
            'badge' => 'Rejected',
            'greeting' => 'Hello ' . ($member->username ?? 'Member') . ',',
            'message' => 'Your payment has been rejected by admin. Reason: ' . $reason,
            'rows' => [
                'Member ID' => $member->member_id ?? 'N/A',
                // 'Package' => $package->name ?? 'N/A',
                'Amount' => '₹' . number_format($payment->amount, 2),
                // 'Payment By' => $r->payment_by,
                // 'Paid By' => $member->username ?? 'N/A',
                'Status' => 'Rejected',
                'Date' => now()->format('d M Y h:i A'),
            ],
            'note' => 'If you believe this was a mistake, please contact HGNL Pay Support.',
        ],
        $member->id,
    );
}
        return back()->with('error', 'Payment rejected successfully.');
    }
}
