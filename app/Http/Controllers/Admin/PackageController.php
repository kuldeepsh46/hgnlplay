<?php
// namespace App\Http\Controllers;
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;

use App\Models\Package;
use Illuminate\Http\Request;

class PackageController extends Controller
{
    public function index()
    {
        $packages = Package::latest()->get();
        return view('admin.packages.index', compact('packages'));
    }

    public function create()
    {
        return view('admin.packages.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'         => 'required|string|max:255',
            'amount'       => 'required|integer|min:0',
            'pv'           => 'required|integer|min:0',
            'direct_bonus' => 'required|integer|min:0',
            'pair_bonus'   => 'required|integer|min:0',
        ]);

        Package::create($data);

        return redirect()->route('packages.index')->with('success', 'Package created successfully!');
    }

    public function edit(Package $package)
    {
        return view('admin.packages.edit', compact('package'));
    }

    public function update(Request $request, Package $package)
    {
        $data = $request->validate([
            'name'         => 'required|string|max:255',
            'amount'       => 'required|integer|min:0',
            'pv'           => 'required|integer|min:0',
            'direct_bonus' => 'required|integer|min:0',
            'pair_bonus'   => 'required|integer|min:0',
        ]);

        $package->update($data);

        return redirect()->route('packages.index')->with('success', 'Package updated successfully!');
    }

    public function destroy(Package $package)
    {
        $package->delete();
        return redirect()->route('packages.index')->with('success', 'Package deleted successfully!');
    }
}