<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

/*
|--------------------------------------------------------------------------
| Lifetime Rank & Rewards (Reward Bonus only)
|--------------------------------------------------------------------------
| A pair = one active member (has a completed package order) in the left
| leg matched with one in the right leg. Lifetime pairs = the smaller of
| the two legs' active counts, across the whole binary subtree.
|
| Only pairs made after launch count: the first time this code runs it
| snapshots every member's lifetime pairs into rank_reward_baselines, and
| rank pairs = lifetime pairs now - pairs at launch.
|
| Every level needs that many NEW pairs on top of the previous levels
| (10, then 20 more, then 40 more ...) and pays ₹80 per pair of the step,
| once per member per level.
|--------------------------------------------------------------------------
*/
class RankRewardService
{
    // [level => [rank name, new pairs needed for this level]]
    public const LEVELS = [
        1 => ['Manager', 10],
        2 => ['Rising Star Club', 20],
        3 => ['Star Club', 40],
        4 => ['Silver Star Club', 80],
        5 => ['Supremo Club', 160],
        6 => ['Royal Club', 320],
        7 => ['Royal Crown Club', 640],
        8 => ['Gold Star Club', 1280],
        9 => ['Platinum Star Club', 2560],
        10 => ['Ruby Star Club', 5120],
        11 => ['Blue Diamond Club', 10240],
        12 => ['Diamond Club', 20480],
        13 => ['Black Diamond Club', 40960],
        14 => ['Crown Diamond Club', 81920],
        15 => ['Ambassador Club', 163840],
        16 => ['Royal Ambassador Club', 327680],
        17 => ['Crown Ambassador Club', 655360],
        18 => ['Imperial Ambassador Club', 1310720],
        19 => ['Chief Brand Club', 2621440],
        20 => ['King Club', 5242880],
    ];

    public const REWARD_PER_PAIR = 80;

    /**
     * The full ladder with cumulative pair targets and reward amounts.
     */
    public static function ladder(): array
    {
        $out = [];
        $total = 0;

        foreach (self::LEVELS as $level => [$name, $step]) {
            $total += $step;
            $out[$level] = [
                'level' => $level,
                'name' => $name,
                'step' => $step,
                'cumulative' => $total,
                'reward' => $step * self::REWARD_PER_PAIR,
            ];
        }

        return $out;
    }

    /**
     * Highest level reached for a lifetime pair count (0 = none yet).
     */
    public static function levelFor(int $pairs): int
    {
        $reached = 0;

        foreach (self::ladder() as $level => $row) {
            if ($pairs < $row['cumulative']) {
                break;
            }
            $reached = $level;
        }

        return $reached;
    }

    /**
     * Left / right active counts and lifetime pairs for every member,
     * computed from the whole placement tree in one pass.
     *
     * @return array<int, array{left:int, right:int, pairs:int}>
     */
    public static function lifetimePairs(): array
    {
        $users = DB::table('users')->get(['id', 'placement_id', 'position']);
        $active = DB::table('orders')->where('status', 'completed')->distinct()->pluck('user_id')->flip();

        $children = [];
        foreach ($users as $u) {
            if ($u->placement_id && $u->placement_id != $u->id) {
                $children[$u->placement_id][] = $u;
            }
        }

        // Active members in each subtree (node included), iterative post-order
        $subtree = [];
        foreach ($users as $root) {
            if (isset($subtree[$root->id])) {
                continue;
            }
            $stack = [[$root->id, false]];
            $seen = [];
            while ($stack) {
                [$id, $expanded] = array_pop($stack);
                if (isset($subtree[$id])) {
                    continue;
                }
                if ($expanded) {
                    $n = isset($active[$id]) ? 1 : 0;
                    foreach ($children[$id] ?? [] as $c) {
                        $n += $subtree[$c->id] ?? 0;
                    }
                    $subtree[$id] = $n;
                    continue;
                }
                if (isset($seen[$id])) {
                    // Placement loop in bad data: count the node alone
                    $subtree[$id] = isset($active[$id]) ? 1 : 0;
                    continue;
                }
                $seen[$id] = true;
                $stack[] = [$id, true];
                foreach ($children[$id] ?? [] as $c) {
                    if (!isset($subtree[$c->id])) {
                        $stack[] = [$c->id, false];
                    }
                }
            }
        }

        $out = [];
        foreach ($users as $u) {
            $left = 0;
            $right = 0;
            foreach ($children[$u->id] ?? [] as $c) {
                $side = strtolower((string) $c->position);
                if ($side === 'left') {
                    $left += $subtree[$c->id] ?? 0;
                } elseif ($side === 'right') {
                    $right += $subtree[$c->id] ?? 0;
                }
            }
            $out[$u->id] = ['left' => $left, 'right' => $right, 'pairs' => min($left, $right)];
        }

        return $out;
    }

    /**
     * Snapshot every member's lifetime pairs as the launch baseline, once.
     * Members who join later have no row, i.e. a baseline of 0.
     */
    public static function ensureBaseline(?array $lifetime = null): void
    {
        if (DB::table('rank_reward_baselines')->exists()) {
            return;
        }

        $lifetime ??= self::lifetimePairs();
        $now = now();
        $rows = [];
        foreach ($lifetime as $userId => $c) {
            $rows[] = ['user_id' => $userId, 'left_count' => $c['left'], 'right_count' => $c['right'], 'pairs' => $c['pairs'], 'created_at' => $now];
        }
        foreach (array_chunk($rows, 1000) as $chunk) {
            DB::table('rank_reward_baselines')->insertOrIgnore($chunk);
        }
    }

    /**
     * Pairs that count for ranks (made after launch) for every member, plus
     * the lifetime left / right active counts and pairs at launch.
     *
     * @return array<int, array{left:int, right:int, lifetime:int, at_launch:int, pairs:int}>
     */
    public static function rankPairs(): array
    {
        $lifetime = self::lifetimePairs();
        self::ensureBaseline($lifetime);
        $baseline = DB::table('rank_reward_baselines')->pluck('pairs', 'user_id');

        $out = [];
        foreach ($lifetime as $userId => $c) {
            $atLaunch = (int) ($baseline[$userId] ?? 0);
            $out[$userId] = [
                'left' => $c['left'],
                'right' => $c['right'],
                'lifetime' => $c['pairs'],
                'at_launch' => $atLaunch,
                'pairs' => max(0, $c['pairs'] - $atLaunch),
            ];
        }

        return $out;
    }

    /**
     * One member's rank progress, for dashboards.
     */
    public static function progressFor(int $userId): array
    {
        $counts = self::rankPairs()[$userId] ?? ['left' => 0, 'right' => 0, 'lifetime' => 0, 'at_launch' => 0, 'pairs' => 0];
        $ladder = self::ladder();
        $level = self::levelFor($counts['pairs']);
        $next = $ladder[$level + 1] ?? null;
        $prevTarget = $level ? $ladder[$level]['cumulative'] : 0;

        return $counts + [
            'level' => $level,
            'rank' => $level ? $ladder[$level]['name'] : null,
            'next' => $next,
            'to_next' => $next ? $next['cumulative'] - $counts['pairs'] : 0,
            'next_pct' => $next ? min(100, ($counts['pairs'] - $prevTarget) / $next['step'] * 100) : 100,
        ];
    }
}
