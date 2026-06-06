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

        // =============================
        // 🔹 Timezone Calculation (IST bounds converted to your App's Timezone)
        // =============================
        // $startOfTodayIST = Carbon::now('Asia/Kolkata')->startOfDay()->setTimezone(config('app.timezone'));
        // $endOfTodayIST = Carbon::now('Asia/Kolkata')->endOfDay()->setTimezone(config('app.timezone'));

        // // =============================
        // // 🔹 Today's Dashboard Data (IST)
        // // =============================

        // // 1. New Users Today
        // $todayUsers = DB::table('users')
        //     ->whereNotIn('id', [106, 107])
        //     ->whereBetween('created_at', [$startOfTodayIST, $endOfTodayIST])
        //     ->count();

        // // 2. Total Top-ups Today (Every single order placed today)
        // $todayTopups = DB::table('orders')
        //     ->whereBetween('created_at', [$startOfTodayIST, $endOfTodayIST])
        //     ->count();

        // // 3. Renewals Today
        // // (Checks for orders today where the user already has an older order in the system)
        // $todayRenewals = DB::table('orders as o1')
        //     ->whereBetween('o1.created_at', [$startOfTodayIST, $endOfTodayIST])
        //     ->whereExists(function ($query) {
        //         $query->select(DB::raw(1))->from('orders as o2')->whereColumn('o1.user_id', 'o2.user_id')->whereColumn('o2.id', '<', 'o1.id'); // Found an older order ID for this user
        //     })
        //     ->count();

        // // 4. Withdrawals Requested Today (Any status, just created today)
        // $todayPendingWithdraws = DB::table('withdraw_requests')
        //     ->whereBetween('created_at', [$startOfTodayIST, $endOfTodayIST])
        //     ->count();

        // // 5. Withdrawals Paid Today
        // // (Uses updated_at because a request made yesterday might be paid today)
        // $todayCompletedWithdraws = DB::table('withdraw_requests')
        //     ->where('status', 'completed')
        //     ->whereBetween('updated_at', [$startOfTodayIST, $endOfTodayIST])
        //     ->count();

        //     $todaysData = [
        //         'todayUsers' => $todayUsers,
        //         'todayTopups' => $todayTopups,
        //         'todayRenewals' => $todayRenewals,
        //         'todayPendingWithdraws' => $todayPendingWithdraws,
        //         'todayCompletedWithdraws' => $todayCompletedWithdraws,
        //     ];

        // 1. Force the timezone conversions into strict SQL datetime strings
        $startOfTodayIST = \Carbon\Carbon::now('Asia/Kolkata')->startOfDay()->format('Y-m-d H:i:s');
        $endOfTodayIST = \Carbon\Carbon::now('Asia/Kolkata')->endOfDay()->format('Y-m-d H:i:s');

        // (Optional) Uncomment this to see exactly what times Laravel is looking for:
        // dd($startOfTodayIST, $endOfTodayIST);

        // =============================
        // 🔹 Today's Dashboard Data (IST)
        // =============================

        // 1. New Users Today
        $todayUsers = DB::table('users')
            ->whereNotIn('id', [106, 107])
            ->whereBetween('created_at', [$startOfTodayIST, $endOfTodayIST])
            ->count();

        // 2. Total Top-ups Today (Every single order placed today)
        $todayTopups = DB::table('orders')
            ->whereBetween('created_at', [$startOfTodayIST, $endOfTodayIST])
            ->count();
            // dd($todayTopups);
        // dd($startOfTodayIST, $endOfTodayIST);

        // 3. Renewals Today
        $todayRenewals = DB::table('orders as o1')
            ->whereBetween('o1.created_at', [$startOfTodayIST, $endOfTodayIST])
            ->whereExists(function ($query) {
                $query->select(DB::raw(1))->from('orders as o2')->whereColumn('o1.user_id', 'o2.user_id')->whereColumn('o2.id', '<', 'o1.id');
            })
            ->count();

        // 4. Withdrawals Requested Today
        $todayPendingWithdraws = DB::table('withdraw_requests')
            ->whereBetween('created_at', [$startOfTodayIST, $endOfTodayIST])
            ->count();

        // 5. Withdrawals Paid Today
        $todayCompletedWithdraws = DB::table('withdraw_requests')
            ->where('status', 'completed')
            ->whereBetween('updated_at', [$startOfTodayIST, $endOfTodayIST])
            ->count();

        $todaysData = [
            'todayUsers' => $todayUsers,
            'todayTopups' => $todayTopups,
            'todayRenewals' => $todayRenewals,
            'todayPendingWithdraws' => $todayPendingWithdraws,
            'todayCompletedWithdraws' => $todayCompletedWithdraws,
        ];

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
            // $totalDownline = DB::table('users')->where('sponsor_id', $user->id)->orWhere('placement_id', $user->id)->count();
            $allDownliners = [];

            // 1. MANUALLY FIND THE TWO GATEKEEPERS
            $leftBranchRoot = \App\Models\User::where('placement_id', $user->id)->where('position', 'left')->first();

            $rightBranchRoot = \App\Models\User::where('placement_id', $user->id)->where('position', 'right')->first();

            // 2. FORCE THE LEFT SIDE
            if ($leftRoot = $leftBranchRoot) {
                $this->crawlAndForceSide($leftRoot, 'left', $allDownliners);
            }

            // 3. FORCE THE RIGHT SIDE
            if ($rightRoot = $rightBranchRoot) {
                $this->crawlAndForceSide($rightRoot, 'right', $allDownliners);
            }

            // 4. PREPARE THE COLLECTION
            $teamMembers = collect($allDownliners)->reject(fn($m) => $m->id == 27)->sortBy('created_at');
            $totalDownline = $teamMembers->count();

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
        return view('dashboard', compact('user', 'totalUsers', 'totalWallet', 'pendingWithdraws', 'completedWithdraws', 'totalTopups', 'labels', 'userData', 'fundData', 'starterTotal', 'sevenTotal', 'thirteenTotal', 'fiftyKTotal', 'oneLakhTotal', 'packageUsers', 'todaysData'));
    }
    private function crawlAndForceSide($node, $side, &$list)
    {
        // We overwrite the 'position' property in the object memory
        // so the Blade sees 'left' or 'right' regardless of the DB value.
        $node->position = $side;
        $list[] = $node;

        // Get EVERY child of this node
        $children = \App\Models\User::where('placement_id', $node->id)->get();

        foreach ($children as $child) {
            // We pass the parent's $side (the forced one) to the child
            $this->crawlAndForceSide($child, $side, $list);
        }
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
        dd('This method is currently not in use. Please check manageUsersOLD() for the previous implementation or refer to the new manageUsers() for the updated version with enhanced data.');
        // $users = DB::table('users as u')
        //     ->whereNotIn('u.id', [106, 107])
        //     ->leftJoin('wallets as w', 'w.user_id', '=', 'u.id')
        //     ->select('u.*', DB::raw('COALESCE(w.balance,0) as wallet_balance'))
        //     ->orderByDesc('u.id')
        //     ->get();
        $users = DB::table('users as u')
            ->whereNotIn('u.id', [106, 107])
            ->leftJoin('wallets as w', 'w.user_id', '=', 'u.id')
            ->leftJoin('orders as o', 'o.user_id', '=', 'u.id') // Join with the orders table
            ->select(
                'u.*',
                DB::raw('COALESCE(w.balance, 0) as wallet_balance'),
                DB::raw('COALESCE(SUM(o.amount), 0) as total_invested'), // Sum the investments
            )
            ->groupBy('u.id', 'w.balance') // Grouping required for aggregate function SUM
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
