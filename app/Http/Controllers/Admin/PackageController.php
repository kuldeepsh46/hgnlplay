<?php
// namespace App\Http\Controllers;
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;

use App\Models\Package;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class PackageController extends Controller
{
    public function index()
    {
        $packages = Package::latest()->get();
        return view('admin.packages.index', compact('packages'));
    }

    public function create()
    {
        $pairingOptions = Package::pairingOptions();
        return view('admin.packages.create', compact('pairingOptions'));
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
        $pairings = $this->pairingInput($request, $data);

        DB::transaction(function () use ($data, $pairings) {
            $package = Package::create($data);
            $this->syncPairings($package, $pairings);
        });

        return redirect()->route('packages.index')->with('success', 'Package created successfully!');
    }

    public function edit(Package $package)
    {
        $pairingOptions = Package::pairingOptions($package->id);
        $isStarter = Package::isStarter($package);
        $pairedKeys = [];
        foreach ($isStarter ? Package::STARTER_PARTS : [null] as $part) {
            $pairedKeys[$part ?? 'all'] = Package::pairedClassKeys($package->id, $part);
        }
        return view('admin.packages.edit', compact('package', 'pairingOptions', 'isStarter', 'pairedKeys'));
    }

    public function update(Request $request, Package $package)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'amount' => 'required|integer|min:0',
            'pv' => 'required|integer|min:0',
        ] + $this->bonusRules($request, $package));

        $pairings = $this->pairingInput($request, $data, $package);

        DB::transaction(function () use ($package, $data, $pairings) {
            $package->update($data);
            $this->syncPairings($package, $pairings);
        });

        return redirect()->route('packages.index')->with('success', 'Package updated successfully!');
    }

    public function destroy(Package $package)
    {
        $package->delete();
        return redirect()->route('packages.index')->with('success', 'Package deleted successfully!');
    }

    private function bonusRules(Request $request, ?Package $package = null): array
    {
        $percentMax = fn($field) => $request->input($field . '_type') === 'percent' ? '|max:100' : '';
        $validKeys = Rule::in(array_column(Package::pairingOptions($package?->id), 'key'));

        return [
            'direct_bonus' => 'required|numeric|min:0' . $percentMax('direct_bonus'),
            'direct_bonus_type' => 'required|in:percent,fixed',
            'pair_bonus' => 'required|numeric|min:0' . $percentMax('pair_bonus'),
            'pair_bonus_type' => 'required|in:percent,fixed',
            'charges_registration_fee' => 'required|boolean',
            'paired_packages' => 'nullable|array',
            'paired_packages.*' => ['string', $validKeys],
            'paired_packages_first' => 'nullable|array',
            'paired_packages_first.*' => ['string', $validKeys],
            'paired_packages_repeat' => 'nullable|array',
            'paired_packages_repeat.*' => ['string', $validKeys],
        ];
    }

    // Selected pairings per own part. Starter Package has two parts
    // (first purchase / repurchase); every other package has one (null).
    private function pairingInput(Request $request, array &$data, ?Package $package = null): array
    {
        unset($data['paired_packages'], $data['paired_packages_first'], $data['paired_packages_repeat']);

        if ($package && Package::isStarter($package)) {
            return [
                'first' => $request->input('paired_packages_first', []),
                'repeat' => $request->input('paired_packages_repeat', []),
            ];
        }

        return ['' => $request->input('paired_packages', [])];
    }

    // Pairing is stored both ways: if A pairs with B, B also pairs with A.
    private function syncPairings(Package $package, array $pairings): void
    {
        foreach ($pairings as $part => $keys) {
            $part = $part === '' ? null : $part;
            $samePart = fn($q, $column) => $part ? $q->where($column, $part) : $q->whereNull($column);

            DB::table('package_pairings')
                ->where(fn($q) => $samePart($q->where('package_id', $package->id), 'package_part'))
                ->orWhere(fn($q) => $samePart($q->where('paired_package_id', $package->id), 'paired_part'))
                ->delete();

            $rows = [];
            foreach (array_unique($keys) as $key) {
                [$otherId, $otherPart] = Package::parseClassKey($key);
                if ($otherId === $package->id) {
                    continue;
                }
                $rows[] = ['package_id' => $package->id, 'package_part' => $part, 'paired_package_id' => $otherId, 'paired_part' => $otherPart];
                $rows[] = ['package_id' => $otherId, 'package_part' => $otherPart, 'paired_package_id' => $package->id, 'paired_part' => $part];
            }

            if ($rows) {
                DB::table('package_pairings')->insert($rows);
            }
        }
    }
}
