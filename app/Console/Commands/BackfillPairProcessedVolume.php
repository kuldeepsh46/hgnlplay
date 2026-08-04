<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

/**
 * One-time backfill for TopupController's binary pair-income logic.
 *
 * Run this ONCE, right after the `normal_pair_processed_volume` and
 * `starter_pair_processed_volume` migration, and BEFORE any further
 * top-ups happen.
 *
 * Why it's needed: those two columns default to 0 for every user,
 * including users with years of existing order history. Without this
 * backfill, the very first time pair income is evaluated for a sponsor,
 * ALL of their pre-existing matched volume (from before this feature
 * even existed) looks "new" and gets paid out immediately — which is
 * exactly the false ₹300 starter payout you saw. This command sets each
 * user's processed-volume counters to their CURRENT matched volume so
 * only volume generated from this point forward can trigger a bonus.
 *
 * Usage: php artisan pair-income:backfill-processed-volume
 */
class BackfillPairProcessedVolume extends Command
{
    protected $signature = 'pair-income:backfill-processed-volume {--dry-run : Show what would be updated without writing anything}';

    protected $description = 'Seed normal_pair_processed_volume and starter_pair_processed_volume to each user\'s current matched volume so historical business is not re-paid under the new pair-income rules.';

    public function handle(): int
    {
        $dryRun = (bool) $this->option('dry-run');

        $userIds = DB::table('users')->pluck('id');
        $total = $userIds->count();

        $this->info("Scanning {$total} users...");

        $bar = $this->output->createProgressBar($total);
        $bar->start();

        $updated = 0;

        foreach ($userIds as $userId) {
            $leftUserIds = collect($this->getFullSubtreeUsers($userId, 'left'))->pluck('id')->filter()->unique()->values()->toArray();
            $rightUserIds = collect($this->getFullSubtreeUsers($userId, 'right'))->pluck('id')->filter()->unique()->values()->toArray();

            $normalMatched = 0;
            $starterMatched = 0;

            if (!empty($leftUserIds) && !empty($rightUserIds)) {
                $normalMatched = min(
                    $this->getNormalPackageBusinessVolume($leftUserIds),
                    $this->getNormalPackageBusinessVolume($rightUserIds),
                );

                $starterMatched = min(
                    $this->getStarterPackageBusinessVolume($leftUserIds),
                    $this->getStarterPackageBusinessVolume($rightUserIds),
                );
            }

            if ($normalMatched > 0 || $starterMatched > 0) {
                if (!$dryRun) {
                    DB::table('users')->where('id', $userId)->update([
                        'normal_pair_processed_volume' => $normalMatched,
                        'starter_pair_processed_volume' => $starterMatched,
                    ]);
                }
                $updated++;
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);

        $this->info(($dryRun ? '[DRY RUN] Would have updated ' : 'Updated ') . "{$updated} user(s) with a non-zero baseline.");

        return self::SUCCESS;
    }

    // --------------------------------------------------------------------
    // Copied verbatim from TopupController so the baseline calculation
    // matches exactly what the controller will compute going forward.
    // --------------------------------------------------------------------

    private function getFullSubtreeUsers($rootId, $side)
    {
        $result = [];

        if (!$rootId || !in_array($side, ['left', 'right'])) {
            return [];
        }

        $start = DB::table('users')->where('placement_id', $rootId)->where('position', $side)->first();

        if (!$start) {
            return [];
        }

        $queue = [$start];
        $visited = [];

        while (!empty($queue)) {
            $node = array_shift($queue);

            if (!$node || in_array($node->id, $visited)) {
                continue;
            }

            $visited[] = $node->id;
            $result[] = $node;

            $children = DB::table('users')->where('placement_id', $node->id)->get();

            foreach ($children as $child) {
                if (!in_array($child->id, $visited)) {
                    $queue[] = $child;
                }
            }
        }

        return $result;
    }

    private function getNormalPackageBusinessVolume(array $userIds): float
    {
        if (empty($userIds)) {
            return 0;
        }

        return (float) DB::table('orders as o')
            ->join('packages as p', 'p.id', '=', 'o.package_id')
            ->whereIn('o.user_id', $userIds)
            ->where('o.status', 'completed')
            ->whereRaw('UPPER(TRIM(p.name)) != ?', ['REPURCHASE BOOSTER PACKAGE'])
            ->whereRaw('COALESCE(p.amount, p.actual_amount, o.amount, 0) < ?', [50000])
            ->where(function ($q) {
                $q->whereRaw('UPPER(TRIM(p.name)) != ?', ['STARTER PACKAGE'])
                    ->orWhere('o.amount', '!=', 1600);
            })
            ->selectRaw("
                COALESCE(SUM(
                    CASE
                        WHEN UPPER(TRIM(p.name)) = 'STARTER PACKAGE' THEN o.amount
                        ELSE COALESCE(p.amount, p.actual_amount, o.amount, 0)
                    END
                ), 0) as total
            ")
            ->value('total');
    }

    private function getStarterPackageBusinessVolume(array $userIds): float
    {
        if (empty($userIds)) {
            return 0;
        }

        return (float) DB::table('orders as o')
            ->join('packages as p', 'p.id', '=', 'o.package_id')
            ->whereIn('o.user_id', $userIds)
            ->where('o.status', 'completed')
            ->whereRaw('UPPER(TRIM(p.name)) = ?', ['STARTER PACKAGE'])
            ->where('o.amount', 1600)
            ->selectRaw('COALESCE(SUM(o.amount), 0) as total')
            ->value('total');
    }
}