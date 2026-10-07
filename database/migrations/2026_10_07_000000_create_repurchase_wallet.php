<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /*
     |--------------------------------------------------------------------------
     | Repurchase Wallet (see App\Services\WalletService)
     |--------------------------------------------------------------------------
     | - wallets.repurchase_balance: 10% of every earning lands here; it can
     |   only be spent on the Repurchase Package.
     | - transactions.main_wallet_amount / repurchase_wallet_amount: how an
     |   earning was split (amount stays the full earning, so income reports,
     |   pair caps and sponsor bonus maths are unchanged).
     | - repurchase_wallet_transactions: the Repurchase Wallet's own ledger.
     |--------------------------------------------------------------------------
     */
    public function up(): void
    {
        Schema::table('wallets', function (Blueprint $table) {
            $table->decimal('repurchase_balance', 15, 2)->default(0);
        });

        Schema::table('transactions', function (Blueprint $table) {
            $table->decimal('main_wallet_amount', 15, 2)->nullable();
            $table->decimal('repurchase_wallet_amount', 15, 2)->nullable();
        });

        Schema::create('repurchase_wallet_transactions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('type', 10);                       // credit | debit
            $table->decimal('amount', 15, 2);
            $table->decimal('balance_after', 15, 2);
            $table->string('source', 50);                     // bonus type for credits, 'repurchase_package' for debits
            $table->unsignedBigInteger('transaction_id')->nullable(); // earning row in `transactions`
            $table->unsignedBigInteger('order_id')->nullable();       // order paid with this wallet
            $table->string('remarks', 500)->nullable();
            $table->timestamps();
            $table->index(['user_id', 'created_at'], 'rwt_user_created_index');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('repurchase_wallet_transactions');

        Schema::table('transactions', function (Blueprint $table) {
            $table->dropColumn(['main_wallet_amount', 'repurchase_wallet_amount']);
        });

        Schema::table('wallets', function (Blueprint $table) {
            $table->dropColumn('repurchase_balance');
        });
    }
};
