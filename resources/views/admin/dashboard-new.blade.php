@extends('common.layout')

@section('title', 'Admin Dashboard')

@section('main')
@php
    // Change vs previous period: [text, css class]
    $delta = function ($key, $higherIsGood = true) use ($metrics, $previous) {
        if (!$previous) {
            return null;
        }
        $now = (float) $metrics[$key];
        $before = (float) $previous[$key];
        if ($before == 0.0) {
            return $now == 0.0 ? ['no change', 'flat'] : ['new', $higherIsGood ? 'up' : 'down'];
        }
        $pct = ($now - $before) / abs($before) * 100;
        if (abs($pct) < 0.5) {
            return ['no change', 'flat'];
        }
        $good = ($pct > 0) === $higherIsGood;
        return [($pct > 0 ? '▲ ' : '▼ ') . number_format(abs($pct), 0) . '%', $good ? 'up' : 'down'];
    };
    $age = fn($date) => \Carbon\Carbon::parse($date)->diffForHumans(null, true) . ' ago';
    $isStale = fn($date) => \Carbon\Carbon::parse($date)->lt(now()->subHours($attention['stale_hours']));
    $memberLink = fn($id) => route('admin.users.edit', $id);
    $treeLink = fn($id) => route('tree.view', $id);
    $actionCount = $attention['fund_requests']->count() + $attention['withdrawals']->count() + $attention['support']->count();
@endphp

<style>
    .adn { --ok: #a7ff1e; --ok-soft: rgba(167, 255, 30, .1); --warn: #ffb547; --warn-soft: rgba(255, 181, 71, .12); --bad: #ff5c7a; --bad-soft: rgba(255, 92, 122, .12); --info: #5cc8ff; --info-soft: rgba(92, 200, 255, .12); --line: #1b222b; --card: #10171f; --card2: #0b0e12; --muted: #8b98a5; --text: #e9eef3;
        color: var(--text); padding: 24px; max-width: 1600px; margin: 0 auto; }
    .adn * { box-sizing: border-box; }
    .adn a { color: inherit; }
    .adn h1 { font-size: 26px; margin: 0; letter-spacing: -.3px; }
    .adn h3 { font-size: 15px; margin: 0; font-weight: 700; letter-spacing: .2px; }
    .adn .sub { color: var(--muted); font-size: 13px; }
    .adn .row { display: grid; gap: 18px; margin-bottom: 18px; }
    .adn .card { background: var(--card); border: 1px solid var(--line); border-radius: 14px; padding: 18px; min-width: 0; }
    .adn .card-head { display: flex; justify-content: space-between; align-items: center; gap: 12px; margin-bottom: 14px; flex-wrap: wrap; }
    .adn .link { color: var(--muted); font-size: 12px; text-decoration: none; border-bottom: 1px dashed #3a4652; }
    .adn .link:hover { color: var(--text); }

    /* Header + filters */
    .adn-top { display: flex; justify-content: space-between; align-items: flex-end; gap: 16px; flex-wrap: wrap; margin-bottom: 20px; }
    .adn-filters { display: flex; gap: 6px; flex-wrap: wrap; align-items: center; }
    .adn-chip { padding: 8px 12px; border-radius: 999px; border: 1px solid var(--line); background: var(--card); color: var(--muted); font-size: 12px; font-weight: 600; text-decoration: none; white-space: nowrap; }
    .adn-chip:hover { color: var(--text); border-color: #3a4652; }
    .adn-chip.on { background: var(--ok); color: #000; border-color: var(--ok); }
    .adn-custom { display: flex; gap: 6px; align-items: center; }
    .adn-custom input { background: var(--card); border: 1px solid var(--line); color: var(--text); border-radius: 8px; padding: 7px 8px; font-size: 12px; color-scheme: dark; }
    .adn-custom button, .adn-btn { background: var(--ok); color: #000; border: 0; border-radius: 8px; padding: 8px 12px; font-weight: 700; font-size: 12px; cursor: pointer; text-decoration: none; display: inline-block; white-space: nowrap; }
    .adn-btn.ghost { background: transparent; color: var(--text); border: 1px solid #3a4652; }
    .adn-btn.danger { background: transparent; color: var(--bad); border: 1px solid rgba(255, 92, 122, .4); }
    .adn-btn.sm { padding: 5px 9px; font-size: 11px; }
    .adn-btn:disabled { opacity: .4; cursor: not-allowed; }

    /* Flash */
    .adn-flash { padding: 12px 16px; border-radius: 10px; margin-bottom: 16px; font-size: 14px; }
    .adn-flash.ok { background: var(--ok-soft); border: 1px solid var(--ok); color: var(--ok); }
    .adn-flash.bad { background: var(--bad-soft); border: 1px solid var(--bad); color: var(--bad); }

    /* Attention strip */
    .adn-alerts { grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); }
    .adn-alert { display: flex; gap: 12px; align-items: center; padding: 14px 16px; border-radius: 12px; border: 1px solid var(--line); background: var(--card); text-decoration: none; transition: .2s; }
    .adn-alert:hover { transform: translateY(-2px); border-color: #3a4652; }
    .adn-alert .ico { width: 38px; height: 38px; border-radius: 10px; display: grid; place-items: center; font-size: 18px; flex-shrink: 0; }
    .adn-alert .num { font-size: 20px; font-weight: 800; line-height: 1.1; }
    .adn-alert .lbl { font-size: 12px; color: var(--muted); }
    .adn-alert.warn { border-color: rgba(255, 181, 71, .35); } .adn-alert.warn .ico { background: var(--warn-soft); }
    .adn-alert.bad { border-color: rgba(255, 92, 122, .35); } .adn-alert.bad .ico { background: var(--bad-soft); }
    .adn-alert.good .ico { background: var(--ok-soft); } .adn-alert.info .ico { background: var(--info-soft); }

    /* KPI */
    .adn-kpis { grid-template-columns: repeat(auto-fit, minmax(190px, 1fr)); }
    .adn-kpi .k-lbl { font-size: 12px; color: var(--muted); font-weight: 600; text-transform: uppercase; letter-spacing: .6px; }
    .adn-kpi .k-val { font-size: 26px; font-weight: 800; margin: 8px 0 6px; letter-spacing: -.5px; white-space: nowrap; }
    .adn-kpi .k-foot { display: flex; justify-content: space-between; gap: 8px; font-size: 12px; color: var(--muted); }
    .adn-kpi.hero { background: linear-gradient(135deg, rgba(167, 255, 30, .12), var(--card) 60%); border-color: rgba(167, 255, 30, .3); }
    .adn-delta { font-weight: 700; white-space: nowrap; }
    .adn-delta.up { color: var(--ok); } .adn-delta.down { color: var(--bad); } .adn-delta.flat { color: var(--muted); }

    /* Layout grids */
    .adn-2-1 { grid-template-columns: 2fr 1fr; }
    .adn-1-1 { grid-template-columns: 1fr 1fr; }
    .adn-3 { grid-template-columns: repeat(3, 1fr); }
    @media (max-width: 1200px) { .adn-2-1, .adn-3 { grid-template-columns: 1fr 1fr; } .adn-2-1 > :first-child { grid-column: 1 / -1; } }
    @media (max-width: 800px) { .adn { padding: 14px; } .adn-2-1, .adn-1-1, .adn-3 { grid-template-columns: 1fr; } .adn-kpi .k-val { font-size: 22px; } }
    @media (max-width: 560px) {
        .adn-alerts, .adn-kpis { grid-template-columns: 1fr 1fr; gap: 10px; }
        .adn-alert { flex-direction: column; align-items: flex-start; gap: 8px; padding: 12px; }
        .adn-alert .ico { width: 32px; height: 32px; font-size: 16px; }
        .adn-kpi { padding: 14px; } .adn-kpi .k-val { font-size: 18px; } .adn-kpi .k-foot { flex-direction: column; gap: 4px; }
        .adn h1 { font-size: 22px; }
    }

    /* Tables */
    .adn-table-wrap { overflow-x: auto; margin: 0 -4px; }
    .adn table { width: 100%; border-collapse: collapse; font-size: 13px; }
    .adn th { text-align: left; color: var(--muted); font-size: 11px; text-transform: uppercase; letter-spacing: .6px; font-weight: 700; padding: 8px 6px; border-bottom: 1px solid var(--line); white-space: nowrap; }
    .adn td { padding: 10px 6px; border-bottom: 1px solid #151c24; vertical-align: middle; white-space: nowrap; }
    .adn tr:last-child td { border-bottom: 0; }
    .adn tbody tr:hover { background: #131b24; }
    .adn .num-cell { text-align: right; font-variant-numeric: tabular-nums; }
    .adn .mono { font-family: ui-monospace, SFMono-Regular, Menlo, monospace; font-size: 12px; color: var(--muted); }
    .adn .wrap { white-space: normal; min-width: 150px; max-width: 260px; color: var(--muted); font-size: 12px; line-height: 1.4; }
    .adn-quick { display: grid; grid-template-columns: repeat(auto-fill, minmax(150px, 1fr)); gap: 10px; margin-top: 16px; }
    .adn-quick a { display: flex; align-items: center; gap: 10px; padding: 12px; border-radius: 10px; border: 1px solid var(--line); background: var(--card2); text-decoration: none; font-size: 13px; font-weight: 600; transition: .2s; }
    .adn-quick a:hover { border-color: var(--ok); transform: translateY(-2px); }
    .adn-quick small { display: block; color: var(--muted); font-weight: 400; font-size: 11px; }
    .adn .who b { display: block; font-weight: 600; }
    .adn .who span { color: var(--muted); font-size: 12px; }
    .adn .pill { display: inline-block; padding: 3px 8px; border-radius: 999px; font-size: 11px; font-weight: 700; }
    .adn .pill.ok { background: var(--ok-soft); color: var(--ok); }
    .adn .pill.warn { background: var(--warn-soft); color: var(--warn); }
    .adn .pill.bad { background: var(--bad-soft); color: var(--bad); }
    .adn .pill.info { background: var(--info-soft); color: var(--info); }
    .adn .pill.mute { background: #1b222b; color: var(--muted); }
    .adn .actions { display: flex; gap: 6px; justify-content: flex-end; }
    .adn .actions form { margin: 0; }
    .adn .empty { padding: 26px 10px; text-align: center; color: var(--muted); font-size: 13px; }
    .adn .bar { height: 6px; border-radius: 3px; background: #1b222b; overflow: hidden; min-width: 60px; }
    .adn .bar i { display: block; height: 100%; background: var(--ok); border-radius: 3px; }

    /* Tabs */
    .adn-tabs { display: flex; gap: 4px; background: var(--card2); padding: 4px; border-radius: 10px; border: 1px solid var(--line); }
    .adn-tabs button { background: none; border: 0; color: var(--muted); padding: 6px 12px; font-size: 12px; font-weight: 700; border-radius: 7px; cursor: pointer; }
    .adn-tabs button.on { background: #1b222b; color: var(--text); }
    .adn [data-tab-panel] { display: none; } .adn [data-tab-panel].on { display: block; }

    /* Misc */
    .adn-chart { position: relative; height: 300px; }
    .adn-chart.sm { height: 240px; }
    .adn-search { display: flex; gap: 8px; }
    .adn-search input { flex: 1; min-width: 0; background: var(--card2); border: 1px solid var(--line); color: var(--text); border-radius: 10px; padding: 12px 14px; font-size: 14px; }
    .adn-search input:focus { outline: none; border-color: var(--ok); }
    .adn-stats { display: grid; grid-template-columns: repeat(2, 1fr); gap: 10px; }
    .adn-stat { background: var(--card2); border: 1px solid var(--line); border-radius: 10px; padding: 12px; }
    .adn-stat b { display: block; font-size: 20px; }
    .adn-stat span { font-size: 12px; color: var(--muted); }
    .adn-list-row { display: flex; justify-content: space-between; align-items: center; gap: 10px; padding: 8px 0; border-bottom: 1px solid #151c24; font-size: 13px; }
    .adn-list-row:last-child { border-bottom: 0; }
    .adn-legend { display: flex; flex-direction: column; gap: 8px; margin-top: 12px; }
    .adn-legend div { display: flex; align-items: center; gap: 8px; font-size: 12px; }
    .adn-legend i { width: 10px; height: 10px; border-radius: 3px; flex-shrink: 0; }
    .adn-legend span { flex: 1; color: var(--muted); }
</style>

<div class="adn">

    {{-- ============ HEADER + PERIOD ============ --}}
    <div class="adn-top">
        <div>
            <h1>Admin Dashboard</h1>
            <div class="sub" style="margin-top:6px;">
                Showing <b style="color:var(--text)">{{ $periodLabel }}</b>
                @if ($from) ({{ $from->format('d M Y') }} – {{ $to->format('d M Y') }}) · compared with the previous {{ $from->diffInDays($to) + 1 }} day(s) @endif
                · updated {{ now()->format('d M, h:i A') }}
            </div>
        </div>
        <div class="adn-filters">
            @foreach ($periods as $key => $label)
                <a class="adn-chip {{ $period === $key ? 'on' : '' }}" href="{{ route('admin.dashboard.new', ['period' => $key]) }}">{{ $label }}</a>
            @endforeach
            <form class="adn-custom" method="GET" action="{{ route('admin.dashboard.new') }}">
                <input type="hidden" name="period" value="custom">
                <input type="date" name="from" value="{{ $period === 'custom' ? $from->format('Y-m-d') : '' }}" required aria-label="From date">
                <input type="date" name="to" value="{{ $period === 'custom' ? $to->format('Y-m-d') : '' }}" required aria-label="To date">
                <button type="submit" class="{{ $period === 'custom' ? '' : 'ghost' }}">Apply</button>
            </form>
        </div>
    </div>

    @if (session('success'))
        <div class="adn-flash ok">✅ {{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div class="adn-flash bad">⚠️ {{ session('error') }}</div>
    @endif

    {{-- ============ NEEDS ATTENTION STRIP ============ --}}
    <div class="row adn-alerts">
        <a href="#actions" class="adn-alert {{ $attention['fund_requests']->count() ? 'warn' : 'good' }}" data-open-tab="funds">
            <div class="ico">💳</div>
            <div><div class="num">{{ $attention['fund_requests']->count() }}</div><div class="lbl">Fund requests pending · ₹{{ $inr($attention['fund_total']) }}</div></div>
        </a>
        <a href="#actions" class="adn-alert {{ $attention['withdraw_short'] ? 'bad' : ($attention['withdrawals']->count() ? 'warn' : 'good') }}" data-open-tab="payouts">
            <div class="ico">🏦</div>
            <div><div class="num">{{ $attention['withdrawals']->count() }}</div><div class="lbl">Withdrawals pending · ₹{{ $inr($attention['withdraw_total']) }}</div></div>
        </a>
        <a href="#actions" class="adn-alert {{ $attention['support']->count() ? 'warn' : 'good' }}" data-open-tab="support">
            <div class="ico">🆘</div>
            <div><div class="num">{{ $attention['support']->count() }}</div><div class="lbl">Support queries to answer</div></div>
        </a>
        <a href="#actions" class="adn-alert {{ ($attention['fund_stale'] + $attention['withdraw_stale']) ? 'bad' : 'good' }}" data-open-tab="funds">
            <div class="ico">⏰</div>
            <div><div class="num">{{ $attention['fund_stale'] + $attention['withdraw_stale'] }}</div><div class="lbl">Requests waiting &gt; {{ $attention['stale_hours'] }}h</div></div>
        </a>
        <a href="{{ route('admin.users') }}" class="adn-alert info">
            <div class="ico">💤</div>
            <div><div class="num">{{ $inr($attention['inactive_members']) }}</div><div class="lbl">Members with no package (7+ days)</div></div>
        </a>
        <a href="{{ route('admin.lucky.index') }}" class="adn-alert info">
            <div class="ico">🎁</div>
            <div><div class="num">{{ $attention['lucky_active'] }}</div><div class="lbl">Active lucky draw cycles</div></div>
        </a>
        @if ($attention['negative_wallets'])
            <a href="{{ route('admin.users') }}" class="adn-alert bad">
                <div class="ico">❗</div>
                <div><div class="num">{{ $attention['negative_wallets'] }}</div><div class="lbl">Wallets with negative balance</div></div>
            </a>
        @endif
    </div>

    {{-- ============ KPIs ============ --}}
    @php
        $kpis = [
            ['business', 'Business (top-ups)', '₹' . $inr($metrics['business']), $metrics['topups'] . ' top-ups · avg ₹' . $inr($metrics['avg_ticket']), true, true],
            ['new_members', 'New members', $inr($metrics['new_members']), $metrics['activations'] . ' activated (first package)', true, false],
            ['repurchases', 'Repurchases / EMIs', $inr($metrics['repurchases']), 'top-ups by existing buyers', true, false],
            ['funds_added', 'Funds added', '₹' . $inr($metrics['funds_added']), 'approved fund requests', true, false],
            ['income_paid', 'Income paid to members', '₹' . $inr($metrics['income_paid']), number_format($metrics['payout_ratio'], 1) . '% of business', false, false],
            ['payouts_net', 'Withdrawals paid', '₹' . $inr($metrics['payouts_net']), 'net, after tax · gross ₹' . $inr($metrics['payouts_paid']), false, false],
            ['net_cash', 'Net cash flow', '₹' . $inr($metrics['net_cash']), 'funds added − withdrawals paid', true, false],
        ];
    @endphp
    <div class="row adn-kpis">
        @foreach ($kpis as [$key, $label, $value, $foot, $higherIsGood, $hero])
            @php $d = $delta($key, $higherIsGood); @endphp
            <div class="card adn-kpi {{ $hero ? 'hero' : '' }}">
                <div class="k-lbl">{{ $label }}</div>
                <div class="k-val">{{ $value }}</div>
                <div class="k-foot">
                    <span>{{ $foot }}</span>
                    @if ($d) <span class="adn-delta {{ $d[1] }}" title="Previous period: {{ in_array($key, ['new_members', 'repurchases']) ? $inr($previous[$key]) : '₹' . $inr($previous[$key]) }}">{{ $d[0] }}</span> @endif
                </div>
            </div>
        @endforeach
        <div class="card adn-kpi">
            <div class="k-lbl">Wallet liability (now)</div>
            <div class="k-val">₹{{ $inr($network['wallet_total']) }}</div>
            <div class="k-foot"><span>held in {{ $inr($network['wallets_funded']) }} member wallets</span></div>
        </div>
    </div>

    {{-- ============ TREND + INCOME MIX ============ --}}
    <div class="row adn-2-1">
        <div class="card">
            <div class="card-head">
                <h3>📈 Business &amp; income trend <span class="sub">({{ $series['monthly'] ? 'monthly' : 'daily' }})</span></h3>
                <div class="adn-tabs" data-chart-switch="trend">
                    <button class="on" data-series="growth">Business vs income</button>
                    <button data-series="members">New members</button>
                    <button data-series="cash">Money in / out</button>
                </div>
            </div>
            <div class="adn-chart"><canvas id="adnTrend"></canvas></div>
        </div>
        <div class="card">
            <div class="card-head"><h3>💸 Income paid by type</h3><span class="sub">₹{{ $inr($metrics['income_paid']) }}</span></div>
            @if (count($incomeBreakdown))
                <div class="adn-chart sm"><canvas id="adnIncome"></canvas></div>
                <div class="adn-legend" id="adnIncomeLegend"></div>
            @else
                <div class="empty">No income paid in this period.</div>
            @endif
        </div>
    </div>

    {{-- ============ ACTION CENTER ============ --}}
    <div class="card" id="actions" style="margin-bottom:18px;">
        <div class="card-head">
            <h3>⚡ Action center <span class="sub">— {{ $actionCount }} item(s) waiting</span></h3>
            <div class="adn-tabs" data-tabs="actions">
                <button class="on" data-tab="funds">Fund requests ({{ $attention['fund_requests']->count() }})</button>
                <button data-tab="payouts">Withdrawals ({{ $attention['withdrawals']->count() }})</button>
                <button data-tab="support">Support ({{ $attention['support']->count() }})</button>
            </div>
        </div>

        <div data-tab-panel="funds" class="on">
            @if ($attention['fund_requests']->isEmpty())
                <div class="empty">🎉 No pending fund requests.</div>
            @else
                <div class="adn-table-wrap"><table>
                    <thead><tr><th>Member</th><th class="num-cell">Amount</th><th>Mode</th><th>Reference</th><th>Deposit date</th><th>Waiting</th><th>Proof</th><th style="text-align:right">Action</th></tr></thead>
                    <tbody>
                    @foreach ($attention['fund_requests'] as $f)
                        <tr>
                            <td class="who"><b><a href="{{ $memberLink($f->user_id) }}">{{ $f->member_id }}</a></b><span>{{ $f->name ?: $f->username }}</span></td>
                            <td class="num-cell"><b>₹{{ $inr($f->amount) }}</b></td>
                            <td>{{ $f->payment_mode ?: '—' }}</td>
                            <td class="mono">{{ \Illuminate\Support\Str::limit($f->transaction_remark ?: ($f->account_number ?: '—'), 22) }}</td>
                            <td>{{ $f->deposit_date ? \Carbon\Carbon::parse($f->deposit_date)->format('d M Y') : '—' }}</td>
                            <td><span class="pill {{ $isStale($f->created_at) ? 'bad' : 'mute' }}">{{ $age($f->created_at) }}</span></td>
                            <td>@if ($f->attachment)<a class="link" href="{{ asset($f->attachment) }}" target="_blank" rel="noopener">View</a>@else<span class="sub">—</span>@endif</td>
                            <td><div class="actions">
                                <form method="POST" action="{{ route('admin.payments.approve', $f->id) }}" onsubmit="return confirm('Approve ₹{{ $inr($f->amount) }} for {{ $f->member_id }}? It will be added to their wallet.')">
                                    @csrf <button class="adn-btn sm">Approve</button>
                                </form>
                                <form method="POST" action="{{ route('admin.payments.reject', $f->id) }}" data-reject>
                                    @csrf <input type="hidden" name="reject_reason"> <button class="adn-btn sm danger">Reject</button>
                                </form>
                            </div></td>
                        </tr>
                    @endforeach
                    </tbody>
                </table></div>
            @endif
            <div style="margin-top:12px;"><a class="link" href="{{ route('admin.payments') }}">Open all payments →</a></div>
        </div>

        <div data-tab-panel="payouts">
            @if ($attention['withdrawals']->isEmpty())
                <div class="empty">🎉 No pending withdrawals.</div>
            @else
                <div class="adn-table-wrap"><table>
                    <thead><tr><th>Member</th><th class="num-cell">Amount</th><th class="num-cell">Net payable</th><th class="num-cell">Wallet</th><th>Bank</th><th>Waiting</th><th style="text-align:right">Action</th></tr></thead>
                    <tbody>
                    @foreach ($attention['withdrawals'] as $w)
                        @php $short = (float) $w->wallet_balance < (float) $w->amount; @endphp
                        <tr>
                            <td class="who"><b><a href="{{ $memberLink($w->user_id) }}">{{ $w->member_id }}</a></b><span>{{ $w->name ?: $w->username }}</span></td>
                            <td class="num-cell"><b>₹{{ $inr($w->amount) }}</b></td>
                            <td class="num-cell">₹{{ $inr($w->net_amount) }}</td>
                            <td class="num-cell">₹{{ $inr($w->wallet_balance) }} @if ($short)<span class="pill bad">short</span>@endif</td>
                            <td class="mono">{{ $w->bank_name ?: '—' }} {{ $w->account_number ? '· ' . $w->account_number : '' }} {{ $w->ifsc_code ? '· ' . $w->ifsc_code : '' }}</td>
                            <td><span class="pill {{ $isStale($w->created_at) ? 'bad' : 'mute' }}">{{ $age($w->created_at) }}</span></td>
                            <td><div class="actions">
                                <form method="POST" action="{{ route('admin.payouts.approve', $w->id) }}" onsubmit="return confirm('Approve withdrawal of ₹{{ $inr($w->amount) }} for {{ $w->member_id }}? It will be deducted from their wallet.')">
                                    @csrf <button class="adn-btn sm" @if ($short) disabled title="Wallet balance is lower than the request" @endif>Approve</button>
                                </form>
                                <form method="POST" action="{{ route('admin.payouts.reject', $w->id) }}" data-reject>
                                    @csrf <input type="hidden" name="reject_reason"> <button class="adn-btn sm danger">Reject</button>
                                </form>
                            </div></td>
                        </tr>
                    @endforeach
                    </tbody>
                </table></div>
            @endif
            <div style="margin-top:12px;"><a class="link" href="{{ route('admin.payouts') }}">Open all payouts →</a></div>
        </div>

        <div data-tab-panel="support">
            @if ($attention['support']->isEmpty())
                <div class="empty">🎉 No unanswered support queries.</div>
            @else
                <div class="adn-table-wrap"><table>
                    <thead><tr><th>Member</th><th>Subject</th><th>Waiting</th><th></th></tr></thead>
                    <tbody>
                    @foreach ($attention['support'] as $q)
                        <tr>
                            <td class="who"><b>{{ $q->member_id }}</b><span>{{ $q->name }}</span></td>
                            <td class="wrap" style="color:var(--text)">{{ $q->subject }}</td>
                            <td><span class="pill {{ $isStale($q->created_at) ? 'bad' : 'mute' }}">{{ $age($q->created_at) }}</span></td>
                            <td style="text-align:right"><a class="adn-btn sm ghost" href="{{ route('admin.support') }}">Reply</a></td>
                        </tr>
                    @endforeach
                    </tbody>
                </table></div>
            @endif
        </div>
    </div>

    {{-- ============ PACKAGES ============ --}}
    <div class="card" style="margin-bottom:18px;">
        <div class="card-head">
            <h3>📦 Package performance <span class="sub">— {{ $from ? $periodLabel . ' vs all time' : 'all time' }}</span></h3>
            <a class="link" href="{{ route('packages.index') }}">Manage packages →</a>
        </div>
        <div class="adn-table-wrap"><table>
                <thead><tr><th>Package</th><th class="num-cell">Price</th><th class="num-cell">Sold</th><th class="num-cell">Revenue</th><th>Share</th><th class="num-cell">All-time</th><th>Direct / Pair</th><th>Reg. fee</th><th>Pairs with</th><th></th></tr></thead>
                <tbody>
                @foreach ($packages as $p)
                    <tr>
                        <td><b>{{ $p['name'] }}</b></td>
                        <td class="num-cell">₹{{ $inr($p['price']) }}</td>
                        <td class="num-cell who"><b>{{ $p['qty'] }}</b><span>{{ $p['buyers'] }} buyers</span></td>
                        <td class="num-cell"><b>₹{{ $inr($p['revenue']) }}</b></td>
                        <td><div class="bar" title="{{ number_format($p['share'], 1) }}%"><i style="width: {{ min(100, $p['share']) }}%"></i></div></td>
                        <td class="num-cell who"><b>₹{{ $inr($p['all_revenue']) }}</b><span>{{ $p['all_qty'] }} sold</span></td>
                        <td><span class="pill info">{{ $p['direct'] }}</span> <span class="pill ok">{{ $p['pair'] }}</span></td>
                        <td>{!! $p['reg_fee'] ? '<span class="pill warn">₹100</span>' : '<span class="pill mute">No</span>' !!}</td>
                        <td class="wrap">{{ $p['pairs'] }}</td>
                        <td><a class="adn-btn sm ghost" href="{{ route('packages.edit', $p['id']) }}">Edit</a></td>
                    </tr>
                @endforeach
                </tbody>
        </table></div>
    </div>

    {{-- ============ LEADERBOARDS ============ --}}
    <div class="row adn-3">
        <div class="card">
            <div class="card-head"><h3>🏆 Top earners</h3><span class="sub">{{ $periodLabel }}</span></div>
            @forelse ($topEarners as $i => $u)
                <div class="adn-list-row">
                    <div class="who"><b><a href="{{ $memberLink($u->id) }}">{{ $i + 1 }}. {{ $u->member_id }}</a></b><span>{{ $u->name }} · {{ $u->entries }} credits</span></div>
                    <b>₹{{ $inr($u->total) }}</b>
                </div>
            @empty
                <div class="empty">No income in this period.</div>
            @endforelse
        </div>
        <div class="card">
            <div class="card-head"><h3>🤝 Top sponsors</h3><span class="sub">new directs · {{ $periodLabel }}</span></div>
            @forelse ($topSponsors as $i => $u)
                <div class="adn-list-row">
                    <div class="who"><b><a href="{{ $treeLink($u->id) }}">{{ $i + 1 }}. {{ $u->member_id }}</a></b><span>{{ $u->name }} · directs' business ₹{{ $inr($u->business) }}</span></div>
                    <b>{{ $u->directs }}</b>
                </div>
            @empty
                <div class="empty">No new referrals in this period.</div>
            @endforelse
        </div>
        <div class="card">
            <div class="card-head"><h3>👛 Biggest wallets</h3><span class="sub">now</span></div>
            @forelse ($topWallets as $i => $u)
                <div class="adn-list-row">
                    <div class="who"><b><a href="{{ $memberLink($u->id) }}">{{ $i + 1 }}. {{ $u->member_id }}</a></b><span>{{ $u->name }}</span></div>
                    <b>₹{{ $inr($u->balance) }}</b>
                </div>
            @empty
                <div class="empty">No wallet balances.</div>
            @endforelse
        </div>
    </div>

    {{-- ============ MEMBER LOOKUP + NETWORK ============ --}}
    <div class="row adn-2-1">
        <div class="card" id="lookup">
            <div class="card-head"><h3>🔎 Member lookup</h3><span class="sub">member ID, name, mobile or email</span></div>
            <form class="adn-search" method="GET" action="{{ route('admin.dashboard.new') }}#lookup">
                @foreach (request()->only('period', 'from', 'to') as $k => $v)
                    <input type="hidden" name="{{ $k }}" value="{{ $v }}">
                @endforeach
                <input type="search" name="q" value="{{ $search }}" placeholder="e.g. HGNL1597 or Rahul or 98xxxxxx" autocomplete="off">
                <button class="adn-btn" type="submit">Search</button>
            </form>
            @if ($search === '')
                <div class="sub" style="margin-top:16px;">Quick links</div>
                <div class="adn-quick">
                    <a href="{{ route('admin.users') }}">👥 <span>Users<small>edit, tree, password</small></span></a>
                    <a href="{{ route('admin.payments') }}">💳 <span>Payments<small>{{ $attention['fund_requests']->count() }} pending</small></span></a>
                    <a href="{{ route('admin.payouts') }}">🏦 <span>Payouts<small>{{ $attention['withdrawals']->count() }} pending</small></span></a>
                    <a href="{{ route('packages.index') }}">📦 <span>Packages<small>bonuses &amp; pairing</small></span></a>
                    <a href="{{ route('admin.support') }}">🆘 <span>Support<small>{{ $attention['support']->count() }} to answer</small></span></a>
                    <a href="{{ route('admin.lucky.index') }}">🎁 <span>Lucky draw<small>{{ $attention['lucky_active'] }} active</small></span></a>
                </div>
            @else
                <div class="adn-table-wrap" style="margin-top:14px;">
                    @if ($searchResults->isEmpty())
                        <div class="empty">No member matches “{{ $search }}”.</div>
                    @else
                        <table>
                            <thead><tr><th>Member</th><th>Contact</th><th>Sponsor</th><th class="num-cell">Invested</th><th class="num-cell">Income</th><th class="num-cell">Wallet</th><th>Joined</th><th></th></tr></thead>
                            <tbody>
                            @foreach ($searchResults as $u)
                                <tr>
                                    <td class="who"><b>{{ $u->member_id }}</b><span>{{ $u->name }} @if ($u->position)· {{ ucfirst($u->position) }}@endif</span></td>
                                    <td class="who"><b style="font-weight:400">{{ $u->mobile ?: '—' }}</b><span>{{ $u->email }}</span></td>
                                    <td>{{ $u->sponsor ?: '—' }}</td>
                                    <td class="num-cell">₹{{ $inr($u->invested) }}</td>
                                    <td class="num-cell">₹{{ $inr($u->income) }}</td>
                                    <td class="num-cell">₹{{ $inr($u->balance) }}</td>
                                    <td>{{ \Carbon\Carbon::parse($u->created_at)->format('d M Y') }}</td>
                                    <td><div class="actions">
                                        <a class="adn-btn sm ghost" href="{{ $memberLink($u->id) }}">Edit</a>
                                        <a class="adn-btn sm ghost" href="{{ $treeLink($u->id) }}">Tree</a>
                                    </div></td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    @endif
                </div>
            @endif
        </div>
        <div class="card">
            <div class="card-head"><h3>🌐 Network snapshot</h3><span class="sub">now</span></div>
            <div class="adn-stats">
                <div class="adn-stat"><b>{{ $inr($network['total']) }}</b><span>Total members</span></div>
                <div class="adn-stat"><b style="color:var(--ok)">{{ $inr($network['paid']) }}</b><span>With a package ({{ $network['total'] ? number_format($network['paid'] / $network['total'] * 100, 0) : 0 }}%)</span></div>
                <div class="adn-stat"><b style="color:var(--warn)">{{ $inr($network['inactive']) }}</b><span>No package yet</span></div>
                <div class="adn-stat"><b>{{ $inr($network['emi_completed']) }}</b><span>EMIs completed · {{ $inr($network['emi_ongoing']) }} ongoing</span></div>
            </div>
            <div style="margin-top:16px;">
                <div class="sub" style="margin-bottom:6px;">Members by state</div>
                @php $maxState = max(1, $network['states']->max() ?? 1); @endphp
                @foreach ($network['states'] as $state => $count)
                    <div class="adn-list-row" style="padding:6px 0;">
                        <span style="min-width:110px">{{ $state }}</span>
                        <div class="bar" style="flex:1"><i style="width: {{ $count / $maxState * 100 }}%"></i></div>
                        <b style="min-width:40px;text-align:right">{{ $count }}</b>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- ============ RECENT ACTIVITY ============ --}}
    <div class="card" style="margin-bottom:18px;">
        <div class="card-head">
            <h3>🕒 Recent activity</h3>
            <div class="adn-tabs" data-tabs="recent">
                <button class="on" data-tab="orders">Top-ups</button>
                <button data-tab="members">New members</button>
                <button data-tab="income">Income credits</button>
            </div>
        </div>
        <div data-tab-panel="orders" class="on"><div class="adn-table-wrap"><table>
            <thead><tr><th>#</th><th>Member</th><th>Package</th><th class="num-cell">Amount</th><th>Paid by</th><th>Via</th><th>When</th></tr></thead>
            <tbody>
            @forelse ($recentOrders as $o)
                <tr>
                    <td class="mono">{{ $o->id }}</td>
                    <td class="who"><b><a href="{{ $memberLink($o->user_id) }}">{{ $o->member_id }}</a></b><span>{{ $o->name }}</span></td>
                    <td>{{ $o->package }}</td>
                    <td class="num-cell"><b>₹{{ $inr($o->amount) }}</b></td>
                    <td>{{ $o->paid_by ?: '—' }}</td>
                    <td><span class="pill mute">{{ $o->payment_by }}</span></td>
                    <td class="sub">{{ \Carbon\Carbon::parse($o->created_at)->format('d M, h:i A') }}</td>
                </tr>
            @empty
                <tr><td colspan="7" class="empty">No top-ups yet.</td></tr>
            @endforelse
            </tbody>
        </table></div></div>
        <div data-tab-panel="members"><div class="adn-table-wrap"><table>
            <thead><tr><th>Member</th><th>Sponsor</th><th>Leg</th><th>Status</th><th>Joined</th><th></th></tr></thead>
            <tbody>
            @foreach ($recentMembers as $m)
                <tr>
                    <td class="who"><b>{{ $m->member_id }}</b><span>{{ $m->name }}</span></td>
                    <td>{{ $m->sponsor ?: '—' }}</td>
                    <td>{{ $m->position ? ucfirst($m->position) : '—' }}</td>
                    <td>{!! $m->investment_count > 0 ? '<span class="pill ok">Active</span>' : '<span class="pill warn">No package</span>' !!}</td>
                    <td class="sub">{{ \Carbon\Carbon::parse($m->created_at)->format('d M Y, h:i A') }}</td>
                    <td><div class="actions"><a class="adn-btn sm ghost" href="{{ $memberLink($m->id) }}">Edit</a><a class="adn-btn sm ghost" href="{{ $treeLink($m->id) }}">Tree</a></div></td>
                </tr>
            @endforeach
            </tbody>
        </table></div></div>
        <div data-tab-panel="income"><div class="adn-table-wrap"><table>
            <thead><tr><th>Member</th><th>Type</th><th class="num-cell">Amount</th><th>When</th></tr></thead>
            <tbody>
            @forelse ($recentIncome as $t)
                <tr>
                    <td class="who"><b><a href="{{ $memberLink($t->user_id) }}">{{ $t->member_id }}</a></b><span>{{ $t->name }}</span></td>
                    <td><span class="pill info">{{ $t->label }}</span></td>
                    <td class="num-cell"><b>₹{{ $inr($t->amount) }}</b></td>
                    <td class="sub">{{ \Carbon\Carbon::parse($t->created_at)->format('d M, h:i A') }}</td>
                </tr>
            @empty
                <tr><td colspan="4" class="empty">No income credits yet.</td></tr>
            @endforelse
            </tbody>
        </table></div></div>
    </div>
</div>

<script>
(function () {
    // ---------- tabs ----------
    function openTab(group, name) {
        group.querySelectorAll('[data-tab]').forEach(b => b.classList.toggle('on', b.dataset.tab === name));
        const card = group.closest('.card');
        card.querySelectorAll('[data-tab-panel]').forEach(p => p.classList.toggle('on', p.dataset.tabPanel === name));
    }
    document.querySelectorAll('[data-tabs]').forEach(group => {
        group.querySelectorAll('[data-tab]').forEach(btn => btn.addEventListener('click', () => openTab(group, btn.dataset.tab)));
    });
    document.querySelectorAll('[data-open-tab]').forEach(a => a.addEventListener('click', () => {
        openTab(document.querySelector('[data-tabs="actions"]'), a.dataset.openTab);
    }));

    // ---------- reject with reason ----------
    document.querySelectorAll('form[data-reject]').forEach(form => form.addEventListener('submit', e => {
        const reason = prompt('Reason for rejecting (sent to the member):', '');
        if (reason === null) { e.preventDefault(); return; }
        form.querySelector('[name="reject_reason"]').value = reason;
    }));

    if (typeof Chart === 'undefined') return;

    // ---------- charts ----------
    const C = { ok: '#a7ff1e', info: '#5cc8ff', warn: '#ffb547', bad: '#ff5c7a', violet: '#b38cff', teal: '#3fd0b0', grid: '#1b222b', text: '#8b98a5' };
    Chart.defaults.color = C.text;
    Chart.defaults.font.family = 'Inter, system-ui, sans-serif';
    Chart.defaults.font.size = 11;
    const inr = v => '₹' + Number(v).toLocaleString('en-IN', { maximumFractionDigits: 0 });
    const short = v => v >= 1e7 ? '₹' + (v / 1e7).toFixed(1) + 'Cr' : v >= 1e5 ? '₹' + (v / 1e5).toFixed(1) + 'L' : v >= 1e3 ? '₹' + (v / 1e3).toFixed(0) + 'k' : '₹' + v;
    const grid = { color: C.grid, drawBorder: false };

    const S = @json($series);
    const trendSets = {
        growth: {
            datasets: [
                { type: 'bar', label: 'Business', data: S.business, backgroundColor: 'rgba(167,255,30,.55)', borderRadius: 4, yAxisID: 'y' },
                { type: 'line', label: 'Income paid', data: S.income, borderColor: C.warn, backgroundColor: C.warn, tension: .35, pointRadius: 2, yAxisID: 'y' },
            ],
            money: true,
        },
        members: {
            datasets: [{ type: 'bar', label: 'New members', data: S.members, backgroundColor: 'rgba(92,200,255,.6)', borderRadius: 4, yAxisID: 'y' }],
            money: false,
        },
        cash: {
            datasets: [
                { type: 'bar', label: 'Funds added', data: S.funds, backgroundColor: 'rgba(167,255,30,.55)', borderRadius: 4, yAxisID: 'y' },
                { type: 'bar', label: 'Withdrawals paid', data: S.payouts.map(v => -v), backgroundColor: 'rgba(255,92,122,.6)', borderRadius: 4, yAxisID: 'y' },
            ],
            money: true,
        },
    };
    const trend = new Chart(document.getElementById('adnTrend'), {
        data: { labels: S.labels, datasets: trendSets.growth.datasets },
        options: {
            maintainAspectRatio: false, interaction: { mode: 'index', intersect: false },
            plugins: { legend: { labels: { boxWidth: 10, boxHeight: 10 } }, tooltip: { callbacks: { label: c => c.dataset.label + ': ' + (trendSets.current?.money === false ? c.parsed.y : inr(Math.abs(c.parsed.y))) } } },
            scales: { x: { grid: { display: false }, ticks: { maxRotation: 0, autoSkip: true, maxTicksLimit: 12 } }, y: { grid, ticks: { callback: v => (trendSets.current?.money === false ? v : short(Math.abs(v))) } } },
        },
    });
    trendSets.current = trendSets.growth;
    document.querySelectorAll('[data-chart-switch="trend"] button').forEach(btn => btn.addEventListener('click', () => {
        btn.parentElement.querySelectorAll('button').forEach(b => b.classList.toggle('on', b === btn));
        trendSets.current = trendSets[btn.dataset.series];
        trend.data.datasets = trendSets.current.datasets;
        trend.update();
    }));

    const incomeRows = @json($incomeBreakdown);
    const incomeEl = document.getElementById('adnIncome');
    if (incomeEl && incomeRows.length) {
        const colors = [C.ok, C.info, C.warn, C.violet, C.teal, C.bad, '#f5e663', '#9aa5b1', '#ff9ad5'];
        new Chart(incomeEl, {
            type: 'doughnut',
            data: { labels: incomeRows.map(r => r.label), datasets: [{ data: incomeRows.map(r => r.total), backgroundColor: colors, borderColor: '#10171f', borderWidth: 3 }] },
            options: { maintainAspectRatio: false, cutout: '68%', plugins: { legend: { display: false }, tooltip: { callbacks: { label: c => c.label + ': ' + inr(c.parsed) } } } },
        });
        const total = incomeRows.reduce((s, r) => s + r.total, 0);
        document.getElementById('adnIncomeLegend').innerHTML = incomeRows.map((r, i) =>
            `<div><i style="background:${colors[i % colors.length]}"></i><span>${r.label} <small>(${r.earners} members)</small></span><b>${inr(r.total)}</b><small style="color:${C.text};width:40px;text-align:right">${(r.total / total * 100).toFixed(0)}%</small></div>`
        ).join('');
    }

})();
</script>
@endsection
