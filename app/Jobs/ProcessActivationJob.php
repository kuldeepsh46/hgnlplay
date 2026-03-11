<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class ProcessActivationJob implements ShouldQueue
{
  use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(public Order $order) { $this->onQueue('mlm'); }

    public function handle(\App\Services\CommissionService $svc): void
    {
        $order = $this->order->fresh(['user','package']);
        if (!$order || $order->status !== 'activated') return;

        $svc->giveDirectBonus($order);
        $svc->propagatePVAndPair($order);
    }
}
