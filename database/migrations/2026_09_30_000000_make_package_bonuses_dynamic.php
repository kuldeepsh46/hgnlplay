<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/*
|--------------------------------------------------------------------------
| Dynamic Package Bonuses + Package Pairing
|--------------------------------------------------------------------------
| - direct_bonus / pair_bonus can now be a percentage or a fixed amount.
| - package_pairings: which packages can pair with which (stored both ways).
| - user_pair_volumes: per-user processed pair volume, per pairing pool.
|
| Existing packages are seeded so payouts stay exactly as they were when
| the values were hardcoded in TopupController:
|   Starter Package         -> ₹500 fixed direct, ₹300 fixed pair
|   Normal (< ₹50,000)      -> 10% direct, 10% pair, all paired together
|                              (+ Starter, whose ₹1000 EMI joins this pool)
|   Repurchase Booster      -> 0 / 0 (Matrix only)
|   ₹50,000 and above       -> 0 / 0 (level commission, no pair income)
|--------------------------------------------------------------------------
*/
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('packages', function (Blueprint $table) {
            $table->decimal('direct_bonus', 10, 2)->default(0)->change();
            $table->decimal('pair_bonus', 10, 2)->default(0)->change();
            $table->string('direct_bonus_type', 10)->default('percent')->after('direct_bonus');
            $table->string('pair_bonus_type', 10)->default('percent')->after('pair_bonus');
        });

        Schema::create('package_pairings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('package_id')->constrained('packages')->cascadeOnDelete();
            $table->foreignId('paired_package_id')->constrained('packages')->cascadeOnDelete();
            $table->unique(['package_id', 'paired_package_id']);
        });

        Schema::create('user_pair_volumes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('pool_key', 191);
            $table->decimal('processed_volume', 15, 2)->default(0);
            $table->timestamps();
            $table->unique(['user_id', 'pool_key']);
        });

        $packages = DB::table('packages')->get();

        $starter = $packages->first(fn($p) => strtoupper(trim((string) $p->name)) === 'STARTER PACKAGE');
        $normalIds = [];

        foreach ($packages as $p) {
            $name = strtoupper(trim((string) $p->name));
            $amount = (float) ($p->amount ?? $p->actual_amount ?? 0);

            if ($name === 'STARTER PACKAGE') {
                $values = ['direct_bonus' => 500, 'direct_bonus_type' => 'fixed', 'pair_bonus' => 300, 'pair_bonus_type' => 'fixed'];
            } elseif ($name === 'REPURCHASE BOOSTER PACKAGE' || $amount >= 50000) {
                $values = ['direct_bonus' => 0, 'direct_bonus_type' => 'percent', 'pair_bonus' => 0, 'pair_bonus_type' => 'percent'];
            } else {
                $values = ['direct_bonus' => 10, 'direct_bonus_type' => 'percent', 'pair_bonus' => 10, 'pair_bonus_type' => 'percent'];
                $normalIds[] = $p->id;
            }

            DB::table('packages')->where('id', $p->id)->update($values);
        }

        // Normal packages all pair with each other, and with Starter (only
        // Starter's ₹1000 EMI volume enters this pool — see TopupController).
        $poolIds = $normalIds;
        if ($starter) {
            $poolIds[] = $starter->id;
        }

        $rows = [];
        foreach ($poolIds as $a) {
            foreach ($poolIds as $b) {
                if ($a !== $b) {
                    $rows[] = ['package_id' => $a, 'paired_package_id' => $b];
                }
            }
        }
        if ($rows) {
            DB::table('package_pairings')->insert($rows);
        }

        // Carry over already-processed pair volume so nothing is re-paid.
        sort($poolIds);
        $normalKey = implode(',', $poolIds);
        $starterKey = $starter ? 'starter:' . $starter->id : null;

        DB::table('users')
            ->where(function ($q) {
                $q->where('normal_pair_processed_volume', '>', 0)->orWhere('starter_pair_processed_volume', '>', 0);
            })
            ->orderBy('id')
            ->chunk(500, function ($users) use ($normalKey, $starterKey) {
                $inserts = [];
                foreach ($users as $u) {
                    if ((float) $u->normal_pair_processed_volume > 0 && $normalKey !== '') {
                        $inserts[] = ['user_id' => $u->id, 'pool_key' => $normalKey, 'processed_volume' => $u->normal_pair_processed_volume, 'created_at' => now(), 'updated_at' => now()];
                    }
                    if ((float) $u->starter_pair_processed_volume > 0 && $starterKey) {
                        $inserts[] = ['user_id' => $u->id, 'pool_key' => $starterKey, 'processed_volume' => $u->starter_pair_processed_volume, 'created_at' => now(), 'updated_at' => now()];
                    }
                }
                if ($inserts) {
                    DB::table('user_pair_volumes')->insert($inserts);
                }
            });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_pair_volumes');
        Schema::dropIfExists('package_pairings');

        Schema::table('packages', function (Blueprint $table) {
            $table->dropColumn(['direct_bonus_type', 'pair_bonus_type']);
        });
    }
};
