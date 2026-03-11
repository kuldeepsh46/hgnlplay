<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\LuckyService;
use Illuminate\Http\Request;
use DB;

class LuckyAdminController extends Controller
{
    /**
     * Show lucky draw participants
     */
    public function index()
    {
        $participants = DB::table('lucky_cycles')
            ->join('users', 'users.id', '=', 'lucky_cycles.user_id')
            ->select(
                'lucky_cycles.id as cycle_id',
                'users.id as user_id',
                'users.name',
                'users.email',
                'lucky_cycles.package_id',
                'lucky_cycles.current_month',
                'lucky_cycles.status'
            )
            ->where('lucky_cycles.status', 'active')
            ->get();

        return view('admin.lucky.index', compact('participants'));
    }

    /**
     * Show vouchers of a specific user
     */
    public function vouchers($cycleId)
    {
        $cycle = DB::table('lucky_cycles')->where('id', $cycleId)->first();

        $vouchers = DB::table('lucky_vouchers')
            ->join('users', 'users.id', '=', 'lucky_vouchers.user_id')
            ->select(
                'lucky_vouchers.*',
                'users.username',
                'users.email',
                'users.mobile'
            )
            ->where('lucky_vouchers.cycle_id', $cycleId)
            ->where('lucky_vouchers.status', 'unused')
            ->get();

        return view('admin.lucky.vouchers', compact('cycle', 'vouchers'));
    }

    /**
     * Declare winner manually
     */
    public function declareWinner(Request $request)
    {
        $request->validate([
            'voucher_id' => 'required|exists:lucky_vouchers,id',
            'reward_note' => 'required|string|max:255',
        ]);

        DB::beginTransaction();

        try {
            $voucher = DB::table('lucky_vouchers')->where('id', $request->voucher_id)->first();

            // Mark voucher used
            DB::table('lucky_vouchers')->where('id', $voucher->id)->update([
                'status' => 'used'
            ]);

            // Mark cycle as won
            DB::table('lucky_cycles')->where('id', $voucher->cycle_id)->update([
                'status' => 'won',
                'won_at' => now()
            ]);

            // Store reward
            DB::table('lucky_rewards')->insert([
                'user_id' => $voucher->user_id,
                'cycle_id' => $voucher->cycle_id,
                'reward_type' => 'cash',
                'reward_note' => $request->reward_note,
                'admin_id' => auth()->id(),
                'created_at' => now()
            ]);

            DB::commit();
            return redirect()->route('admin.lucky.index')
                ->with('success', 'Winner declared successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', $e->getMessage());
        }
    }
}
