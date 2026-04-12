<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // =============================
        // 🔹 Global Admin Dashboard Data
        // =============================
        $totalUsers = DB::table('users')
            ->whereNotIn('id', [106, 107])
            ->count();
        $totalWallet = DB::table('wallets')->sum('balance');
        $pendingWithdraws = DB::table('withdraw_requests')->where('status', 'pending')->count();
        $completedWithdraws = DB::table('withdraw_requests')->where('status', 'completed')->count();
        $totalTopups = DB::table('orders')->count();

        // ✅ Monthly Growth for Current Month
        $startOfMonth = now()->startOfMonth();
        $endOfMonth = now()->endOfMonth();

        // Users created this month (daily count)
        $userGrowth = DB::table('users')
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('COUNT(*) as count'))
            ->whereBetween('created_at', [$startOfMonth, $endOfMonth])
            ->groupBy(DB::raw('DATE(created_at)'))
            ->orderBy('date')
            ->pluck('count', 'date');

        // Funds added this month (daily sum)
        $fundGrowth = DB::table('fund_requests')
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('SUM(amount) as total'))
            ->whereBetween('created_at', [$startOfMonth, $endOfMonth])
            ->groupBy(DB::raw('DATE(created_at)'))
            ->orderBy('date')
            ->pluck('total', 'date');

        $labels = collect($userGrowth->keys())->merge($fundGrowth->keys())->unique()->sort()->values();
        $userData = $labels->map(fn($d) => $userGrowth[$d] ?? 0);
        $fundData = $labels->map(fn($d) => $fundGrowth[$d] ?? 0);

        $packageSums = DB::table('transactions')
            ->select('amount', DB::raw('SUM(amount) as total'))
            ->where('type', 'Debit')
            ->whereIn('amount', [1000, 1100, 7000, 13000, 50000, 100000])
            ->groupBy('amount')
            ->pluck('total', 'amount');

        // Package Users Data (for modal)
        $packageUsers = DB::table('transactions as t')
            ->join('users as u', 'u.id', '=', 't.user_id')
            ->select('t.amount', 'u.name', 'u.email', 't.created_at')
            ->where('t.type', 'Debit')
            ->whereIn('t.amount', [1000, 1100, 7000, 13000, 50000, 100000])
            ->orderBy('t.created_at', 'desc')
            ->get()
            ->groupBy(function ($item) {
                return (int) $item->amount; // 🔥 convert 100000.00 → 100000
            });

        // dd($$packageSums);

        // Starter Package (1000 + 1100 combined)
        $starterTotal = ($packageSums['1000.00'] ?? 0) + ($packageSums['1100.00'] ?? 0);

        $sevenTotal = $packageSums['7000.00'] ?? 0;
        $thirteenTotal = $packageSums['13000.00'] ?? 0;
        $fiftyKTotal = $packageSums['50000.00'] ?? 0;
        $oneLakhTotal = $packageSums['100000.00'] ?? 0;

        // =============================
        // 🔹 Customer-Specific Dashboard Data
        // =============================
        // if ($user->hasRole('customer')) {
        //     // 1️⃣ Payout Received (Completed)
        //     $payoutReceived = DB::table('withdraw_requests')->where('user_id', $user->id)->where('status', 'completed')->count();

        //     // 2️⃣ Payout Pending
        //     $payoutPending = DB::table('withdraw_requests')->where('user_id', $user->id)->where('status', 'pending')->count();

        //     // 3️⃣ Direct Income (sum of transactions with "Direct 10% Commission…")
        //     $directIncome = DB::table('transactions')->where('user_id', $user->id)->where('remarks', 'like', 'Direct 10% Commission%')->sum('amount');

        //     $pairIncome = DB::table('transactions')->where('user_id', $user->id)->where('remarks', 'like', 'Pair Completion Bonus%')->sum('amount');

        //     // 4️⃣ Ledger Balance (today’s net movement)
        //     $today = Carbon::today();
        //     $todayCredits = DB::table('transactions')->where('user_id', $user->id)->whereDate('created_at', $today)->where('type', 'Credit')->sum('amount');

        //     $todayDebits = DB::table('transactions')->where('user_id', $user->id)->whereDate('created_at', $today)->where('type', 'Debit')->sum('amount');

        //     $ledgerBalance = $todayCredits - $todayDebits;

        //     $todayearning = $directIncome + $pairIncome;

        //     // 5️⃣ Topup Wallet (current wallet balance)
        //     $walletBalance = DB::table('wallets')->where('user_id', $user->id)->value('balance') ?? 0;

        //     // 6️⃣ Total Downline (users having this user as sponsor_id OR placement_id)
        //     $totalDownline = DB::table('users')->where('sponsor_id', $user->id)->orWhere('placement_id', $user->id)->count();

        //     $cycle = DB::table('lucky_cycles')
        //         ->where('user_id', auth()->id())
        //         ->first();

        //     $totalVouchers = 0;
        //     $unusedVouchers = 0;
        //     $rewardStatus = 'Not Eligible';
        //     $rewardText = '-';

        //     if ($cycle) {
        //         $totalVouchers = DB::table('lucky_vouchers')->where('cycle_id', $cycle->id)->count();

        //         $unusedVouchers = DB::table('lucky_vouchers')->where('cycle_id', $cycle->id)->where('status', 'unused')->count();

        //         if ($cycle->status === 'won') {
        //             $rewardStatus = '🎉 Winner';
        //             $rewardText = 'Congratulations! You won a reward';
        //         } elseif ($cycle->status === 'completed') {
        //             $rewardStatus = '🏆 Gold Reward';
        //             $rewardText = $cycle->package_id == 4 ? 'Gold worth ₹65,000' : 'Gold worth ₹1,30,000';
        //         } else {
        //             $rewardStatus = '⏳ Active';
        //             $rewardText = 'Lucky draw ongoing';
        //         }
        //     }

        //     $voucherGroups = [];
        //     $rewards = [];

        //     if ($cycle) {
        //         $voucherGroups = DB::table('lucky_vouchers')->where('cycle_id', $cycle->id)->orderBy('month_no')->get()->groupBy('month_no');

        //         $rewards = DB::table('lucky_rewards')->where('cycle_id', $cycle->id)->get();
        //     }

        //     return view('dashboard', compact('user', 'payoutReceived', 'payoutPending', 'directIncome', 'pairIncome', 'ledgerBalance', 'walletBalance', 'totalDownline', 'totalUsers', 'totalWallet', 'pendingWithdraws', 'completedWithdraws', 'totalTopups', 'labels', 'userData', 'fundData', 'cycle', 'totalVouchers', 'unusedVouchers', 'rewardStatus', 'rewardText', 'voucherGroups', 'rewards', 'packageUsers'));
        // }

        if ($user->hasRole('customer')) {
            // 1️⃣ Payouts
            // dd($user->id);
            $payoutReceived = DB::table('withdraw_requests')->where('user_id', $user->id)->where('status', 'completed')->count();
            $payoutPending = DB::table('withdraw_requests')->where('user_id', $user->id)->where('status', 'pending')->count();

            // 2️⃣ Incomes
            // $directIncome = DB::table('transactions')->where('user_id', $user->id)->where('remarks', 'like', 'Direct 10% Commission%')->sum('amount');
            $directIncome = DB::table('transactions')->where('user_id', $user->id)->where('remarks', 'like', '%Commission%')->sum('amount');
            $pairIncome = DB::table('transactions')->where('user_id', $user->id)->where('remarks', 'like', 'Pair Completion Bonus%')->sum('amount');

            // 3️⃣ Wallet & Earnings
            $walletBalance = DB::table('wallets')->where('user_id', $user->id)->value('balance') ?? 0;
            $totalEarning = $directIncome + $pairIncome;

            // 4️⃣ Total Downline (Global)
            $totalDownline = DB::table('users')->where('sponsor_id', $user->id)->orWhere('placement_id', $user->id)->count();

            // 5️⃣ SPLIT Downline (For Radio Buttons)
            // We filter by 'leg' column (1 = Left, 2 = Right)
            $leftDownline = DB::table('users')
                ->where('leg', 1)
                ->where(function ($query) use ($user) {
                    $query->where('sponsor_id', $user->id)->orWhere('placement_id', $user->id);
                })
                ->count();

            $rightDownline = DB::table('users')
                ->where('leg', 2)
                ->where(function ($query) use ($user) {
                    $query->where('sponsor_id', $user->id)->orWhere('placement_id', $user->id);
                })
                ->count();

            // 6️⃣ Lucky Cycle Logic (Your existing code)
            $cycle = DB::table('lucky_cycles')
                ->where('user_id', auth()->id())
                ->first();
            $totalVouchers = 0;
            $unusedVouchers = 0;
            $rewardStatus = 'Not Eligible';
            $rewardText = '-';
            $voucherGroups = [];
            $rewards = [];

            if ($cycle) {
                $totalVouchers = DB::table('lucky_vouchers')->where('cycle_id', $cycle->id)->count();
                $unusedVouchers = DB::table('lucky_vouchers')->where('cycle_id', $cycle->id)->where('status', 'unused')->count();

                if ($cycle->status === 'won') {
                    $rewardStatus = '🎉 Winner';
                    $rewardText = 'Congratulations! You won a reward';
                } elseif ($cycle->status === 'completed') {
                    $rewardStatus = '🏆 Gold Reward';
                    $rewardText = $cycle->package_id == 4 ? 'Gold worth ₹65,000' : 'Gold worth ₹1,30,000';
                } else {
                    $rewardStatus = '⏳ Active';
                    $rewardText = 'Lucky draw ongoing';
                }
                $voucherGroups = DB::table('lucky_vouchers')->where('cycle_id', $cycle->id)->orderBy('month_no')->get()->groupBy('month_no');
                $rewards = DB::table('lucky_rewards')->where('cycle_id', $cycle->id)->get();
            }

            return view('dashboard', compact('user', 'payoutReceived', 'payoutPending', 'directIncome', 'pairIncome', 'walletBalance', 'totalDownline', 'leftDownline', 'rightDownline', 'cycle', 'totalVouchers', 'unusedVouchers', 'rewardStatus', 'rewardText', 'voucherGroups', 'rewards', 'totalEarning'));
        }

        // Admin dashboard view
        return view(
            'dashboard',
            compact(
                'user',
                'totalUsers',
                'totalWallet',
                'pendingWithdraws',
                'completedWithdraws',
                'totalTopups',
                'labels',
                'userData',
                'fundData',
                'starterTotal',
                'sevenTotal',
                'thirteenTotal',
                'fiftyKTotal',
                'oneLakhTotal',
                'packageUsers', // 👈 ADD THIS
            ),
        );
    }
    public function SSSSindex()
    {
        $user = Auth::user();

        $totalUsers = DB::table('users')->count();
        $totalWallet = DB::table('wallets')->sum('balance');
        $pendingWithdraws = DB::table('withdraw_requests')->where('status', 'pending')->count();
        $completedWithdraws = DB::table('withdraw_requests')->where('status', 'completed')->count();
        $totalTopups = DB::table('orders')->count();

        // ✅ Monthly Growth for Current Month
        $startOfMonth = now()->startOfMonth();
        $endOfMonth = now()->endOfMonth();

        // Users created this month (daily count)
        $userGrowth = DB::table('users')
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('COUNT(*) as count'))
            ->whereBetween('created_at', [$startOfMonth, $endOfMonth])
            ->groupBy(DB::raw('DATE(created_at)'))
            ->orderBy('date')
            ->pluck('count', 'date');

        // Funds added this month (daily sum from fund_requests or orders)
        $fundGrowth = DB::table('fund_requests')
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('SUM(amount) as total'))
            ->whereBetween('created_at', [$startOfMonth, $endOfMonth])
            ->groupBy(DB::raw('DATE(created_at)'))
            ->orderBy('date')
            ->pluck('total', 'date');

        // Prepare arrays for Chart.js
        $labels = collect($userGrowth->keys())->merge($fundGrowth->keys())->unique()->sort()->values();
        $userData = $labels->map(fn($d) => $userGrowth[$d] ?? 0);
        $fundData = $labels->map(fn($d) => $fundGrowth[$d] ?? 0);

        return view('dashboard', compact('user', 'totalUsers', 'totalWallet', 'pendingWithdraws', 'completedWithdraws', 'totalTopups', 'labels', 'userData', 'fundData'));
    }

    public function manageUsers()
    {
        $users = DB::table('users as u')
            ->whereNotIn('u.id', [106, 107])
            ->leftJoin('wallets as w', 'w.user_id', '=', 'u.id')
            ->select('u.*', DB::raw('COALESCE(w.balance,0) as wallet_balance'))
            ->orderByDesc('u.id')
            ->get();

        // Add extra data for each user
        foreach ($users as $user) {
            // ✅ Total Completed Withdraw
            $user->withdraw_completed = DB::table('withdraw_requests')->where('user_id', $user->id)->where('status', 'completed')->sum('net_amount');

            // ✅ Total Pending Withdraw
            $user->withdraw_pending = DB::table('withdraw_requests')->where('user_id', $user->id)->where('status', 'pending')->sum('net_amount');

            // ✅ First Purchased Package
            $firstOrder = DB::table('orders')->where('user_id', $user->id)->where('status', 'completed')->orderBy('created_at', 'asc')->first();

            if ($firstOrder) {
                $package = DB::table('packages')->where('id', $firstOrder->package_id)->value('name');

                $price = $firstOrder->amount;

                $user->first_package = $package ?? 'N/A';
                $user->price = $price ?? 'N/A';
            } else {
                $user->first_package = 'N/A';
                $user->price = 'N/A';
            }
        }

        return view('admin.manage-users', compact('users'));
    }

    public function manageUsersOLD()
    {
        $users = DB::table('users')
            ->whereNotIn('id', [106, 107])
            ->orderByDesc('id')
            ->get();
        // ->paginate(10);

        return view('admin.manage-users', compact('users'));
    }

    public function deleteUser($id)
    {
        DB::table('users')->where('id', $id)->delete();
        DB::table('wallets')->where('user_id', $id)->delete();
        return back()->with('success', 'User deleted successfully!');
    }

    public function editUser($id)
    {
        $user = DB::table('users')->find($id);
        $wallet = DB::table('wallets')->where('user_id', $id)->first();
        return view('admin.edit-user', compact('user', 'wallet'));
    }

    public function updateUserPassword(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'password' => 'required|min:6|confirmed',
        ]);

        DB::table('users')
            ->where('id', $request->user_id)
            ->update([
                'password' => Hash::make($request->password),
            ]);

        return back()->with('success', 'Password updated successfully!');
    }

    public function showUserTree($id = null)
    {
        $user = $id ? \App\Models\User::findOrFail($id) : Auth::user();

        // Recursive function to build the tree
        $buildTree = function ($user) use (&$buildTree) {
            if (!$user) {
                return null;
            }

            $leftChild = \App\Models\User::where('placement_id', $user->id)->where('position', 'left')->first();

            $rightChild = \App\Models\User::where('placement_id', $user->id)->where('position', 'right')->first();

            return [
                'id' => $user->id,
                'username' => $user->username ?? $user->name,
                'email' => $user->email,
                'investment_count' => $user->investment_count,
                'status' => $user->status ?? 'active',
                'children' => array_filter([$buildTree($leftChild), $buildTree($rightChild)]),
            ];
        };

        $treeData = $buildTree($user);

        return view('admin.tree-view', compact('user', 'treeData'));
    }

    public function updateUser(Request $r, $id)
    {
        // dd($r->all());
        // $r->validate([
        //     'username' => 'required|string',
        //     'email' => 'required|email',
        //     'status' => 'required|string',
        //     'balance' => 'nullable|numeric'
        // ]);

        DB::table('users')
            ->where('id', $id)
            ->update([
                'username' => $r->username,
                'email' => $r->email,
                'mobile' => $r->mobile,
                'updated_at' => now(),
            ]);

        // Update wallet balance
        DB::table('wallets')->updateOrInsert(['user_id' => $id], ['balance' => $r->balance ?? 0, 'updated_at' => now()]);

        return redirect()->route('admin.users')->with('success', 'User details updated successfully!');
    }
}
