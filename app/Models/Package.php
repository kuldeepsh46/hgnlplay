<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Package extends Model
{
    // Updated to match your database column names
    protected $fillable = ['name', 'actual_amount', 'discounted_amount', 'amount', 'pv', 'direct_bonus', 'direct_bonus_type', 'pair_bonus', 'pair_bonus_type'];

    // Keep bonuses numeric in JSON (API) after the column became decimal
    protected $casts = [
        'direct_bonus' => 'float',
        'pair_bonus' => 'float',
    ];

    // Packages this package can pair with (stored both ways in package_pairings)
    public function pairedPackages()
    {
        return $this->belongsToMany(Package::class, 'package_pairings', 'package_id', 'paired_package_id');
    }

    // e.g. "10%" or "₹500"
    public static function formatBonus($value, $type): string
    {
        $value = (float) $value;
        $display = rtrim(rtrim(number_format($value, 2, '.', ''), '0'), '.');

        return $type === 'fixed' ? '₹' . $display : $display . '%';
    }
}
