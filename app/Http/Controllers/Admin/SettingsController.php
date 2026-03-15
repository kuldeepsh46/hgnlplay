<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use App\Models\Setting;

class SettingsController extends Controller
{
    /**
     * Show the settings page
     */
    public function index()
    {
        // Fetch the first row of settings
        $settings = DB::table('settings')->first();
        return view('admin.settings.index', compact('settings'));
    }

    /**
     * Update the QR Scanner Image
     */
    // public function updateQrScanner(Request $request)
    // {
    //     $request->validate([
    //         'qr_image' => 'required|image|mimes:jpeg,png,jpg|max:2048',
    //     ]);

    //     if ($request->hasFile('qr_image')) {
    //         // 1. Optional: Delete old image to save space
    //         $currentSetting = DB::table('settings')->first();
    //         if ($currentSetting && $currentSetting->qr_scanner_img) {
    //             $oldPath = public_path($currentSetting->qr_scanner_img);
    //             if (File::exists($oldPath)) {
    //                 File::delete($oldPath);
    //             }
    //         }

    //         // 2. Process and move the new image
    //         $image = $request->file('qr_image');
    //         $fileName = 'qr_scanner_' . time() . '.' . $image->getClientOriginalExtension();
    //         $image->move(public_path('uploads/admin'), $fileName);
    //         $dbPath = 'uploads/admin/' . $fileName;

    //         // 3. Update or Insert the setting in the DB
    //         DB::table('settings')->updateOrInsert(
    //             ['id' => 1],
    //             [
    //                 'qr_scanner_img' => $dbPath,
    //                 'updated_at' => now()
    //             ]
    //         );

    //         return back()->with('success', 'QR Scanner updated successfully!');
    //     }

    //     return back()->with('error', 'Please select a valid image.');
    // }
    public function updateScanner(Request $request)
    {
        $request->validate([
            'qr_image' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        if ($request->hasFile('qr_image')) {
            $image = $request->file('qr_image');

            // 1. Generate a unique name
            $name = 'qr_scanner_' . time() . '.' . $image->getClientOriginalExtension();

            // 2. Set the destination to public/assets/images/
            $destinationPath = public_path('assets/images/');

            // 3. Move the file
            $image->move($destinationPath, $name);

            // 4. Create the relative path for the database
            $dbPath = 'assets/images/' . $name;

            // 5. Save to database using DB Facade (Avoids "Class not found" error)
            \Illuminate\Support\Facades\DB::table('settings')->updateOrInsert(
                ['id' => 1], // Updates the first row
                [
                    'qr_scanner_img' => $dbPath,
                    'updated_at' => now(),
                ],
            );

            return back()->with('success', 'QR Scanner updated and saved to assets/images!');
        }

        return back()->with('error', 'Please select a valid image.');
    }
}
