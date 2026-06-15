<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Package extends Model
{
    // Updated to match your database column names
    protected $fillable = ['name', 'actual_amount', 'discounted_amount', 'amount', 'pv', 'direct_bonus', 'pair_bonus'];
}
