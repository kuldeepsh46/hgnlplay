<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/*
|--------------------------------------------------------------------------
| Registration Fee Setting + Starter Pairing Parts + Per-Leg Pair Volume
|--------------------------------------------------------------------------
| Runs after 2026_09_30_000000_make_package_bonuses_dynamic.
|
| - packages.charges_registration_fee: add ₹100 on a member's first
|   purchase. Starter Package and Repurchase Booster = No (as before),
|   every other package = Yes (as before).
|
| - package_pairings.package_part / paired_part: Starter Package pairs in
|   two parts — 'first' (₹1600 first purchase) and 'repeat' (₹1000
|   EMI/repurchase). Every other package has NULL. Existing Starter
|   pairings:
|     * with packages that existed when dynamic pairing was introduced
|       -> 'repeat' (only Starter's ₹1000 repurchase ever matched them)
|     * with packages created after that -> 'first' (the ₹1600 Starter
|       purchase the admin meant when ticking it)
|
| - user_pair_consumptions: per user, per leg, per package (part) volume
|   already used for pair income, so the same volume is never paid twice
|   when packages have different pairings. Filled per user by
|   TopupController the first time it is needed, from user_pair_volumes.
|--------------------------------------------------------------------------
*/
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('packages', function (Blueprint $table) {
            $table->boolean('charges_registration_fee')->default(true)->after('pair_bonus_type');
        });

        DB::table('packages')
            ->whereRaw('UPPER(TRIM(name)) IN (?, ?)', ['STARTER PACKAGE', 'REPURCHASE BOOSTER PACKAGE'])
            ->update(['charges_registration_fee' => false]);

        Schema::table('package_pairings', function (Blueprint $table) {
            $table->string('package_part', 10)->nullable()->after('package_id');
            $table->string('paired_part', 10)->nullable()->after('paired_package_id');
            $table->unique(['package_id', 'package_part', 'paired_package_id', 'paired_part'], 'package_pairings_unique');
            $table->dropUnique(['package_id', 'paired_package_id']);
        });

        $starterId = DB::table('packages')->whereRaw('UPPER(TRIM(name)) = ?', ['STARTER PACKAGE'])->value('id');

        if ($starterId) {
            // Packages paired when dynamic pairing was introduced: the pool
            // seeded first by that migration (e.g. "1,2,3,8"); otherwise the
            // packages created before it.
            $seededKey = DB::table('user_pair_volumes')->where('pool_key', 'not like', 'starter:%')->orderBy('id')->value('pool_key');

            if ($seededKey) {
                $legacyIds = array_map('intval', explode(',', $seededKey));
            } else {
                $introducedAt = DB::table('user_pair_volumes')->min('created_at') ?? now();
                $legacyIds = DB::table('packages')
                    ->where(fn($q) => $q->where('created_at', '<', $introducedAt)->orWhereNull('created_at'))
                    ->pluck('id')
                    ->all();
            }

            $partFor = fn($otherId) => in_array($otherId, $legacyIds) ? 'repeat' : 'first';

            foreach (DB::table('package_pairings')->where('package_id', $starterId)->get() as $row) {
                DB::table('package_pairings')->where('id', $row->id)->update(['package_part' => $partFor($row->paired_package_id)]);
            }

            foreach (DB::table('package_pairings')->where('paired_package_id', $starterId)->get() as $row) {
                DB::table('package_pairings')->where('id', $row->id)->update(['paired_part' => $partFor($row->package_id)]);
            }
        }

        Schema::create('user_pair_consumptions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('leg', 5);
            $table->string('class_key', 30);
            $table->decimal('consumed_volume', 15, 2)->default(0);
            $table->timestamps();
            $table->unique(['user_id', 'leg', 'class_key']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_pair_consumptions');

        DB::table('package_pairings')->where('package_part', 'first')->orWhere('paired_part', 'first')->delete();

        Schema::table('package_pairings', function (Blueprint $table) {
            $table->unique(['package_id', 'paired_package_id']);
            $table->dropUnique('package_pairings_unique');
            $table->dropColumn(['package_part', 'paired_part']);
        });

        Schema::table('packages', function (Blueprint $table) {
            $table->dropColumn('charges_registration_fee');
        });
    }
};
