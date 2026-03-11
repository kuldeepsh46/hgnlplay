<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class EMIController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $emis = DB::table('emis')
            ->where('user_id', $user->id)
            ->orderBy('emi_number')
            ->paginate(10);

        return view('emi.index', compact('user', 'emis'));
    }

    // For Admin use (mark EMI Paid)
    public function markPaid($id)
    {
        DB::table('emis')->where('id', $id)->update([
            'status' => 'Paid',
            'paid_at' => now(),
            'updated_at' => now(),
        ]);

        return back()->with('success', 'EMI marked as paid successfully!');
    }
}
