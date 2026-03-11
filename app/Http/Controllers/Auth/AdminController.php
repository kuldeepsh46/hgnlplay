<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    public function index()
    {
        // Basic stats for admin dashboard
        $totalUsers = DB::table('users')->count();
        $totalWallet = DB::table('wallets')->sum('balance');
        $pendingWithdraws = DB::table('withdraws')->where('status','pending')->count();
        $completedWithdraws = DB::table('withdraws')->where('status','completed')->count();
        $totalTopups = DB::table('orders')->count();

        return view('admin.dashboard', compact(
            'totalUsers','totalWallet','pendingWithdraws','completedWithdraws','totalTopups'
        ));
    }
}
