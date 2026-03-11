<?php
namespace App\Services;

use App\Models\{User,Order,Commission,WalletLedger,Package};
use Illuminate\Support\Facades\DB;

class CommissionService
{
    public int $dailyPairCap = 50; // adjust as needed

    public function creditWallet(User $user, float $amount, string $reason, $reference): void
    {
        $user->wallet_balance = round($user->wallet_balance + $amount,2);
        $user->save();

        WalletLedger::create([
            'user_id' => $user->id,
            'direction' => 'credit',
            'amount' => $amount,
            'balance_after' => $user->wallet_balance,
            'reason' => $reason,
            'reference_type' => get_class($reference),
            'reference_id' => $reference->id,
        ]);
    }

    // 1) Direct bonus to sponsor
    public function giveDirectBonus(Order $order): void
    {
        $buyer = $order->user;
        $sponsor = $buyer->sponsor;
        if (!$sponsor) return;

        $pkg = $order->package;
        $bonus = $pkg->direct_bonus;
        $com = Commission::create([
            'user_id'=>$sponsor->id,'type'=>'direct','amount'=>$bonus,
            'source_user_id'=>$buyer->id,'order_id'=>$order->id,
            'meta'=>json_encode(['package'=>$pkg->name]),
        ]);
        $this->creditWallet($sponsor, $bonus, 'Direct bonus', $com);
    }

    // 2) Add PV up the placement line and create pair commissions
    public function propagatePVAndPair(Order $order): void
    {
        $pv = $order->pv;
        $buyer = $order->user;

        // Walk up the BINARY placement parents and add PV to the correct side
        $currentUser = $buyer;
        $pkg = $order->package;

        // Find buyer node first
        $buyerNode = $buyer->node;
        if (!$buyerNode) return;

        $parent = $buyerNode->parent; // User model (may be null)
        $childSide = $buyerNode->side;

        while ($parent) {
            if ($childSide === 'L') {
                $parent->left_volume += $pv;
                $parent->carry_left  += $pv;
            } else {
                $parent->right_volume += $pv;
                $parent->carry_right  += $pv;
            }

            // Calculate pairs available now
            $pairs = min($parent->carry_left, $parent->carry_right);
            if ($pairs > 0) {
                // apply daily cap (simple example—swap with per-day counter as needed)
                $payablePairs = min($pairs, $this->dailyPairCap);

                $amount = $payablePairs * $pkg->pair_bonus;
                if ($amount > 0) {
                    $com = Commission::create([
                        'user_id'=>$parent->id,'type'=>'pair','amount'=>$amount,
                        'source_user_id'=>$buyer->id,'order_id'=>$order->id,
                        'meta'=>json_encode(['pairs'=>$payablePairs,'pair_bonus'=>$pkg->pair_bonus]),
                    ]);
                    $this->creditWallet($parent, $amount, 'Pair bonus', $com);

                    // reduce carries equally
                    $parent->carry_left  -= $payablePairs;
                    $parent->carry_right -= $payablePairs;
                }
            }

            $parent->save();

            // climb to next parent
            $parentNode = $parent->node;
            if (!$parentNode || !$parentNode->parent) break;
            $childSide = $parentNode->side;  // parent's side relative to its parent
            $parent = $parentNode->parent;
        }
    }
}
