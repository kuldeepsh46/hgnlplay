<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\RankRewardService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

/*
|--------------------------------------------------------------------------
| Rank & Rewards ledger (admin, read-only)
|--------------------------------------------------------------------------
| The rank ladder with how many members reached each rank, who is closest
| to their next rank, and every reward paid (rank_reward_payouts).
|--------------------------------------------------------------------------
*/
class RankRewardController extends Controller
{
    private const PER_PAGE = 25;

    public function index(Request $request)
    {
        abort_unless(Auth::user()?->hasRole('admin'), 403);

        $filters = $this->filters($request);
        $ladder = RankRewardService::ladder();

        $perLevel = DB::table('rank_reward_payouts')
            ->selectRaw('level, COUNT(*) as members, SUM(amount) as total')
            ->groupBy('level')
            ->get()
            ->keyBy('level');

        $summary = DB::table('rank_reward_payouts')
            ->selectRaw('COUNT(*) as rewards, COUNT(DISTINCT user_id) as members, COALESCE(SUM(amount), 0) as total, MAX(created_at) as last_paid')
            ->first();

        // Members with pairs since launch, most pairs first
        $counts = RankRewardService::rankPairs();
        $summary->launched_at = DB::table('rank_reward_baselines')->min('created_at');
        $users = DB::table('users')->whereIn('id', array_keys(array_filter($counts, fn($c) => $c['pairs'] > 0)))
            ->get(['id', 'member_id', 'name'])->keyBy('id');
        $climberTotal = count(array_filter($counts, fn($c) => $c['pairs'] > 0));
        $climbers = collect($counts)
            ->filter(fn($c, $id) => $c['pairs'] > 0 && isset($users[$id]))
            ->map(function ($c, $id) use ($users, $ladder) {
                $level = RankRewardService::levelFor($c['pairs']);
                $next = $ladder[$level + 1] ?? null;
                return (object) ($c + [
                    'id' => $id,
                    'member_id' => $users[$id]->member_id,
                    'name' => $users[$id]->name,
                    'level' => $level,
                    'rank' => $level ? $ladder[$level]['name'] : '—',
                    'next' => $next,
                    'to_next' => $next ? $next['cumulative'] - $c['pairs'] : 0,
                ]);
            })
            ->sortByDesc('pairs')
            ->take(20)
            ->values();

        $rows = $this->query($filters)->paginate(self::PER_PAGE)->appends(array_filter($filters, fn($v) => $v !== null && $v !== ''));

        return view('admin.rank-rewards', compact('ladder', 'perLevel', 'summary', 'climbers', 'climberTotal', 'rows', 'filters'));
    }

    public function export(Request $request)
    {
        abort_unless(Auth::user()?->hasRole('admin'), 403);

        $filters = $this->filters($request);
        $query = $this->query($filters);
        $filename = 'rank_rewards_' . ($filters['from'] ?: 'start') . '_to_' . ($filters['to'] ?: 'today') . '.csv';

        return response()->stream(function () use ($query) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['Paid on', 'Member ID', 'Member', 'Level', 'Rank', 'New pairs', 'Pairs needed', 'Pairs since launch when paid', 'Left active', 'Right active', 'Reward', 'Transaction ID']);
            foreach ($query->cursor() as $r) {
                fputcsv($file, [$r->created_at, $r->member_id, $r->name, $r->level, $r->rank_name, $r->pairs_step, $r->pairs_required, $r->pairs_at_payout, $r->left_count, $r->right_count, $r->amount, $r->transaction_id]);
            }
            fclose($file);
        }, 200, [
            'Content-type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ]);
    }

    private function filters(Request $request): array
    {
        $v = $request->validate([
            'from' => ['nullable', 'date_format:Y-m-d'],
            'to' => ['nullable', 'date_format:Y-m-d', 'after_or_equal:from'],
            'member' => ['nullable', 'string', 'max:100'],
            'level' => ['nullable', 'integer', 'min:1', 'max:' . count(RankRewardService::LEVELS)],
        ]);

        return [
            'from' => $v['from'] ?? null,
            'to' => $v['to'] ?? null,
            'member' => isset($v['member']) ? trim($v['member']) : null,
            'level' => $v['level'] ?? null,
        ];
    }

    private function query(array $f)
    {
        $q = DB::table('rank_reward_payouts as p')
            ->leftJoin('users as u', 'u.id', '=', 'p.user_id')
            ->select('p.*', 'u.member_id', 'u.name')
            ->orderByDesc('p.id');

        if ($f['from']) {
            $q->where('p.created_at', '>=', $f['from'] . ' 00:00:00');
        }
        if ($f['to']) {
            $q->where('p.created_at', '<=', $f['to'] . ' 23:59:59');
        }
        if ($f['member']) {
            $like = '%' . $f['member'] . '%';
            $q->where(fn($w) => $w->where('u.member_id', 'like', $like)->orWhere('u.name', 'like', $like)->orWhere('u.username', 'like', $like));
        }
        if ($f['level']) {
            $q->where('p.level', $f['level']);
        }

        return $q;
    }
}
