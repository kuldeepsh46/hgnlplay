<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Package;

class PackagesController extends Controller
{
    public function index()
    {
        return Package::select(
            'id',
            'name',
            'actual_amount',
            'discounted_amount',
            'amount',
            'pv',
            'direct_bonus',
            'pair_bonus'
        )
        ->latest()
        ->take(20)
        ->get();
    }

    public function show($id)
    {
        return Package::select(
            'id',
            'name',
            'actual_amount',
            'discounted_amount',
            'amount',
            'pv',
            'direct_bonus',
            'pair_bonus',
            'created_at',
            'updated_at'
        )
        ->findOrFail($id);
    }
}