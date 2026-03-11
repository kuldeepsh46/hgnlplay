<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Wallet extends Model
{
    //
     protected $fillable = [
        'user_id', 'amount', 'deposit_date', 'payment_mode', 
        'bank_name', 'account_number', 'transaction_remark', 
        'attachment', 'status'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
