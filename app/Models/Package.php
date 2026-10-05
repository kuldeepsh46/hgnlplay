<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Package extends Model
{
    // Updated to match your database column names
    protected $fillable = ['name', 'actual_amount', 'discounted_amount', 'amount', 'pv', 'direct_bonus', 'direct_bonus_type', 'pair_bonus', 'pair_bonus_type', 'charges_registration_fee'];

    // Keep bonuses numeric in JSON (API) after the column became decimal
    protected $casts = [
        'direct_bonus' => 'float',
        'pair_bonus' => 'float',
        'charges_registration_fee' => 'boolean',
    ];

    // Starter Package pairs in two parts: ₹1600 first purchase / ₹1000 repurchase
    public const STARTER_PARTS = ['first', 'repeat'];

    public static function isStarter($package): bool
    {
        return strtoupper(trim((string) $package->name)) === 'STARTER PACKAGE';
    }

    // "8" for a package, "1:first" / "1:repeat" for a Starter part
    public static function classKey($packageId, $part = null): string
    {
        return $part ? $packageId . ':' . $part : (string) $packageId;
    }

    public static function parseClassKey(string $key): array
    {
        $bits = explode(':', $key, 2);

        return [(int) $bits[0], $bits[1] ?? null];
    }

    // Selectable pairing targets: one per package, two for Starter
    public static function pairingOptions($excludeId = null): array
    {
        $options = [];

        foreach (static::orderBy('id')->get() as $p) {
            if ($excludeId && $p->id == $excludeId) {
                continue;
            }

            if (static::isStarter($p)) {
                $options[] = ['key' => static::classKey($p->id, 'first'), 'name' => $p->name, 'note' => '₹' . number_format($p->actual_amount) . ' first purchase'];
                $options[] = ['key' => static::classKey($p->id, 'repeat'), 'name' => $p->name, 'note' => '₹' . number_format($p->discounted_amount ?: 1000) . ' repurchase'];
            } else {
                $options[] = ['key' => static::classKey($p->id), 'name' => $p->name, 'note' => '₹' . number_format($p->actual_amount)];
            }
        }

        return $options;
    }

    // Class keys this package (part) pairs with
    public static function pairedClassKeys($packageId, $part = null): array
    {
        return DB::table('package_pairings')
            ->where('package_id', $packageId)
            ->where(fn($q) => $part ? $q->where('package_part', $part) : $q->whereNull('package_part'))
            ->orderBy('paired_package_id')
            ->get()
            ->map(fn($r) => static::classKey($r->paired_package_id, $r->paired_part))
            ->all();
    }

    // e.g. "Seven + One Package, Starter Package (₹1,000 repurchase)"
    public function pairingSummary($part = null): string
    {
        $labels = collect(static::pairingOptions($this->id))->keyBy('key');

        return collect(static::pairedClassKeys($this->id, $part))
            ->map(function ($key) use ($labels) {
                $opt = $labels[$key] ?? null;
                if (!$opt) {
                    return null;
                }
                return str_contains($key, ':') ? "{$opt['name']} ({$opt['note']})" : $opt['name'];
            })
            ->filter()
            ->implode(', ');
    }

    // e.g. "10%" or "₹500"
    public static function formatBonus($value, $type): string
    {
        $value = (float) $value;
        $display = rtrim(rtrim(number_format($value, 2, '.', ''), '0'), '.');

        return $type === 'fixed' ? '₹' . $display : $display . '%';
    }
}
