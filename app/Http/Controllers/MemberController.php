<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Mail\WelcomeMemberMail;
use Illuminate\Support\Facades\Mail;

class MemberController extends Controller
{
    public function create()
    {
        $user = Auth::user();
        return view('member_register', compact('user'));
    }
public function store(Request $request)
{
    // 1. Validation Logic
    $request->validate([
        // If not logged in, sponsor_id is mandatory and must exist
        'sponsor_id' => Auth::check() ? 'nullable' : 'required|exists:users,member_id',
        'username'   => 'required|unique:users,username',
        'name'       => 'required|string|max:255',
        'email'      => 'required|email|unique:users,email',
        'mobile'     => 'required|digits_between:10,12',
        'password'   => 'required|min:6|confirmed',
        'position'   => 'required|in:left,right',
        'state'      => 'required|string|max:100',
    ]);

    // 2. Identify the Sponsor
    // We use the ID from the form if the user isn't logged in
    $sponsorMemberId = Auth::check() ? Auth::user()->member_id : $request->sponsor_id;
    $sponsor = User::where('member_id', $sponsorMemberId)->first();

    if (!$sponsor) {
        return redirect()->back()->withErrors(['sponsor_id' => 'The provided Sponsor ID was not found.'])->withInput();
    }

    // 3. Binary Placement Logic
    $leg = $request->position;
    $placementId = $sponsor->id;

    $leftChild = User::where('placement_id', $sponsor->id)->where('position', 'left')->first();
    $rightChild = User::where('placement_id', $sponsor->id)->where('position', 'right')->first();

    if ($leg === 'left' && $leftChild) {
        $placementId = $this->findLastAvailable($leftChild->id, 'left');
    } elseif ($leg === 'right' && $rightChild) {
        $placementId = $this->findLastAvailable($rightChild->id, 'right');
    }

    // 4. Create Member
    // Note: I added a fallback for pan_number in case it's not in the form
    $member = User::create([
        'username'     => $request->username,
        'name'         => $request->name,
        'email'        => $request->email,
        'mobile'        => $request->mobile,
        'state'        => $request->state,
        'city'         => $request->city,
        'password'      => Hash::make($request->password),
        'sponsor_id'   => $sponsor->id,
        'placement_id' => $placementId,
        'sponsor_name' => $sponsor->name,
        'position'     => $leg,
        'is_active'    => true,
        // Ensure your User model generates a 'member_id' automatically in boot() or here
    ]);

    $member->assignRole('customer');

    // 5. Email Logic (Wrapped in try-catch so failures don't stop the redirect)
    try {
        Mail::to($member->email)->send(new WelcomeMemberMail($member, $request->password));
    } catch (\Exception $e) {
        \Log::error("Registration Email Failed: " . $e->getMessage());
    }

    // 6. Redirect with Success
    if (Auth::check()) {
        return redirect()->route('member.register')->with('success', 'New member registered successfully in your downline!');
    }

    // return redirect()->route('login')->with('success', 'Registration successful! Use your Member ID to login.');
    if (Auth::check()) {
    return redirect()->route('member.register')->with('success', 'Member registered successfully!');
}

// FOR GUESTS: Redirect back to the registration page so they see the timer
return redirect()->route('member.register')->with('success', 'Your account has been created successfully!');
}
    // public function store(Request $request)
    // {
    //     // 1. Validation Logic
    //     // We make sponsor_id 'sometimes' required if the user is not logged in
    //     $request->validate([
    //         'sponsor_id' => Auth::check() ? 'nullable' : 'required|exists:users,member_id',
    //         'username' => 'required|unique:users,username',
    //         'name' => 'required|string|max:255',
    //         'email' => 'required|email|unique:users,email',
    //         'mobile' => 'required|digits_between:10,11',
    //         'password' => 'required|min:6|confirmed',
    //         'position' => 'required|in:left,right',
    //         'state' => 'required|string|max:100',
    //     ]);

    //     // 2. Identify the Sponsor
    //     // Condition: If Auth user exists, use their member_id, else get from the form
    //     $user = Auth::user();
    //     $sponsorMemberId = $user ? $user->member_id : $request->sponsor_id;

    //     $sponsor = User::where('member_id', $sponsorMemberId)->first();

    //     if (!$sponsor) {
    //         return redirect()
    //             ->back()
    //             ->withErrors(['sponsor_id' => 'The provided Sponsor ID is invalid.']);
    //     }

    //     // 3. Binary Placement Logic
    //     $leg = $request->position; // 'left' or 'right'
    //     $placementId = $sponsor->id; // Default placement is directly under sponsor

    //     // Check if the immediate spot is taken
    //     $leftChild = User::where('placement_id', $sponsor->id)->where('position', 'left')->first();
    //     $rightChild = User::where('placement_id', $sponsor->id)->where('position', 'right')->first();

    //     // If the selected leg is occupied, find the last available node in that lineage
    //     if ($leg === 'left' && $leftChild) {
    //         $placementId = $this->findLastAvailable($leftChild->id, 'left');
    //     } elseif ($leg === 'right' && $rightChild) {
    //         $placementId = $this->findLastAvailable($rightChild->id, 'right');
    //     }

    //     // 4. Create the New Member
    //     $member = User::create([
    //         'username' => $request->username,
    //         'name' => $request->name,
    //         'email' => $request->email,
    //         'mobile' => $request->mobile,
    //         'pan_number' => $request->pan_number,
    //         'state' => $request->state,
    //         'city' => $request->city,
    //         'password' => Hash::make($request->password),
    //         'sponsor_id' => $sponsor->id, // The person who referred them
    //         'placement_id' => $placementId, // The person they are actually sitting under
    //         'sponsor_name' => $sponsor->name,
    //         'position' => $leg,
    //         'is_active' => true,
    //     ]);

    //     // 5. Assign Role & Post-Registration Actions
    //     $member->assignRole('customer');

    //     // Send Welcome Email
    //     Mail::to($member->email)->send(new WelcomeMemberMail($member, $request->password));
    //     // try {
    //     //     Mail::to($member->email)->send(new WelcomeMemberMail($member, $request->password));
    //     // } catch (\Exception $e) {
    //     //     // Log error but don't stop the registration process
    //     //     Log::error("Mail failed for user {$member->username}: " . $e->getMessage());
    //     // }

    //     // 6. Redirect based on Auth status
    //     if (Auth::check()) {
    //         return redirect()->route('member.register')->with('success', 'Member registered successfully in your downline!');
    //     }

    //     return redirect()->route('login')->with('success', 'Registration successful! You can now login with your credentials.');
    // }
    // public function store(Request $request)
    // {
    //     $request->validate([
    //         'username' => 'required|unique:users,username',
    //         'name' => 'required|string|max:255',
    //         'email' => 'required|email|unique:users,email',
    //         'mobile' => 'required|digits_between:10,11',
    //         'password' => 'required|min:6|confirmed',
    //         'position' => 'required|in:left,right',
    //         'state' => 'required|string|max:100',
    //     ]);
    //     $sponsor = Auth::user();
    //     $leg = $request->position; // left or right
    //     $placementId = $sponsor->id;

    //     $leftChild = User::where('placement_id', $sponsor->id)->where('position', 'left')->first();
    //     $rightChild = User::where('placement_id', $sponsor->id)->where('position', 'right')->first();

    //     if ($leg === 'left' && $leftChild) {
    //         $placementId = $this->findLastAvailable($leftChild->id, 'left');
    //     } elseif ($leg === 'right' && $rightChild) {
    //         $placementId = $this->findLastAvailable($rightChild->id, 'right');
    //     }
    //     // $encryptedPassword = Hash::make($request->password);
    //     $member = User::create([
    //         'username' => $request->username,
    //         'name' => $request->name,
    //         'email' => $request->email,
    //         'mobile' => $request->mobile,
    //         'pan_number' => $request->pan_number,
    //         'state' => $request->state,
    //         'city' => $request->city,
    //         'password' => Hash::make($request->password),
    //         'sponsor_id' => $sponsor->id,
    //         'placement_id' => $placementId, // ✅ updated
    //         'sponsor_name' => $sponsor->name,
    //         'position' => $leg,
    //         'is_active' => true,
    //     ]);

    //     $member->assignRole('customer');

    //     // $request->validate([
    //     //     'username' => 'required|unique:users,username',
    //     //     'name' => 'required|string|max:255',
    //     //     'email' => 'required|email|unique:users,email',
    //     //     'mobile' => 'required|digits_between:10,11',
    //     //     'password' => 'required|min:6|confirmed',
    //     //     'position' => 'required|in:left,right',
    //     // ]);

    //     // $sponsor = Auth::user();

    //     // // Create new user (downline)
    //     // $member = User::create([
    //     //     'username' => $request->username,
    //     //     'name' => $request->name,
    //     //     'email' => $request->email,
    //     //     'mobile' => $request->mobile,
    //     //     'pan_number' => $request->pan_number,
    //     //     'state' => $request->state,
    //     //     'city' => $request->city,
    //     //     'password' => Hash::make($request->password),
    //     //     'sponsor_id' => $sponsor->id,
    //     //     'placement_id' => $sponsor->id, // ✅ Add this line
    //     //     'sponsor_name' => $sponsor->name,
    //     //     'position' => $request->position,
    //     //     'is_active' => true,
    //     // ]);

    //     // // Assign role
    //     // $member->assignRole('customer');
    //     Mail::to($member->email)->send(new WelcomeMemberMail($member, $request->password));

    //     return redirect()->route('member.register')->with('success', 'Member registered successfully!');
    // }

    private function findLastAvailable($userId, $leg)
    {
        $current = User::find($userId);

        while (true) {
            $child = User::where('placement_id', $current->id)->where('position', $leg)->first();

            if (!$child) {
                return $current->id; // found available placement
            }

            $current = $child;
        }
    }
    // public function checkId(Request $request)
    // {
    //     $member = \App\Models\User::where('member_id', $request->member_id)->first();

    //     if ($member) {
    //         return response()->json([
    //             'success' => true,
    //             'name' => $member->name,
    //         ]);
    //     }

    //     return response()->json(['success' => false]);
    // }
    public function checkId(Request $request)
    {
        $user = User::where('member_id', $request->member_id)->first();

        if ($user) {
            return response()->json([
                'success' => true,
                'name' => $user->username,
                // 🔹 Send the target user's count, not the admin's
                'investment_count' => (int) $user->investment_count,
            ]);
        }

        return response()->json(['success' => false]);
    }
}
