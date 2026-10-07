@extends('common.layout')
@section('title', 'Dashboard')
@section('main')
@php
    $name = $user->name ?? $user->username;
    $hour = now()->hour;
    $greeting = $hour < 12 ? 'Good morning' : ($hour < 17 ? 'Good afternoon' : 'Good evening');

    $monthDelta = $lastMonthEarning > 0 ? (($monthEarning - $lastMonthEarning) / $lastMonthEarning) * 100 : null;
    $maxIncome = max($incomeBreakdown->max(), 1);
    $legTotal = max($leftDownline + $rightDownline, 1);
    $kycDone = collect($kycChecklist)->filter()->count();
    $kycTotal = count($kycChecklist);
    $trendTotal = $trend->sum('amount');

    $tiers = [
        ['label' => 'Tier 1', 'count' => $progress->tier_1_count ?? 0, 'target' => 3],
        ['label' => 'Tier 2', 'count' => $progress->tier_2_count ?? 0, 'target' => 9],
        ['label' => 'Tier 3', 'count' => $progress->tier_3_count ?? 0, 'target' => 27],
    ];

    $refBase = url('/register') . '?refid=' . $user->id . '&name=' . urlencode($user->username ?? $user->name);

    // Small inline icon set (stroke icons, 24px grid)
    $icons = [
        'wallet' => '<path d="M19 7V5a2 2 0 0 0-2-2H5a2 2 0 0 0 0 4h14a2 2 0 0 1 2 2v3"/><path d="M3 5v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-3"/><path d="M21 12h-4a2 2 0 0 0 0 4h4z"/>',
        'bag' => '<path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"/><path d="M3 6h18"/><path d="M16 10a4 4 0 0 1-8 0"/>',
        'trend' => '<path d="m22 7-8.5 8.5-5-5L2 17"/><path d="M16 7h6v6"/>',
        'sun' => '<circle cx="12" cy="12" r="4"/><path d="M12 2v2M12 20v2M4.9 4.9l1.4 1.4M17.7 17.7l1.4 1.4M2 12h2M20 12h2M4.9 19.1l1.4-1.4M17.7 6.3l1.4-1.4"/>',
        'plus' => '<path d="M12 5v14M5 12h14"/>',
        'upload' => '<path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><path d="m17 8-5-5-5 5"/><path d="M12 3v12"/>',
        'send' => '<path d="M22 2 11 13"/><path d="M22 2 15 22l-4-9-9-4z"/>',
        'tree' => '<rect x="9" y="2" width="6" height="5" rx="1"/><rect x="2" y="17" width="6" height="5" rx="1"/><rect x="16" y="17" width="6" height="5" rx="1"/><path d="M12 7v5M5 17v-2a3 3 0 0 1 3-3h8a3 3 0 0 1 3 3v2"/>',
        'chart' => '<path d="M3 3v18h18"/><path d="M8 17V9M13 17V5M18 17v-6"/>',
        'receipt' => '<path d="M4 2v20l3-2 3 2 3-2 3 2 3-2 3 2V2l-3 2-3-2-3 2-3-2-3 2z"/><path d="M8 8h8M8 12h8M8 16h5"/>',
        'copy' => '<rect x="9" y="9" width="13" height="13" rx="2"/><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/>',
        'check' => '<path d="M20 6 9 17l-5-5"/>',
        'x' => '<path d="M18 6 6 18M6 6l12 12"/>',
        'users' => '<path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.9M16 3.1a4 4 0 0 1 0 7.8"/>',
        'crown' => '<path d="m2 4 3 12h14l3-12-6 7-4-7-4 7z"/><path d="M5 20h14"/>',
        'ticket' => '<path d="M2 9a3 3 0 0 0 0 6v2a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2v-2a3 3 0 0 0 0-6V7a2 2 0 0 0-2-2H4a2 2 0 0 0-2 2z"/><path d="M13 5v2M13 17v2M13 11v2"/>',
        'shield' => '<path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/><path d="m9 12 2 2 4-4"/>',
    ];
    $icon = fn ($n, $cls = 'ic') => '<svg class="' . $cls . '" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">' . $icons[$n] . '</svg>';
@endphp

<div class="md">

    {{-- ============ HERO ============ --}}
    <header class="md-hero">
        <div class="md-hero__intro">
            <p class="md-eyebrow">{{ now()->format('l, d M Y') }}</p>
            <h1 class="md-hero__title">{{ $greeting }}, {{ \Illuminate\Support\Str::of($name)->explode(' ')->first() }} <span aria-hidden="true">👋</span></h1>
            <div class="md-chips">
                <button type="button" class="md-chip md-chip--btn" data-copy="{{ $user->member_id }}" aria-label="Copy member ID {{ $user->member_id }}">
                    ID: <strong>{{ $user->member_id }}</strong> {!! $icon('copy', 'ic ic--sm') !!}
                </button>
                <span class="md-chip md-chip--rank">{!! $icon('crown', 'ic ic--sm') !!} {{ $rankProgress['rank'] ?? 'No rank yet' }}</span>
                @if ($firstOrder)
                    <span class="md-chip">Active since {{ \Carbon\Carbon::parse($firstOrder->created_at)->format('M Y') }}</span>
                @else
                    <span class="md-chip md-chip--warn">Not activated</span>
                @endif
                @if ($sponsor)
                    <span class="md-chip">Sponsor: {{ $sponsor->name }} ({{ $sponsor->member_id }})</span>
                @endif
            </div>
        </div>

        <nav class="md-actions" aria-label="Quick actions">
            <a href="{{ route('member.topup') }}" class="md-action md-action--primary">{!! $icon('plus') !!}<span>Top up</span></a>
            <a href="{{ route('wallet.fund') }}" class="md-action">{!! $icon('upload') !!}<span>Add funds</span></a>
            <a href="{{ route('withdraw.index') }}" class="md-action">{!! $icon('send') !!}<span>Withdraw</span></a>
            <a href="{{ route('tree') }}" class="md-action">{!! $icon('tree') !!}<span>My team</span></a>
            <a href="{{ route('reports.index') }}" class="md-action">{!! $icon('chart') !!}<span>Reports</span></a>
        </nav>
    </header>

    {{-- ============ KPI CARDS ============ --}}
    <section class="md-kpis" aria-label="Key figures">
        <article class="md-kpi md-kpi--feature">
            <div class="md-kpi__top">
                <span class="md-kpi__icon">{!! $icon('wallet') !!}</span>
                <h2 class="md-kpi__label">Main wallet</h2>
            </div>
            <p class="md-kpi__value">₹{{ number_format($walletBalance, 2) }}</p>
            <p class="md-kpi__meta">Available to withdraw or top up</p>
            <a href="{{ route('withdraw.index') }}" class="md-kpi__link">Withdraw <span aria-hidden="true">→</span></a>
        </article>

        <article class="md-kpi">
            <div class="md-kpi__top">
                <span class="md-kpi__icon md-kpi__icon--violet">{!! $icon('bag') !!}</span>
                <h2 class="md-kpi__label">Repurchase wallet</h2>
            </div>
            <p class="md-kpi__value">₹{{ number_format($repurchaseBalance, 2) }}</p>
            <p class="md-kpi__meta">For the Repurchase Package only</p>
            <a href="{{ route('repurchase.wallet') }}" class="md-kpi__link">Statement <span aria-hidden="true">→</span></a>
        </article>

        <article class="md-kpi">
            <div class="md-kpi__top">
                <span class="md-kpi__icon">{!! $icon('trend') !!}</span>
                <h2 class="md-kpi__label">This month</h2>
            </div>
            <p class="md-kpi__value">₹{{ number_format($monthEarning, 2) }}</p>
            <p class="md-kpi__meta">
                @if ($monthDelta !== null)
                    <span class="md-delta md-delta--{{ $monthDelta >= 0 ? 'up' : 'down' }}">
                        <span aria-hidden="true">{{ $monthDelta >= 0 ? '▲' : '▼' }}</span>
                        {{ number_format(abs($monthDelta), 1) }}%
                        <span class="sr-only">{{ $monthDelta >= 0 ? 'up' : 'down' }}</span>
                    </span>
                    vs last month (₹{{ number_format($lastMonthEarning, 0) }})
                @else
                    No earnings last month to compare
                @endif
            </p>
        </article>

        <article class="md-kpi">
            <div class="md-kpi__top">
                <span class="md-kpi__icon md-kpi__icon--amber">{!! $icon('sun') !!}</span>
                <h2 class="md-kpi__label">Today</h2>
            </div>
            <p class="md-kpi__value">₹{{ number_format($todayEarning, 2) }}</p>
            <p class="md-kpi__meta">Lifetime earnings ₹{{ number_format($totalEarning, 2) }}</p>
        </article>
    </section>

    {{-- ============ EARNINGS ============ --}}
    <section class="md-grid md-grid--8-4">
        <article class="md-card">
            <div class="md-card__head">
                <div>
                    <h2 class="md-card__title">Earnings — last 30 days</h2>
                    <p class="md-card__sub">₹{{ number_format($trendTotal, 2) }} earned across all income types</p>
                </div>
            </div>
            @if ($trendTotal > 0)
                <div class="md-chart">
                    <canvas id="earningsChart" role="img"
                        aria-label="Bar chart of daily earnings for the last 30 days, total ₹{{ number_format($trendTotal, 2) }}"></canvas>
                </div>
                <details class="md-table-toggle">
                    <summary>View as table</summary>
                    <div class="table-res">
                        <table class="md-table">
                            <thead><tr><th scope="col">Date</th><th scope="col" class="num">Earned</th></tr></thead>
                            <tbody>
                                @foreach ($trend->reverse() as $d)
                                    @if ($d['amount'] > 0)
                                        <tr><td>{{ $d['date'] }}</td><td class="num">₹{{ number_format($d['amount'], 2) }}</td></tr>
                                    @endif
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </details>
            @else
                <div class="md-empty">
                    <p>No earnings in the last 30 days yet.</p>
                    <a href="#referral" class="md-btn md-btn--ghost">Invite someone to get started</a>
                </div>
            @endif
        </article>

        <article class="md-card">
            <div class="md-card__head">
                <div>
                    <h2 class="md-card__title">Income breakdown</h2>
                    <p class="md-card__sub">Lifetime · ₹{{ number_format($totalEarning, 2) }}</p>
                </div>
            </div>
            <ul class="md-bars">
                @foreach ($incomeBreakdown as $label => $amount)
                    <li>
                        <div class="md-bars__row">
                            <span>{{ $label }}</span>
                            <strong>₹{{ number_format($amount, 2) }}</strong>
                        </div>
                        <div class="md-bars__track" aria-hidden="true">
                            <span style="width: {{ $amount > 0 ? max(2, round($amount / $maxIncome * 100, 1)) : 0 }}%"></span>
                        </div>
                    </li>
                @endforeach
            </ul>
            <p class="md-foot">90% of each earning goes to your main wallet and 10% to your Repurchase Wallet.</p>
        </article>
    </section>

    {{-- ============ TEAM · REFERRAL · ACCOUNT ============ --}}
    <section class="md-grid md-grid--3">
        <article class="md-card">
            <div class="md-card__head">
                <h2 class="md-card__title">{!! $icon('users', 'ic ic--title') !!} My team</h2>
                <a href="{{ route('tree') }}" class="md-link">Tree view</a>
            </div>
            <div class="md-legs">
                <div><span class="md-legs__n">{{ number_format($leftDownline) }}</span><span class="md-legs__l"><i class="md-key md-key--l" aria-hidden="true"></i>Left leg</span></div>
                <div><span class="md-legs__n">{{ number_format($totalDownline) }}</span><span class="md-legs__l">Total</span></div>
                <div><span class="md-legs__n">{{ number_format($rightDownline) }}</span><span class="md-legs__l"><i class="md-key md-key--r" aria-hidden="true"></i>Right leg</span></div>
            </div>
            <div class="md-split" role="img"
                aria-label="Leg balance: {{ $leftDownline }} left, {{ $rightDownline }} right">
                <span class="md-split__l" style="width: {{ round($leftDownline / $legTotal * 100, 1) }}%"></span>
                <span class="md-split__r" style="width: {{ round($rightDownline / $legTotal * 100, 1) }}%"></span>
            </div>
            <dl class="md-stats">
                <div><dt>Direct referrals</dt><dd>{{ $directReferrals }}</dd></div>
                <div><dt>Active directs</dt><dd>{{ $activeDirects }}</dd></div>
                <div><dt>Joined this month</dt><dd>{{ $newThisMonth }}</dd></div>
            </dl>
        </article>

        <article class="md-card" id="referral">
            <div class="md-card__head">
                <h2 class="md-card__title">{!! $icon('send', 'ic ic--title') !!} Invite &amp; earn</h2>
            </div>
            <p class="md-card__sub">Share your link. Choose the leg new members join.</p>
            <div class="md-seg" role="radiogroup" aria-label="Referral leg">
                <label><input type="radio" name="leg" value="1" checked><span>Left leg</span></label>
                <label><input type="radio" name="leg" value="2"><span>Right leg</span></label>
            </div>
            <label for="refLink" class="sr-only">Your referral link</label>
            <div class="md-copy">
                <input type="text" id="refLink" readonly value="{{ $refBase }}&leg=1" data-base="{{ $refBase }}">
                <button type="button" class="md-btn md-btn--icon" data-copy-target="refLink" aria-label="Copy referral link">{!! $icon('copy') !!}</button>
            </div>
            <div class="md-row">
                <button type="button" class="md-btn md-btn--primary" data-copy-target="refLink">Copy link</button>
                <a class="md-btn md-btn--wa" id="waShare" href="#" target="_blank" rel="noopener">WhatsApp</a>
            </div>
        </article>

        <article class="md-card">
            <div class="md-card__head">
                <h2 class="md-card__title">{!! $icon('shield', 'ic ic--title') !!} Account</h2>
                <a href="{{ route('profile') }}" class="md-link">Profile</a>
            </div>
            <dl class="md-list">
                <div><dt>Package</dt><dd>{{ $latestOrder->package ?? '—' }}</dd></div>
                <div><dt>Last top-up</dt><dd>{{ $latestOrder ? \Carbon\Carbon::parse($latestOrder->created_at)->format('d M Y') : '—' }}</dd></div>
                <div>
                    <dt>EMIs</dt>
                    <dd>
                        @if ($paymentDue)
                            {{ $paymentDue['paid'] }}/{{ $paymentDue['total_emis'] }}
                            @if ($paymentDue['due_count'] > 0)
                                · <a href="{{ route('payments.due') }}" class="md-pill md-pill--bad">{{ $paymentDue['due_count'] }} due</a>
                            @else
                                · <span class="md-pill md-pill--good">Up to date</span>
                            @endif
                        @elseif ($user->emi_status === 'completed')
                            <span class="md-pill md-pill--good">Completed</span>
                        @else
                            —
                        @endif
                    </dd>
                </div>
                @if ($paymentDue)
                    <div><dt>Next EMI</dt><dd>{{ $paymentDue['next']['due_date']->format('d M Y') }} · ₹{{ number_format($paymentDue['amount_per_emi']) }}</dd></div>
                @endif
            </dl>
            <div class="md-kyc">
                <div class="md-row md-row--between">
                    <span class="md-kyc__title">Profile &amp; KYC</span>
                    <span class="md-kyc__count">{{ $kycDone }}/{{ $kycTotal }}</span>
                </div>
                <div class="md-progress" role="progressbar" aria-valuemin="0" aria-valuemax="{{ $kycTotal }}" aria-valuenow="{{ $kycDone }}" aria-label="Profile and KYC completion">
                    <span style="width: {{ round($kycDone / $kycTotal * 100) }}%"></span>
                </div>
                <ul class="md-checks">
                    @foreach ($kycChecklist as $item => $ok)
                        <li class="{{ $ok ? 'is-ok' : '' }}">{!! $icon($ok ? 'check' : 'x', 'ic ic--sm') !!} {{ $item }}<span class="sr-only">: {{ $ok ? 'done' : 'missing' }}</span></li>
                    @endforeach
                </ul>
                @if ($kycDone < $kycTotal)
                    <a href="{{ route('profile.kyc') }}" class="md-btn md-btn--ghost md-btn--block">Complete KYC</a>
                @endif
            </div>
        </article>
    </section>

    {{-- ============ RANK · MATRIX ============ --}}
    <section class="md-grid md-grid--7-5">
        <article class="md-card">
            <div class="md-card__head">
                <div>
                    <h2 class="md-card__title">{!! $icon('crown', 'ic ic--title') !!} Rank &amp; rewards</h2>
                    <p class="md-card__sub">{{ $rankProgress['rank'] ?? 'No rank yet' }}{{ $rankProgress['level'] ? ' · Level ' . $rankProgress['level'] : '' }}</p>
                </div>
                <span class="md-pill md-pill--gold">₹{{ number_format($incomeBreakdown['Rank Reward'] ?? 0) }} earned</span>
            </div>
            <dl class="md-stats">
                <div><dt>Pairs since launch</dt><dd>{{ number_format($rankProgress['pairs']) }}</dd></div>
                <div><dt>Active left</dt><dd>{{ number_format($rankProgress['left']) }}</dd></div>
                <div><dt>Active right</dt><dd>{{ number_format($rankProgress['right']) }}</dd></div>
            </dl>
            @if ($rankProgress['next'])
                <div class="md-row md-row--between md-next">
                    <span>Next: <strong>{{ $rankProgress['next']['name'] }}</strong></span>
                    <span>{{ number_format($rankProgress['to_next']) }} pair{{ $rankProgress['to_next'] == 1 ? '' : 's' }} to go · ₹{{ number_format($rankProgress['next']['reward']) }}</span>
                </div>
                <div class="md-progress md-progress--lg" role="progressbar" aria-valuemin="0" aria-valuemax="100"
                    aria-valuenow="{{ round($rankProgress['next_pct']) }}" aria-label="Progress to {{ $rankProgress['next']['name'] }}">
                    <span style="width: {{ round($rankProgress['next_pct'], 1) }}%"></span>
                </div>
            @else
                <p class="md-next">🎉 Highest rank reached.</p>
            @endif
            <details class="md-table-toggle">
                <summary>See all ranks</summary>
                <div class="table-res">
                    <table class="md-table">
                        <thead><tr><th scope="col">Level</th><th scope="col">Rank</th><th scope="col" class="num">Total pairs</th><th scope="col" class="num">Reward</th><th scope="col"><span class="sr-only">Achieved</span></th></tr></thead>
                        <tbody>
                            @foreach ($rankLadder as $row)
                                <tr class="{{ $row['level'] <= ($rankProgress['level'] ?? 0) ? 'is-done' : '' }}">
                                    <td>{{ $row['level'] }}</td>
                                    <td>{{ $row['name'] }}</td>
                                    <td class="num">{{ number_format($row['cumulative']) }}</td>
                                    <td class="num">₹{{ number_format($row['reward']) }}</td>
                                    <td>{!! $row['level'] <= ($rankProgress['level'] ?? 0) ? $icon('check', 'ic ic--sm ic--ok') . '<span class="sr-only">Achieved</span>' : '' !!}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </details>
        </article>

        <article class="md-card">
            <div class="md-card__head">
                <div>
                    <h2 class="md-card__title">{!! $icon('tree', 'ic ic--title') !!} Matrix progress</h2>
                    <p class="md-card__sub">Rank level {{ $user->rank_level ?? 0 }}</p>
                </div>
            </div>
            <ul class="md-tiers">
                @foreach ($tiers as $t)
                    @php $pct = min(100, $t['count'] / $t['target'] * 100); @endphp
                    <li>
                        <div class="md-row md-row--between">
                            <span>{{ $t['label'] }}</span>
                            <strong>{{ $t['count'] }}/{{ $t['target'] }}</strong>
                        </div>
                        <div class="md-progress" role="progressbar" aria-valuemin="0" aria-valuemax="{{ $t['target'] }}"
                            aria-valuenow="{{ $t['count'] }}" aria-label="{{ $t['label'] }} progress">
                            <span style="width: {{ round($pct, 1) }}%"></span>
                        </div>
                    </li>
                @endforeach
            </ul>
            <dl class="md-stats md-stats--2">
                <div><dt>Withdrawn</dt><dd>₹{{ number_format($withdrawals->withdrawn, 0) }}</dd></div>
                <div><dt>Pending payout</dt><dd>₹{{ number_format($withdrawals->pending_amount, 0) }}<small> ({{ $withdrawals->pending_count }})</small></dd></div>
            </dl>
        </article>
    </section>

    {{-- ============ ACTIVITY · LUCKY DRAW ============ --}}
    <section class="md-grid md-grid--8-4">
        <article class="md-card">
            <div class="md-card__head">
                <h2 class="md-card__title">{!! $icon('receipt', 'ic ic--title') !!} Recent activity</h2>
                <a href="{{ route('reports.index') }}" class="md-link">All transactions</a>
            </div>
            @if ($recentTransactions->isEmpty())
                <div class="md-empty"><p>No transactions yet.</p></div>
            @else
                <ul class="md-activity">
                    @foreach ($recentTransactions as $t)
                        @php $isCredit = strtolower($t->type) === 'credit'; @endphp
                        <li>
                            <span class="md-activity__dot md-activity__dot--{{ $isCredit ? 'in' : 'out' }}" aria-hidden="true">{{ $isCredit ? '+' : '−' }}</span>
                            <div class="md-activity__body">
                                <strong>{{ $t->bonus_type ? \Illuminate\Support\Str::headline($t->bonus_type) : ($isCredit ? 'Credit' : 'Debit') }}</strong>
                                <span title="{{ $t->remarks }}">{{ \Illuminate\Support\Str::limit($t->remarks, 70) }}</span>
                            </div>
                            <div class="md-activity__amt">
                                <strong class="{{ $isCredit ? 'is-in' : 'is-out' }}">{{ $isCredit ? '+' : '−' }}₹{{ number_format($t->amount, 2) }}</strong>
                                <time datetime="{{ \Carbon\Carbon::parse($t->created_at)->toIso8601String() }}">{{ \Carbon\Carbon::parse($t->created_at)->diffForHumans() }}</time>
                            </div>
                        </li>
                    @endforeach
                </ul>
            @endif
        </article>

        <article class="md-card md-lucky">
            <div class="md-card__head">
                <h2 class="md-card__title">{!! $icon('ticket', 'ic ic--title') !!} Lucky draw</h2>
                @if ($cycle)
                    <span class="md-pill {{ $cycle->status === 'won' ? 'md-pill--gold' : 'md-pill--good' }}">{{ $rewardStatus }}</span>
                @endif
            </div>
            @if ($cycle)
                <dl class="md-stats md-stats--2">
                    <div><dt>Months</dt><dd>{{ $cycle->current_month }}/16</dd></div>
                    <div><dt>Vouchers</dt><dd>{{ $totalVouchers }}</dd></div>
                    <div><dt>Active</dt><dd>{{ $unusedVouchers }}</dd></div>
                    <div><dt>Reward</dt><dd class="md-small">{{ $rewardText }}</dd></div>
                </dl>
                <button type="button" class="md-btn md-btn--ghost md-btn--block" data-open-dialog="luckyDialog">View vouchers</button>
            @else
                <p class="md-card__sub">Buy a ₹50,000 or ₹1,00,000 package to join the lucky draw and win gold rewards.</p>
                <a href="{{ route('member.topup') }}" class="md-btn md-btn--ghost md-btn--block">Explore packages</a>
            @endif
        </article>
    </section>
</div>

@if ($cycle)
    <dialog id="luckyDialog" class="md-dialog" aria-labelledby="luckyDialogTitle">
        <div class="md-card__head">
            <h2 id="luckyDialogTitle" class="md-card__title">🎟 Lucky vouchers &amp; rewards</h2>
            <button type="button" class="md-btn md-btn--icon" data-close-dialog aria-label="Close">{!! $icon('x') !!}</button>
        </div>
        <p class="md-reward {{ $cycle->status === 'won' ? 'is-won' : '' }}"><strong>{{ $rewardStatus }}</strong> — {{ $rewardText }}</p>
        @forelse ($voucherGroups as $month => $vouchers)
            <details class="md-month">
                <summary>Month {{ $month }} <span>({{ count($vouchers) }})</span></summary>
                <div class="md-vouchers">
                    @foreach ($vouchers as $v)
                        <span class="md-voucher md-voucher--{{ $v->status }}">{{ $v->voucher_code }}<span class="sr-only"> ({{ $v->status }})</span></span>
                    @endforeach
                </div>
            </details>
        @empty
            <p class="md-card__sub">No vouchers issued yet.</p>
        @endforelse
    </dialog>
@endif

<div class="md-toast" id="mdToast" role="status" aria-live="polite"></div>

<style>
    .md { --g-900:#0d3b2a; --g-700:#08734f; --g-600:#0f8a5f; --g-100:#dff3e7; --g-50:#f1faf5;
          --v-600:#6a4bb0; --v-100:#ece6f8; --a-600:#a15c0c; --a-100:#fdf0d8;
          --bad:#b9304b; --bad-100:#fbe9ed;
          --ink:#142e25; --ink-2:#496359; --line:#d3e6da; --surface:#fff;
          --r-lg:18px; --r-md:12px; --shadow:0 1px 2px rgba(13,77,51,.05), 0 8px 24px rgba(13,77,51,.06);
          font-family:Inter,system-ui,sans-serif; color:var(--ink); display:flex; flex-direction:column; gap:20px; }
    .md .ic { width:20px; height:20px; flex-shrink:0; }
    .md .ic--sm { width:15px; height:15px; }
    .md .ic--title { width:18px; height:18px; color:var(--g-700); vertical-align:-3px; margin-right:4px; }
    .md .ic--ok { color:var(--g-700); }

    /* Hero */
    .md-hero { display:flex; flex-wrap:wrap; align-items:flex-end; justify-content:space-between; gap:18px;
               padding:24px 26px; border-radius:var(--r-lg);
               background:radial-gradient(120% 140% at 0% 0%, #c9ecd7 0%, transparent 55%),
                          radial-gradient(100% 120% at 100% 100%, #e6ddf7 0%, transparent 55%), var(--surface);
               border:1px solid var(--line); box-shadow:var(--shadow); }
    .md-eyebrow { margin:0 0 4px; font-size:13px; font-weight:600; color:var(--ink-2); }
    .md-hero__title { margin:0 0 12px; font-size:clamp(22px, 3vw, 30px); font-weight:800; letter-spacing:-.5px; color:var(--g-900); }
    .md-chips { display:flex; flex-wrap:wrap; gap:8px; }
    .md-chip { display:inline-flex; align-items:center; gap:6px; padding:6px 12px; border-radius:99px; font-size:13px;
               background:rgba(255,255,255,.8); border:1px solid var(--line); color:var(--ink-2); }
    .md-chip strong { color:var(--ink); }
    .md-chip--btn { cursor:pointer; font:inherit; font-size:13px; }
    .md-chip--btn:hover { border-color:var(--g-600); color:var(--ink); }
    .md-chip--rank { background:var(--a-100); border-color:#f0d39c; color:#6b3d05; font-weight:600; }
    .md-chip--warn { background:var(--bad-100); border-color:#efb9c5; color:var(--bad); font-weight:600; }

    .md-actions { display:flex; flex-wrap:wrap; gap:10px; }
    .md-action { display:flex; flex-direction:column; align-items:center; justify-content:center; gap:6px; min-width:84px; min-height:72px;
                 padding:10px 12px; border-radius:var(--r-md); background:var(--surface); border:1px solid var(--line);
                 color:var(--ink); font-size:13px; font-weight:600; text-decoration:none; transition:transform .15s, box-shadow .15s, border-color .15s; }
    .md-action .ic { color:var(--g-700); }
    .md-action:hover { transform:translateY(-2px); border-color:var(--g-600); box-shadow:var(--shadow); }
    .md-action--primary { background:var(--g-700); border-color:var(--g-700); color:#fff; }
    .md-action--primary .ic { color:#fff; }
    .md-action--primary:hover { background:#056344; }

    /* KPIs */
    .md-kpis { display:grid; grid-template-columns:repeat(4, minmax(0,1fr)); gap:16px; }
    .md-kpi { position:relative; padding:18px 20px; border-radius:var(--r-lg); background:var(--surface); border:1px solid var(--line); box-shadow:var(--shadow); }
    .md-kpi--feature { background:linear-gradient(140deg, #0b7a53, #075a3e); border-color:#075a3e; color:#fff; }
    .md-kpi__top { display:flex; align-items:center; gap:10px; margin-bottom:12px; }
    .md-kpi__icon { display:grid; place-items:center; width:36px; height:36px; border-radius:10px; background:var(--g-100); color:var(--g-700); }
    .md-kpi__icon--violet { background:var(--v-100); color:var(--v-600); }
    .md-kpi__icon--amber { background:var(--a-100); color:var(--a-600); }
    .md-kpi--feature .md-kpi__icon { background:rgba(255,255,255,.16); color:#fff; }
    .md-kpi__label { margin:0; font-size:13px; font-weight:600; color:var(--ink-2); text-transform:none; }
    .md-kpi--feature .md-kpi__label { color:#d4f0e1; }
    .md-kpi__value { margin:0; font-size:clamp(22px, 2.4vw, 28px); font-weight:800; letter-spacing:-.5px; font-variant-numeric:tabular-nums; }
    .md-kpi__meta { margin:6px 0 0; font-size:13px; color:var(--ink-2); }
    .md-kpi--feature .md-kpi__meta { color:#cdeadb; }
    .md-kpi__link { display:inline-block; margin-top:10px; font-size:13px; font-weight:700; color:var(--g-700); text-decoration:none; }
    .md-kpi--feature .md-kpi__link { color:#fff; }
    .md-kpi__link:hover { text-decoration:underline; }
    .md-delta { display:inline-flex; align-items:center; gap:3px; padding:1px 7px; border-radius:99px; font-weight:700; font-size:12px; }
    .md-delta--up { background:var(--g-100); color:#0b5a3c; }
    .md-delta--down { background:var(--bad-100); color:var(--bad); }

    /* Cards & grid */
    .md-grid { display:grid; gap:16px; }
    .md-grid--8-4 { grid-template-columns:minmax(0,2fr) minmax(0,1fr); }
    .md-grid--7-5 { grid-template-columns:minmax(0,7fr) minmax(0,5fr); }
    .md-grid--3 { grid-template-columns:repeat(3, minmax(0,1fr)); }
    .md-card { display:flex; flex-direction:column; gap:14px; padding:20px 22px; border-radius:var(--r-lg);
               background:var(--surface); border:1px solid var(--line); box-shadow:var(--shadow); min-width:0; }
    .md-card__head { display:flex; align-items:flex-start; justify-content:space-between; gap:12px; }
    .md-card__title { margin:0; font-size:16px; font-weight:700; color:var(--ink); }
    .md-card__sub { margin:2px 0 0; font-size:13px; color:var(--ink-2); }
    .md-link { font-size:13px; font-weight:700; color:var(--g-700); text-decoration:none; white-space:nowrap; }
    .md-link:hover { text-decoration:underline; }
    .md-foot { margin:auto 0 0; font-size:12px; color:var(--ink-2); }
    .md-row { display:flex; flex-wrap:wrap; align-items:center; gap:10px; }
    .md-row--between { justify-content:space-between; }
    .md-small { font-size:13px !important; font-weight:600 !important; }

    .md-chart { position:relative; height:240px; }
    .md-table-toggle summary { cursor:pointer; font-size:13px; font-weight:600; color:var(--g-700); }
    .md-table-toggle[open] summary { margin-bottom:8px; }
    .md-table { width:100%; border-collapse:collapse; font-size:14px; }
    .md-table th, .md-table td { padding:8px 10px; text-align:left; border-bottom:1px solid var(--line); }
    .md-table .num { text-align:right; font-variant-numeric:tabular-nums; }
    .md-table tr.is-done td { color:var(--g-700); font-weight:600; }
    .md-empty { display:flex; flex-direction:column; align-items:center; justify-content:center; gap:10px; min-height:180px;
                text-align:center; color:var(--ink-2); background:var(--g-50); border-radius:var(--r-md); }
    .md-empty p { margin:0; }

    /* Breakdown bars */
    .md-bars { list-style:none; margin:0; padding:0; display:flex; flex-direction:column; gap:12px; }
    .md-bars__row { display:flex; justify-content:space-between; gap:8px; font-size:14px; margin-bottom:5px; }
    .md-bars__row span { color:var(--ink-2); }
    .md-bars__row strong { font-variant-numeric:tabular-nums; }
    .md-bars__track, .md-progress { height:8px; border-radius:99px; background:#e6f1ea; overflow:hidden; }
    .md-bars__track span, .md-progress span { display:block; height:100%; border-radius:99px; background:var(--g-600); }
    .md-progress--lg { height:10px; }

    /* Team */
    .md-legs { display:grid; grid-template-columns:repeat(3,1fr); text-align:center; }
    .md-legs > div { display:flex; flex-direction:column; }
    .md-legs > div:nth-child(2) { border-inline:1px solid var(--line); }
    .md-legs__n { font-size:24px; font-weight:800; font-variant-numeric:tabular-nums; }
    .md-legs__l { font-size:12px; color:var(--ink-2); }
    .md-split { display:flex; gap:2px; height:10px; border-radius:99px; overflow:hidden; background:#e6f1ea; }
    .md-key { display:inline-block; width:8px; height:8px; border-radius:2px; margin-right:5px; vertical-align:0; }
    .md-key--l, .md-split__l { background:var(--g-600); }
    .md-key--r { background:var(--v-600); }
    .md-split__r { background:var(--v-600); }
    .md-stats { display:grid; grid-template-columns:repeat(3,1fr); gap:10px; margin:0; }
    .md-stats--2 { grid-template-columns:repeat(2,1fr); }
    .md-stats > div { padding:10px 12px; border-radius:var(--r-md); background:var(--g-50); }
    .md-stats dt { font-size:12px; color:var(--ink-2); }
    .md-stats dd { margin:2px 0 0; font-size:18px; font-weight:700; font-variant-numeric:tabular-nums; }
    .md-stats dd small { font-size:12px; font-weight:500; color:var(--ink-2); }

    /* Referral */
    .md-seg { display:inline-flex; padding:4px; border-radius:var(--r-md); background:var(--g-50); border:1px solid var(--line); }
    .md-seg label { position:relative; }
    .md-seg input { position:absolute; opacity:0; inset:0; margin:0; cursor:pointer; }
    .md-seg span { display:block; padding:7px 16px; border-radius:9px; font-size:13px; font-weight:600; color:var(--ink-2); }
    .md-seg input:checked + span { background:var(--surface); color:var(--g-700); box-shadow:0 1px 3px rgba(13,77,51,.15); }
    .md-seg input:focus-visible + span { outline:2px solid var(--g-600); outline-offset:1px; }
    .md-copy { display:flex; gap:8px; }
    .md-copy input { flex:1; min-width:0; padding:10px 12px; border-radius:10px; border:1px solid var(--line); background:var(--g-50);
                     font-size:13px; color:var(--ink); }

    /* Buttons & pills */
    .md-btn { display:inline-flex; align-items:center; justify-content:center; gap:6px; min-height:42px; padding:9px 16px;
              border-radius:10px; border:1px solid var(--line); background:var(--surface); color:var(--ink);
              font:inherit; font-size:14px; font-weight:700; text-decoration:none; cursor:pointer; }
    .md-btn:hover { border-color:var(--g-600); }
    .md-btn--primary { background:var(--g-700); border-color:var(--g-700); color:#fff; }
    .md-btn--primary:hover { background:#056344; }
    .md-btn--wa { background:#1c8f4c; border-color:#1c8f4c; color:#fff; }
    .md-btn--wa:hover { background:#157a3f; }
    .md-btn--ghost { color:var(--g-700); border-color:#a9d0b9; }
    .md-btn--icon { width:42px; padding:0; }
    .md-btn--block { width:100%; }
    .md-pill { display:inline-block; padding:3px 10px; border-radius:99px; font-size:12px; font-weight:700; text-decoration:none; white-space:nowrap; }
    .md-pill--good { background:var(--g-100); color:#0b5a3c; }
    .md-pill--bad { background:var(--bad-100); color:var(--bad); }
    .md-pill--gold { background:var(--a-100); color:#6b3d05; }

    /* Account */
    .md-list { margin:0; display:flex; flex-direction:column; }
    .md-list > div { display:flex; justify-content:space-between; gap:10px; padding:8px 0; border-bottom:1px dashed var(--line); font-size:14px; }
    .md-list dt { color:var(--ink-2); }
    .md-list dd { margin:0; font-weight:600; text-align:right; }
    .md-kyc { display:flex; flex-direction:column; gap:8px; padding:12px; border-radius:var(--r-md); background:var(--g-50); }
    .md-kyc__title { font-size:13px; font-weight:700; }
    .md-kyc__count { font-size:13px; font-weight:700; color:var(--g-700); }
    .md-checks { list-style:none; margin:0; padding:0; display:flex; flex-wrap:wrap; gap:6px 12px; font-size:13px; color:var(--bad); }
    .md-checks li { display:inline-flex; align-items:center; gap:4px; }
    .md-checks li.is-ok { color:var(--g-700); }

    .md-next { margin:0; font-size:14px; color:var(--ink-2); }
    .md-next strong { color:var(--ink); }
    .md-tiers { list-style:none; margin:0; padding:0; display:flex; flex-direction:column; gap:14px; font-size:14px; }
    .md-tiers .md-row { margin-bottom:6px; }

    /* Activity */
    .md-activity { list-style:none; margin:0; padding:0; }
    .md-activity li { display:flex; align-items:center; gap:12px; padding:10px 0; border-bottom:1px solid var(--line); }
    .md-activity li:last-child { border-bottom:0; }
    .md-activity__dot { display:grid; place-items:center; width:34px; height:34px; border-radius:50%; flex-shrink:0; font-weight:800; }
    .md-activity__dot--in { background:var(--g-100); color:var(--g-700); }
    .md-activity__dot--out { background:var(--bad-100); color:var(--bad); }
    .md-activity__body { flex:1; min-width:0; display:flex; flex-direction:column; }
    .md-activity__body strong { font-size:14px; }
    .md-activity__body span { font-size:12px; color:var(--ink-2); white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
    .md-activity__amt { display:flex; flex-direction:column; align-items:flex-end; text-align:right; }
    .md-activity__amt strong { font-variant-numeric:tabular-nums; font-size:14px; white-space:nowrap; }
    .md-activity__amt .is-in { color:var(--g-700); }
    .md-activity__amt .is-out { color:var(--bad); }
    .md-activity__amt time { font-size:12px; color:var(--ink-2); white-space:nowrap; }

    /* Dialog */
    .md-dialog { width:min(560px, calc(100vw - 32px)); max-height:80vh; padding:22px; border:0; border-radius:var(--r-lg);
                 box-shadow:0 30px 80px rgba(13,77,51,.3); color:var(--ink); }
    .md-dialog::backdrop { background:rgba(20,46,37,.45); }
    .md-reward { margin:14px 0; padding:12px 14px; border-radius:var(--r-md); background:var(--g-50); font-size:14px; }
    .md-reward.is-won { background:var(--a-100); }
    .md-month { border-top:1px solid var(--line); padding:10px 0; }
    .md-month summary { cursor:pointer; font-weight:600; }
    .md-month summary span { color:var(--ink-2); font-weight:400; }
    .md-vouchers { display:flex; flex-wrap:wrap; gap:6px; margin-top:10px; }
    .md-voucher { padding:4px 10px; border-radius:8px; font:600 12px/1.4 ui-monospace, monospace; background:var(--g-100); color:#0b5a3c; }
    .md-voucher--used { background:#eef1f4; color:#5a6570; text-decoration:line-through; }

    .md-toast { position:fixed; left:50%; bottom:24px; transform:translate(-50%, 20px); padding:10px 18px; border-radius:99px;
                background:var(--ink, #142e25); color:#fff; font-size:14px; font-weight:600; opacity:0; pointer-events:none;
                transition:opacity .2s, transform .2s; z-index:3000; }
    .md-toast.is-on { opacity:1; transform:translate(-50%, 0); }

    @media (max-width: 1200px) {
        .md-kpis { grid-template-columns:repeat(2, minmax(0,1fr)); }
        .md-grid--3 { grid-template-columns:repeat(2, minmax(0,1fr)); }
        .md-grid--3 > :last-child { grid-column:1 / -1; }
    }
    @media (max-width: 900px) {
        .md-grid--8-4, .md-grid--7-5, .md-grid--3 { grid-template-columns:minmax(0,1fr); }
        .md-hero { padding:20px; }
        .md-actions { width:calc(100% + 40px); margin:0 -20px; padding:2px 20px 6px; flex-wrap:nowrap; overflow-x:auto;
                      scroll-snap-type:x mandatory; scrollbar-width:none; }
        .md-actions::-webkit-scrollbar { display:none; }
        .md-action { flex:0 0 auto; min-width:82px; scroll-snap-align:start; white-space:nowrap; }
    }
    @media (max-width: 520px) {
        .md-kpis { grid-template-columns:repeat(2, minmax(0,1fr)); gap:12px; }
        .md-kpi { padding:14px; }
        .md-kpi--feature, .md-kpi:last-child { grid-column:1 / -1; }
        .md-kpi__top { margin-bottom:8px; }
        .md-kpi__icon { width:30px; height:30px; }
        .md-kpi:not(.md-kpi--feature) .md-kpi__value { font-size:19px; }
        .md-kpi:not(.md-kpi--feature) .md-kpi__meta { font-size:12px; }
        .md-stats > div { padding:8px 10px; }
        .md-stats dt { font-size:11px; }
        .md-stats dd { font-size:16px; }
        .md-card { padding:18px; }
    }
    @media (prefers-reduced-motion: reduce) {
        .md-action, .md-toast { transition:none; }
        .md-action:hover { transform:none; }
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function () {
    var toast = document.getElementById('mdToast');
    var toastTimer;
    function showToast(msg) {
        toast.textContent = msg;
        toast.classList.add('is-on');
        clearTimeout(toastTimer);
        toastTimer = setTimeout(function () { toast.classList.remove('is-on'); }, 2200);
    }
    function copyText(text, label) {
        var done = function () { showToast(label + ' copied'); };
        if (navigator.clipboard && window.isSecureContext) {
            navigator.clipboard.writeText(text).then(done, function () { fallback(); });
        } else { fallback(); }
        function fallback() {
            var ta = document.createElement('textarea');
            ta.value = text; ta.setAttribute('readonly', ''); ta.style.position = 'fixed'; ta.style.opacity = '0';
            document.body.appendChild(ta); ta.select();
            try { document.execCommand('copy'); done(); } catch (e) {}
            ta.remove();
        }
    }

    // Copy buttons
    document.querySelectorAll('[data-copy]').forEach(function (b) {
        b.addEventListener('click', function () { copyText(b.dataset.copy, 'Member ID'); });
    });
    document.querySelectorAll('[data-copy-target]').forEach(function (b) {
        b.addEventListener('click', function () { copyText(document.getElementById(b.dataset.copyTarget).value, 'Referral link'); });
    });

    // Referral leg toggle + WhatsApp share
    var ref = document.getElementById('refLink');
    var wa = document.getElementById('waShare');
    function syncRef() {
        var leg = document.querySelector('input[name="leg"]:checked').value;
        ref.value = ref.dataset.base + '&leg=' + leg;
        wa.href = 'https://wa.me/?text=' + encodeURIComponent('Join my team on Himalaya Pay: ' + ref.value);
    }
    document.querySelectorAll('input[name="leg"]').forEach(function (r) { r.addEventListener('change', syncRef); });
    syncRef();

    // Dialogs
    document.querySelectorAll('[data-open-dialog]').forEach(function (b) {
        b.addEventListener('click', function () { document.getElementById(b.dataset.openDialog).showModal(); });
    });
    document.querySelectorAll('dialog').forEach(function (d) {
        d.addEventListener('click', function (e) { if (e.target === d) d.close(); });
        d.querySelectorAll('[data-close-dialog]').forEach(function (b) { b.addEventListener('click', function () { d.close(); }); });
    });

    // Earnings chart (single series, so no legend; hover shows the day's amount)
    var el = document.getElementById('earningsChart');
    if (el && window.Chart) {
        var data = @json($trend);
        var reduce = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
        new Chart(el, {
            type: 'bar',
            data: {
                labels: data.map(function (d) { return d.date; }),
                datasets: [{
                    data: data.map(function (d) { return d.amount; }),
                    backgroundColor: '#0f8a5f',
                    hoverBackgroundColor: '#08734f',
                    borderRadius: { topLeft: 4, topRight: 4 },
                    borderSkipped: 'bottom',
                    maxBarThickness: 18,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                animation: reduce ? false : { duration: 500 },
                interaction: { mode: 'index', intersect: false },
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#142e25', padding: 10, displayColors: false,
                        callbacks: { label: function (c) { return '₹' + c.parsed.y.toLocaleString('en-IN', { minimumFractionDigits: 2 }); } }
                    }
                },
                scales: {
                    x: { grid: { display: false }, border: { display: false },
                         ticks: { color: '#496359', font: { size: 11 }, maxRotation: 0, autoSkip: true, maxTicksLimit: 8 } },
                    y: { beginAtZero: true, border: { display: false }, grid: { color: '#e6f1ea' },
                         ticks: { color: '#496359', font: { size: 11 }, maxTicksLimit: 5,
                                  callback: function (v) { return '₹' + Number(v).toLocaleString('en-IN'); } } }
                }
            }
        });
    }
});
</script>
@endsection
