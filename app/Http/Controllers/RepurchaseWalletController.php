<?php

namespace App\Http\Controllers;

use App\Models\Package;
use App\Services\WalletService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class RepurchaseWalletController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        $type = in_array($request->query('type'), ['credit', 'debit'], true) ? $request->query('type') : null;

        $entries = DB::table('repurchase_wallet_transactions')
            ->where('user_id', $user->id)
            ->when($type, fn ($q) => $q->where('type', $type))
            ->orderByDesc('id')
            ->paginate(20)
            ->withQueryString();

        $totals = DB::table('repurchase_wallet_transactions')
            ->where('user_id', $user->id)
            ->selectRaw("COALESCE(SUM(CASE WHEN type = 'credit' THEN amount END), 0) as credited")
            ->selectRaw("COALESCE(SUM(CASE WHEN type = 'debit' THEN amount END), 0) as spent")
            ->first();

        $repurchasePackages = Package::orderBy('id')->get()->filter(fn ($p) => Package::isRepurchase($p))->values();

        return view('repurchase-wallet', [
            'user' => $user,
            'balance' => WalletService::repurchaseBalance($user->id),
            'credited' => (float) $totals->credited,
            'spent' => (float) $totals->spent,
            'entries' => $entries,
            'type' => $type,
            'percent' => WalletService::REPURCHASE_PERCENT,
            'repurchasePackages' => $repurchasePackages,
        ]);
    }
}
