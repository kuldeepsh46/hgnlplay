<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class MemberController extends Controller
{

    public function create()
    {
        $user = Auth::user();
        return view('member_register', compact('user'));
    }

    public function store(Request $request)
    {

        $request->validate([
            'username' => 'required|unique:users,username',
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'mobile' => 'required|digits_between:10,11',
            'password' => 'required|min:6|confirmed',
            'position' => 'required|in:left,right',
            'state' => 'required|string|max:100',
        ]);

        $sponsor = Auth::user();
        $leg = $request->position; // left or right
        $placementId = $sponsor->id;

        // check sponsor children
        $leftChild = User::where('placement_id', $sponsor->id)
            ->where('position', 'left')
            ->first();

        $rightChild = User::where('placement_id', $sponsor->id)
            ->where('position', 'right')
            ->first();


        // if leg already filled -> go deeper
        if ($leg === 'left' && $leftChild) {
            $placementId = $this->findLastAvailable($leftChild->id, 'left');
        }

        if ($leg === 'right' && $rightChild) {
            $placementId = $this->findLastAvailable($rightChild->id, 'right');
        }


        /*
        ======================================
        Determine actual position under parent
        ======================================
        */

        $placementUser = User::find($placementId);

        $leftCheck = User::where('placement_id', $placementId)
            ->where('position', 'left')
            ->first();

        $rightCheck = User::where('placement_id', $placementId)
            ->where('position', 'right')
            ->first();


        if (!$leftCheck) {
            $finalPosition = 'left';
        } else {
            $finalPosition = 'right';
        }


        /*
        ======================================
        Create Member
        ======================================
        */

        $member = User::create([

            'username' => $request->username,
            'name' => $request->name,
            'email' => $request->email,
            'mobile' => $request->mobile,
            'pan_number' => $request->pan_number,
            'state' => $request->state,
            'city' => $request->city,
            'password' => Hash::make($request->password),

            'sponsor_id' => $sponsor->id,
            'placement_id' => $placementId,
            'sponsor_name' => $sponsor->name,

            'position' => $finalPosition, // ✅ Correct branch

            'is_active' => true,
        ]);

        $member->assignRole('customer');


        return redirect()->route('member.register')
            ->with('success', 'Member registered successfully!');
    }



    /*
    ======================================
    Find Last Available Position
    ======================================
    */

    private function findLastAvailable($userId, $leg)
    {

        $current = User::find($userId);

        while (true) {

            $child = User::where('placement_id', $current->id)
                ->where('position', $leg)
                ->first();

            if (!$child) {
                return $current->id; // empty spot found
            }

            $current = $child;
        }
    }

}