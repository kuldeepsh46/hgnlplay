<?php

namespace App\Console\Commands;

use App\Enums\BonusType;
use App\Services\WalletService;
use App\Services\RankRewardService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class DistributeRankRewards extends Command
{
    protected $signature = 'income:rank-rewards
        {--dry-run : Show who would be paid without crediting anything}
        {--member= : Only check this member ID (e.g. HGNL1010)}';

    protected $description = 'Credit lifetime Rank & Rewards (Reward Bonus) for every rank level members have newly reached';

    public function handle(): int
    {
        $dryRun = (bool) $this->option('dry-run');

        if (!$dryRun && ($pausedBy = \App\Services\SystemSwitch::payoutsPausedBy())) {
            $this->warn("Skipped: payouts are paused by the \"{$pausedBy}\" system switch. Ranks reached meanwhile are paid on the first run after it is turned off.");
            return self::SUCCESS;
        }
        $ladder = RankRewardService::ladder();
        $counts = RankRewardService::rankPairs();

        $onlyUserId = null;
        if ($this->option('member')) {
            $onlyUserId = DB::table('users')->where('member_id', $this->option('member'))->value('id');
            if (!$onlyUserId) {
                $this->error('No member with ID ' . $this->option('member') . '.');
                return self::FAILURE;
            }
        }

        $paidLevels = DB::table('rank_reward_payouts')->get(['user_id', 'level'])
            ->groupBy('user_id')
            ->map(fn($rows) => $rows->pluck('level')->flip());

        $credited = 0;
        $total = 0;
        $rows = [];

        foreach ($counts as $userId => $c) {
            if ($onlyUserId && $userId != $onlyUserId) {
                continue;
            }

            $reached = RankRewardService::levelFor($c['pairs']);

            for ($level = 1; $level <= $reached; $level++) {
                if (isset($paidLevels[$userId][$level])) {
                    continue;
                }

                $rank = $ladder[$level];
                $memberId = DB::table('users')->where('id', $userId)->value('member_id') ?: ('#' . $userId);
                $rows[] = [$memberId, $level, $rank['name'], $c['pairs'] . " (lifetime {$c['lifetime']} - {$c['at_launch']} at launch)", '₹' . number_format($rank['reward'])];

                if ($dryRun) {
                    $total += $rank['reward'];
                    continue;
                }

                if ($this->pay($userId, $memberId, $rank, $c)) {
                    $credited++;
                    $total += $rank['reward'];
                }
            }
        }

        if ($rows) {
            $this->table(['Member', 'Level', 'Rank', 'Pairs since launch', 'Reward'], $rows);
        }

        $this->info(($dryRun ? 'Dry run: ' . count($rows) . ' reward(s) would be paid' : "Rank rewards: {$credited} credited")
            . ' (₹' . number_format($total, 2) . ').');

        return self::SUCCESS;
    }

    private function pay(int $userId, string $memberId, array $rank, array $c): bool
    {
        return DB::transaction(function () use ($userId, $memberId, $rank, $c) {
            DB::table('users')->where('id', $userId)->lockForUpdate()->first();

            // The unique (user_id, level) key makes this a no-op when the
            // level was already paid, even if two runs overlap.
            $inserted = DB::table('rank_reward_payouts')->insertOrIgnore([
                'user_id' => $userId,
                'level' => $rank['level'],
                'rank_name' => $rank['name'],
                'pairs_step' => $rank['step'],
                'pairs_required' => $rank['cumulative'],
                'left_count' => $c['left'],
                'right_count' => $c['right'],
                'pairs_at_payout' => $c['pairs'],
                'amount' => $rank['reward'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            if (!$inserted) {
                return false;
            }


            $remarks = 'Rank Reward - Level ' . $rank['level'] . ' ' . $rank['name'] . ' achieved by ' . $memberId
                . ': ' . number_format($rank['step']) . ' new pairs × ₹' . RankRewardService::REWARD_PER_PAIR
                . ' | Needs ' . number_format($rank['cumulative']) . ' pairs since launch'
                . ' | Has ' . number_format($c['pairs']) . ' (' . number_format($c['lifetime']) . ' lifetime - ' . number_format($c['at_launch']) . ' at launch; Left ' . number_format($c['left']) . ' · Right ' . number_format($c['right']) . ' active)';

            // 90% main wallet / 10% Repurchase Wallet
            $transactionId = WalletService::creditEarning($userId, (float) $rank['reward'], [
                'type' => 'credit',
                'bonus_type' => BonusType::RankReward->value,
                'remarks' => $remarks,
            ]);

            DB::table('rank_reward_payouts')
                ->where('user_id', $userId)
                ->where('level', $rank['level'])
                ->update(['transaction_id' => $transactionId]);

            return true;
        });
    }
}
