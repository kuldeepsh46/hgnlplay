<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Setting;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class ProfileController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        return view('profile', compact('user'));
    }
    public function edit()
    {
        $user = Auth::user();
        return view('profile.edit', compact('user'));
    }

    public function update(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'name' => 'required|string|max:255',
            'mobile' => 'required',
            'city' => 'required|string',
            'state' => 'required|string',
            'pan_number' => 'nullable|string',
            'address' => 'nullable|string',
        ]);

        $user->update($request->only(['name', 'mobile', 'city', 'state', 'pan_number', 'address', 'pincode', 'dob', 'account_number', 'account_holder_name', 'bank_name', 'branch_name', 'bank_address', 'ifsc_code', 'nominee_name', 'relation', 'nominee_dob', 'nominee_gender']));

        return redirect()->route('profile')->with('success', 'Profile updated successfully.');
    }

    public function kyc()
    {
        $user = Auth::user();
        return view('profile.kyc', compact('user'));
    }

    public function uploadKyc(Request $request)
    {
        // dd($request()->all());

        $request->validate([
            'id_proof' => 'required|file|mimes:jpg,jpeg,png,pdf|max:2048',
            'address_proof' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
            'account_proof' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ]);

        $user = Auth::user();

        if ($request->hasFile('id_proof')) {
            $user->id_proof = $request->file('id_proof')->store('kyc', 'public');
        }
        if ($request->hasFile('address_proof')) {
            $user->address_proof = $request->file('address_proof')->store('kyc', 'public');
        }
        if ($request->hasFile('account_proof')) {
            $user->account_proof = $request->file('account_proof')->store('kyc', 'public');
        }

        $user->save();

        return back()->with('success', 'KYC uploaded successfully.');
    }
    public function kycUpload(Request $r)
    {
        $user = Auth::user();
        // dd($user->getFillable());

        $r->validate([
            'id_proof' => 'nullable|file',
            'address_proof' => 'nullable|file',
            'account_proof' => 'nullable|file',
        ]);

        $data = [];
        // ✅ Store each file if uploaded
        if ($r->hasFile('id_proof')) {
            $file = $r->file('id_proof');
            $filename = time() . '_id_' . $file->getClientOriginalName();
            $file->move(public_path('uploads/kyc/id_proof'), $filename);
            $data['id_proof'] = 'uploads/kyc/id_proof/' . $filename;
        }

        if ($r->hasFile('address_proof')) {
            $file = $r->file('address_proof');
            $filename = time() . '_address_' . $file->getClientOriginalName();
            $file->move(public_path('uploads/kyc/address_proof'), $filename);
            $data['address_proof'] = 'uploads/kyc/address_proof/' . $filename;
        }

        if ($r->hasFile('account_proof')) {
            $file = $r->file('account_proof');
            $filename = time() . '_account_' . $file->getClientOriginalName();
            $file->move(public_path('uploads/kyc/account_proof'), $filename);
            $data['account_proof'] = 'uploads/kyc/account_proof/' . $filename;
        }

        $user->update($data);

        return back()->with('success', 'KYC documents uploaded successfully!');
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'new_password' => ['required', 'min:8', 'confirmed'],
        ]);

        $user = Auth::user();

        // if (!Hash::check($request->current_password, $user->password)) {
        //     return back()->with('error', 'Current password is incorrect.');
        // }

        $user->update(['password' => Hash::make($request->new_password)]);

        return back()->with('success', 'Password updated successfully!');
    }

    // app/Http/Controllers/ProfileController.php

    public function walletFundRequest()
    {
        $user = Auth::user();

        // recent requests for the table
        $requests = DB::table('fund_requests')->where('user_id', $user->id)->orderByDesc('id')->get();
        // $qr_image = Setting::where('id', 1)->value('qr_scanner_img');
        // return view('wallet-fund-request', compact('user', 'requests', 'qr_image'));
        $upi_qr = Setting::where('id', 1)->value('qr_scanner_img');
        $usdt_qr = Setting::where('id', 2)->value('qr_scanner_img');

        return view('wallet-fund-request', compact('user', 'requests', 'upi_qr', 'usdt_qr'));
    }

    public function storeWalletFundRequest(Request $r)
    {
        $r->validate([
            'amount' => 'required|numeric|min:1',
            'deposit_date' => 'required|date',
            'payment_mode' => 'required|string',
            'bank_name' => 'nullable|string',
            'account_number' => 'nullable|string',
            'transaction_remark' => 'nullable|string',
            'attachment' => 'nullable|file',
        ]);

        $user = Auth::user();

        // If form fields empty, fall back to user's saved bank details
        $bankName = $r->filled('bank_name') ? $r->bank_name : $user->bank_name;
        $accountNumber = $r->filled('account_number') ? $r->account_number : $user->account_number;

        // upload file to public/uploads/fund_requests
        $path = null;
        if ($r->hasFile('attachment')) {
            $dir = public_path('uploads/fund_requests');
            if (!is_dir($dir)) {
                @mkdir($dir, 0755, true);
            }
            $file = $r->file('attachment');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move($dir, $filename);
            $path = 'public/uploads/fund_requests/' . $filename;
        }

        DB::table('fund_requests')->insert([
            'user_id' => $user->id,
            'amount' => $r->amount,
            'deposit_date' => $r->deposit_date,
            'payment_mode' => $r->payment_mode,
            'bank_name' => $bankName, // saved to fund_requests
            'account_number' => $accountNumber, // saved to fund_requests
            'transaction_remark' => $r->transaction_remark,
            'attachment' => $path,
            'status' => 'pending',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return back()->with('success', 'Fund request submitted successfully!');
    }
}
