<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema; // Fixes Schema undefined
use Illuminate\Support\Facades\Artisan; // Fixes Artisan undefined
class ManageUsersRolesController extends Controller
{
    /**
     * Display the management dashboard
     */
    public function index(Request $request)
    {
        // $search = $request->input('search');
$search = $request->input('search');
$user = DB::table('users')
    ->where('member_id', 'LIKE', "%{$search}%")
    ->orWhere('username', 'LIKE', "%{$search}%")
    ->first();
        // Always paginate all users for the second tab
        $allUsers = DB::table('users')->select('id', 'username', 'member_id', 'investment_count', 'emi_status')->latest()->paginate(15)->withQueryString();

        $user = null;
        $orders = collect();

        if ($search) {
            // Individual Inspection Logic
            $user = DB::table('users')->where('member_id', $search)->first();
            if ($user) {
                $orders = DB::table('orders')->where('user_id', $user->id)->latest()->get();
            }
        } else {
            // Global Recent Activity Logic (Shows when not searching)
            $orders = DB::table('orders')->join('users', 'orders.user_id', '=', 'users.id')->select('orders.*', 'users.username', 'users.member_id')->latest()->limit(10)->get();
        }

        return view('admin.manage-users-role.index', compact('user', 'orders', 'allUsers'));
    }

    // public function reverseAll(Request $request)
    // {
    //     $user = DB::table('users')->where('id', $request->user_id)->first();
    //     if (!$user) return back()->with('error', 'User not found');

    //     DB::transaction(function () use ($user) {
    //         // Delete all orders & transactions
    //         DB::table('orders')->where('user_id', $user->id)->delete();
    //         DB::table('transactions')->where('user_id', $user->id)->delete();

    //         // Reset investment count to 0
    //         DB::table('users')->where('id', $user->id)->update(['investment_count' => 0]);
    //     });

    //     return back()->with('success', "All data reversed for {$user->username}. Count is now 0.");
    // }

    // /**
    //  * Delete a specific package and reverse investment points
    //  */
    // public function deletePackage(Request $request)
    // {
    //     $order = DB::table('orders')->where('id', $request->order_id)->first();
    //     if (!$order) return back()->with('error', 'Order not found.');

    //     // Determine points to subtract (1k=1, 7k=7, 13k=13, 50k=50, 100k=100)
    //     $amount = (float)$order->amount;
    //     $points = match ($amount) {
    //         1000.0, 1100.0 => 1,
    //         7000.0, 7100.0 => 7,
    //         13000.0, 13100.0 => 13,
    //         50000.0, 50100.0 => 50,
    //         100000.0, 100100.0 => 100,
    //         default => floor($amount / 1000),
    //     };

    //     DB::beginTransaction();
    //     try {
    //         // 1. Reverse User Points
    //         DB::table('users')->where('id', $order->user_id)->decrement('investment_count', $points);
    //         DB::table('users')->where('id', $order->user_id)->update(['emi_status' => 'ongoing']);

    //         // 2. Remove Order & matching Debit Transaction
    //         DB::table('orders')->where('id', $order->id)->delete();
    //         DB::table('transactions')
    //             ->where('user_id', $order->from_user_id)
    //             ->where('amount', $order->amount)
    //             ->where('type', 'Debit')
    //             ->limit(1)->delete();

    //         DB::commit();
    //         return back()->with('success', "Package ₹{$amount} deleted. {$points} points reversed.");
    //     } catch (\Exception $e) {
    //         DB::rollBack();
    //         return back()->with('error', 'Operation failed: ' . $e->getMessage());
    //     }
    // }

    // 1. Reverse SINGLE Package
    // 1. Reverse SINGLE Package
    public function deletePackage(Request $request)
    {
        $orderId = $request->input('order_id');

        DB::transaction(function () use ($orderId) {
            $order = DB::table('orders')->where('id', $orderId)->first();

            if ($order) {
                $userId = $order->user_id;
                $pointsToDeduct = floor($order->amount / 1000);

                // 1. Delete the specific Order
                DB::table('orders')->where('id', $orderId)->delete();

                // 2. Delete related Transactions
                // We look for transactions created at the exact same time as the order
                DB::table('transactions')->where('user_id', $userId)->where('created_at', $order->created_at)->delete();

                // 3. Subtract the points/EMIs from the User
                DB::table('users')->where('id', $userId)->update([
    'investment_count' => DB::raw("GREATEST(0, CAST(investment_count AS SIGNED) - $pointsToDeduct)"),
    'emi_status' => 0 // Set to 0 directly to avoid the 'ongoing' math error
]);

                // 4. Adjust Wallet Balance (Optional: subtract the amount if already paid)
                // This depends on if you want to take the money back from their wallet
                $refundAmount = $order->amount * 0.1; // Assuming 10% was the bonus
                DB::table('wallets')->where('user_id', $userId)->decrement('balance', $refundAmount);
            }
        });

        return back()->with('success', 'Package and related transactions reversed successfully.');
    }

    // 2. Reverse ALL Packages (Full Reset)
    public function reverseAll(Request $request)
    {
        $userId = $request->input('user_id');

        DB::transaction(function () use ($userId) {
            // 1. Delete all transactions (Incomes, Pair Bonuses, etc.)
            DB::table('transactions')->where('user_id', $userId)->delete();

            // 2. Delete all orders (Package history)
            DB::table('orders')->where('user_id', $userId)->delete();

            // 3. Reset Wallet balance to zero
            DB::table('wallets')
                ->where('user_id', $userId)
                ->update([
                    'balance' => 0,
                    'updated_at' => now(),
                ]);

            // 4. Reset User Stats
            DB::table('users')
                ->where('id', $userId)
                ->update([
                    'investment_count' => 0,
                    'emi_status' => 0,
                    // Add other fields like 'total_earnings' if you track them
                ]);
        });

        return back()->with('success', 'User cleared: Orders, Transactions, and Stats have been wiped.');
    }

    /**
     * The Panic Button: Drops all database tables
     */
    public function systemPanic(Request $request)
    {
        if ($request->panic_key !== 'Nuke2026') {
            return abort(403, 'Invalid Master Key.');
        }

        try {
            Schema::disableForeignKeyConstraints();
            $tables = DB::select('SHOW TABLES');
            $dbName = 'Tables_in_' . env('DB_DATABASE');

            foreach ($tables as $table) {
                Schema::drop($table->$dbName);
            }
            Schema::enableForeignKeyConstraints();

            Artisan::call('config:clear');
            Artisan::call('cache:clear');

            return response()->json(['status' => 'success', 'message' => 'Schema Destroyed.']);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }
}
