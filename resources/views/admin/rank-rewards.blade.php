@extends('common.layout')
@section('title', 'Rank Rewards')
@section('main')
<style>
.rrw .card {
    background: var(--card);
    border: 1px solid #1f2832;
    border-radius: var(--radius);
    padding: 20px;
    margin-bottom: 24px;
}
.rrw h2 { margin: 0 0 6px; font-size: 18px; }
.rrw .filter-form { display: flex; gap: 10px; flex-wrap: wrap; align-items: flex-end; margin-bottom: 10px; }
.rrw .filter-form label { display: block; font-size: 12px; margin-bottom: 5px; color: #a9b9c7; }
.rrw .filter-form input, .rrw .filter-form select {
    padding: 8px; border-radius: 6px; background: #141c22; border: 1px solid #1f2832; color: #fff;
}
.rrw .btn { font-size: 14px; padding: 9px 16px; border: none; border-radius: 8px; cursor: pointer; font-weight: 600; text-decoration: none; display: inline-block; }
.rrw .btn-primary { background: var(--accent); color: #fff; }
.rrw .btn-dark { background: #333; color: #fff; }
.rrw .stats { display: grid; grid-template-columns: repeat(auto-fit, minmax(160px, 1fr)); gap: 12px; margin-bottom: 16px; }
.rrw .stat { background: #141c22; border: 1px solid #1f2832; border-radius: 10px; padding: 14px; }
.rrw .stat p { margin: 0; font-size: 12px; color: #a9b9c7; }
.rrw .stat h3 { margin: 6px 0 0; font-size: 20px; }
.rrw .table-wrap { overflow-x: auto; }
.rrw table { width: 100%; border-collapse: collapse; margin-top: 10px; }
.rrw th, .rrw td { border: 1px solid #1e2b36; padding: 10px; text-align: center; font-size: 14px; white-space: nowrap; }
.rrw th { background: #161f29; color: #a9b9c7; }
.rrw td { color: #d4dee8; }
.rrw td small { display: block; color: #8899a8; }
.rrw .muted { color: #8899a8; font-size: 13px; margin: 0 0 12px; }
.rrw .link { color: var(--accent); text-decoration: none; }
.rrw .bar { height: 6px; border-radius: 3px; background: #1f2832; overflow: hidden; min-width: 90px; }
.rrw .bar > span { display: block; height: 100%; background: linear-gradient(90deg, #35e0c9, #f0bd5a); }
.rrw .grid2 { display: grid; grid-template-columns: minmax(0, 1fr) minmax(0, 1fr); gap: 24px; }
.rrw .grid2 > .card { min-width: 0; }
@media (max-width: 1500px) { .rrw .grid2 { grid-template-columns: minmax(0, 1fr); } }
</style>

<div class="rrw">
    <div class="header">
        <h1>Rank &amp; Rewards</h1>
        <div class="user-info">👤 {{ Auth::user()->username ?? Auth::user()->name }}</div>
    </div>

    <div class="card">
        <p class="muted">
            A pair is one active member (bought a package) in the left leg matched with one in the right leg.
            Only pairs made after launch count{{ $summary->launched_at ? ' (launch snapshot ' . \Carbon\Carbon::parse($summary->launched_at)->format('d M Y, h:i A') . ')' : '' }}.
            Each rank needs that many new pairs on top of the previous ranks and pays ₹{{ \App\Services\RankRewardService::REWARD_PER_PAIR }} per pair, once. The nightly job pays at 00:20 IST.
        </p>
        <div class="stats">
            <div class="stat"><p>Total rewards paid</p><h3>₹{{ number_format($summary->total ?? 0, 2) }}</h3></div>
            <div class="stat"><p>Rewards</p><h3>{{ number_format($summary->rewards ?? 0) }}</h3></div>
            <div class="stat"><p>Members rewarded</p><h3>{{ number_format($summary->members ?? 0) }}</h3></div>
            <div class="stat"><p>Members with new pairs</p><h3>{{ number_format($climberTotal) }}</h3></div>
            <div class="stat"><p>Last paid</p><h3 style="font-size:15px;">{{ $summary->last_paid ? \Carbon\Carbon::parse($summary->last_paid)->format('d M Y, h:i A') : '—' }}</h3></div>
        </div>
    </div>

    <div class="grid2">
        <div class="card">
            <h2>Rank ladder</h2>
            <p class="muted">How many members have been paid each rank.</p>
            <div class="table-wrap">
                <table>
                    <thead><tr><th>Level</th><th>Rank</th><th>New pairs</th><th>Total pairs</th><th>Reward</th><th>Members paid</th><th>Paid</th></tr></thead>
                    <tbody>
                        @foreach ($ladder as $row)
                            @php $pl = $perLevel[$row['level']] ?? null; @endphp
                            <tr>
                                <td>{{ $row['level'] }}</td>
                                <td style="text-align:left;">{{ $row['name'] }}</td>
                                <td>{{ number_format($row['step']) }}</td>
                                <td>{{ number_format($row['cumulative']) }}</td>
                                <td>₹{{ number_format($row['reward']) }}</td>
                                <td>
                                    @if ($pl)
                                        <a class="link" href="{{ route('admin.rank-rewards', ['level' => $row['level']]) }}">{{ number_format($pl->members) }}</a>
                                    @else
                                        0
                                    @endif
                                </td>
                                <td>₹{{ number_format($pl->total ?? 0) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="card">
            <h2>Top climbers</h2>
            <p class="muted">Members with the most pairs since launch and how far they are from their next rank.</p>
            <div class="table-wrap">
                <table>
                    <thead><tr><th>Member</th><th>Pairs since launch</th><th>Active L · R</th><th>Rank</th><th>Next rank</th><th>Progress</th></tr></thead>
                    <tbody>
                        @forelse ($climbers as $c)
                            @php
                                $prev = $c->level ? $ladder[$c->level]['cumulative'] : 0;
                                $pct = $c->next ? min(100, ($c->pairs - $prev) / $c->next['step'] * 100) : 100;
                            @endphp
                            <tr>
                                <td style="text-align:left;">{{ $c->member_id }}<small>{{ $c->name }}</small></td>
                                <td>{{ number_format($c->pairs) }}<small>{{ number_format($c->lifetime) }} lifetime</small></td>
                                <td>{{ number_format($c->left) }} · {{ number_format($c->right) }}</td>
                                <td>{{ $c->rank }}</td>
                                <td>@if ($c->next){{ $c->next['name'] }}<small>{{ number_format($c->to_next) }} more</small>@else 🎉 Top @endif</td>
                                <td><div class="bar"><span style="width:{{ round($pct, 1) }}%"></span></div></td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="muted">No member has made a pair since launch yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="card">
        <h2>Rewards paid</h2>
        <form method="GET" action="{{ route('admin.rank-rewards') }}" class="filter-form">
            <div>
                <label>Paid from</label>
                <input type="date" name="from" value="{{ $filters['from'] }}">
            </div>
            <div>
                <label>Paid to</label>
                <input type="date" name="to" value="{{ $filters['to'] }}">
            </div>
            <div>
                <label>Member</label>
                <input type="text" name="member" value="{{ $filters['member'] }}" placeholder="Member ID or name">
            </div>
            <div>
                <label>Rank</label>
                <select name="level">
                    <option value="">All ranks</option>
                    @foreach ($ladder as $row)
                        <option value="{{ $row['level'] }}" @selected($filters['level'] == $row['level'])>L{{ $row['level'] }} · {{ $row['name'] }}</option>
                    @endforeach
                </select>
            </div>
            <button class="btn btn-primary" type="submit">Filter</button>
            <a href="{{ route('admin.rank-rewards') }}" class="btn btn-dark">Reset</a>
            <button class="btn btn-primary" type="submit" formaction="{{ route('admin.rank-rewards.export') }}">⬇ Export CSV</button>
        </form>

        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Paid On</th>
                        <th>Member</th>
                        <th>Rank</th>
                        <th>New Pairs</th>
                        <th>Pairs Needed</th>
                        <th>Pairs When Paid</th>
                        <th>Active L · R</th>
                        <th>Reward</th>
                        <th>Txn ID</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($rows as $i => $r)
                        <tr>
                            <td>{{ $rows->firstItem() + $i }}</td>
                            <td>{{ \Carbon\Carbon::parse($r->created_at)->format('d M Y, h:i A') }}</td>
                            <td style="text-align:left;">{{ $r->member_id ?? '#' . $r->user_id }}<small>{{ $r->name }}</small></td>
                            <td>L{{ $r->level }} · {{ $r->rank_name }}</td>
                            <td>{{ number_format($r->pairs_step) }}</td>
                            <td>{{ number_format($r->pairs_required) }}</td>
                            <td>{{ number_format($r->pairs_at_payout) }}</td>
                            <td>{{ number_format($r->left_count) }} · {{ number_format($r->right_count) }}</td>
                            <td>₹{{ number_format($r->amount, 2) }}</td>
                            <td>
                                @if ($r->transaction_id)
                                    <a class="link" href="{{ route('admin.transactions.show', $r->transaction_id) }}">#{{ $r->transaction_id }}</a>
                                @else
                                    —
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="10" class="muted">No rank rewards paid{{ array_filter($filters) ? ' for these filters' : ' yet' }}.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{ $rows->links('partials.pagination') }}
    </div>
</div>
@endsection
