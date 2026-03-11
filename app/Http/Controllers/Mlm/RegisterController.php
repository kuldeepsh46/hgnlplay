<?php

namespace App\Http\Controllers\Mlm;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class RegisterController extends Controller
{
    public function show()
    {
        // optional sponsor=USERNAME&side=L/R
        return view('mlm.register');
    }

    public function store(Request $r, \App\Services\PlacementService $placer)
    {
        $data = $r->validate([
            'name'=>'required','email'=>'required|email|unique:users',
            'username'=>'required|alpha_dash|unique:users',
            'password'=>'required|min:1|confirmed',
            'sponsor'=>'nullable|exists:users,username',
            'side'=>'nullable|in:L,R'
        ]);

        $user = \App\Models\User::create([
            'name'=>$data['name'],
            'email'=>$data['email'],
            'username'=>$data['username'],
            'password'=>bcrypt($data['password']),
        ]);

        if (!empty($data['sponsor'])) {
            $sponsor = \App\Models\User::where('username',$data['sponsor'])->first();
            $user->sponsor_id = $sponsor->id;
            $user->leg = $data['side'] ?? 'L';
            $user->save();

            // Place in binary (spillover safe)
            $placer->place($user, $sponsor, $user->leg);
        }

        auth()->login($user);
        return redirect()->route('dashboard')->with('success','Registered!');
    }
}

