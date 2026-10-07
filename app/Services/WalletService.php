<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

/*
|--------------------------------------------------------------------------
| Main wallet + Repurchase Wallet
|--------------------------------------------------------------------------
| Every earning (pair, direct, level, rank, sponsor bonus, rewards...) is
| split: REPURCHASE_PERCENT goes to the Repurchase Wallet and the rest to
| the main wallet. Wallet fund requests are NOT earnings and credit the
| main wallet in full (they don't go through here).
|
| The `transactions` row keeps the full earning in `amount` — income
| reports, the daily pair cap and the sponsor binary bonus all sum that —
| and records the split in main_wallet_amount / repurchase_wallet_amount.
|
| Repurchase Wallet money can only buy the Repurchase Package
| (Package::isRepurchase). It is never withdrawable or transferable.
|--------------------------------------------------------------------------
*/
class WalletService
{
    public const REPURCHASE_PERCENT = 10;

    /**
     * Credit an earning, split between the two wallets, and record it.
     *
     * $transaction holds the `transactions` columns (type, bonus_type,
     * remarks, ...); amount and the split columns are filled in here.
     * Returns the new `transactions` id.
     */
    public static function creditEarning(int $userId, float $amount, array $transaction): int
    {
        return DB::transaction(function () use ($userId, $amount, $transaction) {
            $repurchase = round($amount * self::REPURCHASE_PERCENT / 100, 2);
            $main = round($amount - $repurchase, 2);

            self::ensureWallet($userId);

            DB::table('wallets')->where('user_id', $userId)->update([
                'balance' => DB::raw('COALESCE(balance, 0) + ' . $main),
                'repurchase_balance' => DB::raw('COALESCE(repurchase_balance, 0) + ' . $repurchase),
                'updated_at' => now(),
            ]);

            $transactionId = DB::table('transactions')->insertGetId(array_merge([
                'user_id' => $userId,
                'created_at' => now(),
                'updated_at' => now(),
            ], $transaction, [
                'amount' => $amount,
                'main_wallet_amount' => $main,
                'repurchase_wallet_amount' => $repurchase,
            ]));

            if ($repurchase > 0) {
                DB::table('repurchase_wallet_transactions')->insert([
                    'user_id' => $userId,
                    'type' => 'credit',
                    'amount' => $repurchase,
                    'balance_after' => self::repurchaseBalance($userId),
                    'source' => $transaction['bonus_type'] ?? 'other',
                    'transaction_id' => $transactionId,
                    'remarks' => self::REPURCHASE_PERCENT . '% of ₹' . number_format($amount, 2) . ' earning'
                        . (isset($transaction['remarks']) ? ' — ' . mb_strimwidth($transaction['remarks'], 0, 400, '…') : ''),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            return $transactionId;
        });
    }

    /**
     * Pay for a Repurchase Package from the Repurchase Wallet.
     * Must run inside the caller's DB transaction. Returns false (and
     * changes nothing) when the balance is too low.
     */
    public static function debitRepurchase(int $userId, float $amount, ?int $orderId, string $remarks): bool
    {
        $wallet = DB::table('wallets')->where('user_id', $userId)->lockForUpdate()->first();

        if (!$wallet || (float) $wallet->repurchase_balance < $amount) {
            return false;
        }

        $balanceAfter = round((float) $wallet->repurchase_balance - $amount, 2);

        DB::table('wallets')->where('user_id', $userId)->update([
            'repurchase_balance' => $balanceAfter,
            'updated_at' => now(),
        ]);

        DB::table('repurchase_wallet_transactions')->insert([
            'user_id' => $userId,
            'type' => 'debit',
            'amount' => $amount,
            'balance_after' => $balanceAfter,
            'source' => 'repurchase_package',
            'order_id' => $orderId,
            'remarks' => $remarks,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return true;
    }

    public static function repurchaseBalance(int $userId): float
    {
        return (float) (DB::table('wallets')->where('user_id', $userId)->value('repurchase_balance') ?? 0);
    }

    private static function ensureWallet(int $userId): void
    {
        if (!DB::table('wallets')->where('user_id', $userId)->exists()) {
            DB::table('wallets')->insert([
                'user_id' => $userId,
                'balance' => 0,
                'repurchase_balance' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
