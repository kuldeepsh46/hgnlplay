<?php

namespace App\Http\Controllers\Mlm;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class TreeController extends Controller
{
    public function index()
    {
        $root = auth()->user(); // or pick any user to visualize
        return view('mlm.tree', compact('root'));
    }
}

