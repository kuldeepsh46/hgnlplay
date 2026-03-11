<?php

namespace App\Http\Controllers\Mlm;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function store(Request $r)
    {
        $data = $r->validate(['package_id'=>'required|exists:packages,id']);
        $user = auth()->user();
        $pkg  = Package::findOrFail($data['package_id']);

        $order = Order::create([
            'user_id'=>$user->id,
            'package_id'=>$pkg->id,
            'status'=>'paid',          // after payment gateway webhook set to paid
            'pv'=>$pkg->pv,
            'amount'=>$pkg->amount,
        ]);

        // For demo: activate immediately (in real life, do this in payment webhook)
        $order->update(['status'=>'activated','activated_at'=>now()]);
        \App\Jobs\ProcessActivationJob::dispatch($order);

        return back()->with('success','Order placed & activation in progress.');
    }
}

