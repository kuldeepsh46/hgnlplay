<?php
// namespace App\Http\Controllers;
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;

use App\Models\Package;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PackageController extends Controller
{
    public function index()
    {
        $packages = Package::with('pairedPackages')->latest()->get();
        return view('admin.packages.index', compact('packages'));
    }

    public function create()
    {
        $allPackages = Package::orderBy('id')->get();
        return view('admin.packages.create', compact('allPackages'));
    }

    // public function store(Request $request)
    // {
    //     $data = $request->validate([
    //         'name' => 'required|string|max:255',
    //         'amount' => 'required|integer|min:0',
    //         'pv' => 'required|integer|min:0',
    //         'direct_bonus' => 'required|integer|min:0',
    //         'pair_bonus' => 'required|integer|min:0',
    //     ]);

    //     Package::create($data);

    //     return redirect()->route('packages.index')->with('success', 'Package created successfully!');
    // }
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'actual_amount' => 'required|integer|min:0',
            'discounted_amount' => 'nullable|integer|min:0',
            // 'amount' => 'required|integer|min:0',
            'pv' => 'required|integer|min:0',
        ] + $this->bonusRules($request));
        // dd($data);
$data['amount'] = $data['actual_amount'];
        $pairedIds = $data['paired_packages'] ?? [];
        unset($data['paired_packages']);

        DB::transaction(function () use ($data, $pairedIds) {
            $package = Package::create($data);
            $this->syncPairings($package, $pairedIds);
        });

        return redirect()->route('packages.index')->with('success', 'Package created successfully!');
    }

    public function edit(Package $package)
    {
        $allPackages = Package::where('id', '!=', $package->id)->orderBy('id')->get();
        $pairedIds = $package->pairedPackages()->pluck('packages.id')->all();
        return view('admin.packages.edit', compact('package', 'allPackages', 'pairedIds'));
    }

    public function update(Request $request, Package $package)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'amount' => 'required|integer|min:0',
            'pv' => 'required|integer|min:0',
        ] + $this->bonusRules($request, $package->id));

        $pairedIds = $data['paired_packages'] ?? [];
        unset($data['paired_packages']);

        DB::transaction(function () use ($package, $data, $pairedIds) {
            $package->update($data);
            $this->syncPairings($package, $pairedIds);
        });

        return redirect()->route('packages.index')->with('success', 'Package updated successfully!');
    }

    public function destroy(Package $package)
    {
        $package->delete();
        return redirect()->route('packages.index')->with('success', 'Package deleted successfully!');
    }

    private function bonusRules(Request $request, $ignoreId = null): array
    {
        $percentMax = fn($field) => $request->input($field . '_type') === 'percent' ? '|max:100' : '';

        return [
            'direct_bonus' => 'required|numeric|min:0' . $percentMax('direct_bonus'),
            'direct_bonus_type' => 'required|in:percent,fixed',
            'pair_bonus' => 'required|numeric|min:0' . $percentMax('pair_bonus'),
            'pair_bonus_type' => 'required|in:percent,fixed',
            'paired_packages' => 'nullable|array',
            'paired_packages.*' => 'integer|exists:packages,id' . ($ignoreId ? '|not_in:' . $ignoreId : ''),
        ];
    }

    // Pairing is stored both ways: if A pairs with B, B also pairs with A.
    private function syncPairings(Package $package, array $pairedIds): void
    {
        $pairedIds = array_values(array_unique(array_map('intval', $pairedIds)));

        DB::table('package_pairings')
            ->where('package_id', $package->id)
            ->orWhere('paired_package_id', $package->id)
            ->delete();

        $rows = [];
        foreach ($pairedIds as $id) {
            if ($id === $package->id) {
                continue;
            }
            $rows[] = ['package_id' => $package->id, 'paired_package_id' => $id];
            $rows[] = ['package_id' => $id, 'paired_package_id' => $package->id];
        }

        if ($rows) {
            DB::table('package_pairings')->insert($rows);
        }
    }
}
