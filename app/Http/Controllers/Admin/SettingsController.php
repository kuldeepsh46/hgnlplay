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
    public function index(Request $request, $id)
    {
        // Fetch the first row of settings
        $settings = DB::table('settings')->where('id', $id)->first();
        // return view('admin.settings.index', compact('settings'));
        if ($id == 1) {
            return view('admin.settings.index', compact('settings'));
        }
        return view('admin.settings.usdt', compact('settings'));
    }
    // public function usdt()
    // {
    //     // Fetch the first row of settings
    //     $settings = DB::table('settings')->first();
    //     return view('admin.settings.usdt', compact('settings'));
    // }

    // public function updateScanner(Request $request)
    // {
    //     $request->validate([
    //         'qr_image' => 'required|image|mimes:jpeg,png,jpg|max:2048',
    //     ]);

    //     if ($request->hasFile('qr_image')) {
    //         $image = $request->file('qr_image');

    //         // 1. Generate a unique name
    //         $name = 'qr_scanner_' . time() . '.' . $image->getClientOriginalExtension();

    //         // 2. Set the destination to public/assets/images/
    //         $destinationPath = public_path('assets/images/');

    //         // 3. Move the file
    //         $image->move($destinationPath, $name);

    //         // 4. Create the relative path for the database
    //         $dbPath = 'assets/images/' . $name;

    //         // 5. Save to database using DB Facade (Avoids "Class not found" error)
    //         \Illuminate\Support\Facades\DB::table('settings')->updateOrInsert(
    //             ['id' => 1], // Updates the first row
    //             [
    //                 'qr_scanner_img' => $dbPath,
    //                 'updated_at' => now(),
    //             ],
    //         );

    //         return back()->with('success', 'QR Scanner updated and saved to assets/images!');
    //     }

    //     return back()->with('error', 'Please select a valid image.');
    // }
    // public function updateScanner(Request $request, $id)
    // {
    //     $request->validate([
    //         'qr_image' => 'required|image|mimes:jpeg,png,jpg|max:2048',
    //     ]);

    //     if ($request->hasFile('qr_image')) {
    //         $image = $request->file('qr_image');

    //         // 1. Fetch current record to check for existing file
    //         $oldRecord = \Illuminate\Support\Facades\DB::table('settings')->where('id', $id)->first();

    //         // 2. Generate a unique name
    //         $name = 'qr_' . $id . '_' . time() . '.' . $image->getClientOriginalExtension();
    //         $destinationPath = public_path('assets/images/');

    //         // 3. Delete old file ONLY if record and file exist
    //         if ($oldRecord && !empty($oldRecord->qr_scanner_img)) {
    //             $oldFile = public_path($oldRecord->qr_scanner_img);
    //             if (file_exists($oldFile)) {
    //                 @unlink($oldFile);
    //             }
    //         }

    //         // 4. Move the new file
    //         $image->move($destinationPath, $name);
    //         $dbPath = 'assets/images/' . $name;

    //         // 5. updateOrInsert handles the "No Entry" logic automatically
    //         \Illuminate\Support\Facades\DB::table('settings')->updateOrInsert(
    //             ['id' => $id],
    //             [
    //                 'qr_scanner_img' => $dbPath,
    //                 'updated_at' => now(),
    //                 // Add default values here if it's a new row
    //                 'created_at' => $oldRecord ? $oldRecord->created_at : now(),
    //             ],
    //         );

    //         $type = $id == 1 ? 'QR Scanner' : 'USDT Scanner';
    //         return back()->with('success', "$type updated successfully!");
    //     }

    //     return back()->with('error', 'Please select a valid image.');
    // }

    public function updateScanner(Request $request, $id)
    {
        $request->validate([
            'qr_image' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        if ($request->hasFile('qr_image')) {
            $image = $request->file('qr_image');

            // 1. Fetch current record
            $oldRecord = \Illuminate\Support\Facades\DB::table('settings')->where('id', $id)->first();

            // 2. Prepare file details
            $name = 'qr_' . $id . '_' . time() . '.' . $image->getClientOriginalExtension();
            $destinationPath = public_path('assets/images/');

            // 3. Cleanup: Delete old file ONLY if it exists in DB and on Disk
            if ($oldRecord && !empty($oldRecord->qr_scanner_img)) {
                $oldFile = public_path($oldRecord->qr_scanner_img);
                if (file_exists($oldFile)) {
                    @unlink($oldFile);
                }
            }

            // 4. Move new file to public/assets/images/
            $image->move($destinationPath, $name);
            $dbPath = 'assets/images/' . $name;

            // 5. Build the data array
            $data = [
                'qr_scanner_img' => $dbPath,
                'updated_at' => now(),
            ];

            // 6. Handle "First Time" Creation: Add created_at if record doesn't exist
            if (!$oldRecord) {
                $data['created_at'] = now();
            }

            // 7. Execute: Updates if ID exists, Creates if ID is missing
            \Illuminate\Support\Facades\DB::table('settings')->updateOrInsert(['id' => $id], $data);

            $type = $id == 1 ? 'QR Scanner' : 'USDT Scanner';
            return back()->with('success', "$type " . ($oldRecord ? 'updated' : 'created') . ' successfully!');
        }

        return back()->with('error', 'Please select a valid image.');
    }
}
