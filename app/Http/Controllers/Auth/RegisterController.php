<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = '/dashboard';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'min:1', 'confirmed'],
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\Models\User
     */


    protected function create(array $data)
    {
        $sponsor = User::find($data['sponsor_id']);
        $leg = $data['position']; // left or right
        $placementId = $sponsor ? $sponsor->id : null;

        if ($sponsor) {
            $leftChild = User::where('placement_id', $sponsor->id)->where('position', 'left')->first();
            $rightChild = User::where('placement_id', $sponsor->id)->where('position', 'right')->first();

            if ($leg === 'left' && $leftChild) {
                $placementId = $this->findLastAvailable($leftChild->id, 'left');
            } elseif ($leg === 'right' && $rightChild) {
                $placementId = $this->findLastAvailable($rightChild->id, 'right');
            }
        }

        $user = User::create([
            'name' => $data['name'],
            'username' => $data['username'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'mobile' => $data['mobile'] ?? null,
            'pan_number' => $data['pan_number'] ?? null,
            'applied_for' => $data['applied_for'] ?? 0,
            'state' => $data['state'] ?? null,
            'city' => $data['city'] ?? null,
            'sponsor_id' => $sponsor ? $sponsor->id : null,
            'placement_id' => $placementId,
            'sponsor_name' => $sponsor ? $sponsor->name : null,
            'position' => $leg,
        ]);

        $user->assignRole('customer');
        return $user;
    }

    protected function createssss(array $data)
    {
        return User::create([
            'name' => $data['name'],
            'username' => $data['username'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'mobile' => $data['mobile'] ?? null,
            'pan_number' => $data['pan_number'] ?? null,
            'applied_for' => isset($data['applied_for']),
            'state' => $data['state'] ?? null,
            'city' => $data['city'] ?? null,
            'sponsor_id' => $data['sponsor_id'] ?? null,
            'placement_id' => $data['sponsor_id'] ?? null,
            'sponsor_name' => $data['sponsor_name'] ?? null,
            'position' => $data['position'] ?? null,
        ]);
        $user->assignRole('customer'); // Spatie role
    }



    private function findLastAvailable($userId, $leg)
    {
        $current = User::find($userId);

        while (true) {
            $child = User::where('placement_id', $current->id)
                        ->where('position', $leg)
                        ->first();

            if (!$child) {
                return $current->id; // found available placement
            }

            $current = $child;
        }
    }

}
