@extends('common.layout')
@section('title', 'Transaction #' . $txn->id)
@section('main')
@include('admin.transactions._styles')
@php
    use App\Http\Controllers\Admin\TransactionLedgerController as Ledger;
    $isDebit = strtolower((string) $txn->type) === 'debit';
    $fmt = fn ($d) => $d ? date('d M Y, h:i:s A', strtotime($d)) : '—';
@endphp

<div class="atx">
    <div class="header">
        <h1>Transaction #{{ $txn->id }}</h1>
        <div class="user-info"><a class="link" href="{{ url()->previous() !== url()->current() ? url()->previous() : route('admin.transactions') }}">← Back to all transactions</a></div>
    </div>

    <div class="grid2">
        <div class="card">
            <h2>What happened</h2>
            <dl class="kv">
                <dt>Amount</dt><dd><strong>{{ $isDebit ? '−' : '+' }}₹{{ number_format($txn->amount, 2) }}</strong> <span class="pill {{ $isDebit ? 'debit' : 'credit' }}">{{ $isDebit ? 'Debit (money out of wallet)' : 'Credit (money into wallet)' }}</span></dd>
                <dt>Type</dt><dd><span class="pill type">{{ $typeLabel }}</span> <small style="color:#8899a8;">{{ $txn->bonus_type ?: 'no type saved' }}</small></dd>
                <dt>When</dt><dd>{{ $fmt($txn->created_at) }}</dd>
                <dt>Why (remarks)</dt><dd>{{ $txn->remarks ?: '—' }}</dd>
                <dt>Raw type field</dt><dd>{{ $txn->type }}</dd>
            </dl>
            <h2 style="margin-top:18px;">How this type works</h2>
            <div class="how">{{ $typeHow }}</div>
        </div>

        <div class="card">
            <h2>Whose wallet</h2>
            @if ($member)
                <dl class="kv">
                    <dt>Member</dt><dd>{{ $member->member_id }} · {{ $member->name }} @if ($member->username)<small style="color:#8899a8;">({{ $member->username }})</small>@endif</dd>
                    <dt>Email / mobile</dt><dd>{{ $member->email }} @if ($member->mobile) · {{ $member->mobile }} @endif</dd>
                    <dt>Sponsor</dt><dd>{{ $sponsor ? $sponsor->member_id . ' · ' . $sponsor->name : '—' }}</dd>
                    <dt>Placed under</dt><dd>{{ $placement ? $placement->member_id . ' · ' . $placement->name : '—' }} @if ($member->position)<small style="color:#8899a8;">({{ $member->position }})</small>@endif</dd>
                    <dt>Joined</dt><dd>{{ $fmt($member->created_at) }}</dd>
                    <dt>Wallet balance now</dt><dd>₹{{ number_format($walletBalance ?? 0, 2) }}</dd>
                </dl>
                <p style="margin:14px 0 0;"><a class="link" href="{{ route('admin.transactions', ['member' => $member->member_id]) }}">See all of this member's transactions →</a></p>
            @else
                <p class="muted">Member #{{ $txn->user_id }} no longer exists.</p>
            @endif
        </div>
    </div>

    @if ($sponsorPayout)
        <div class="card">
            <h2>How this sponsor bonus was calculated</h2>
            <dl class="kv">
                <dt>From downline</dt><dd>{{ $sponsorPayout->source_member_id }} · {{ $sponsorPayout->source_name }}</dd>
                <dt>Income day</dt><dd>{{ date('d M Y', strtotime($sponsorPayout->income_date)) }}</dd>
                <dt>Their pair income that day</dt><dd>₹{{ number_format($sponsorPayout->binary_income, 2) }}</dd>
                <dt>Counted (max ₹5,000)</dt><dd>₹{{ number_format($sponsorPayout->capped_income, 2) }}</dd>
                <dt>10% bonus</dt><dd><strong>₹{{ number_format($sponsorPayout->bonus_amount, 2) }}</strong></dd>
            </dl>
            <p style="margin:14px 0 0;"><a class="link" href="{{ route('admin.transactions', ['member' => $sponsorPayout->source_member_id, 'from' => $sponsorPayout->income_date, 'to' => $sponsorPayout->income_date, 'types' => ['pair_bonus_normal', 'pair_bonus_starter', 'pair_bonus_2000', 'pair_bonus']]) }}">See the pair income entries it was based on →</a></p>
        </div>
    @endif

    @if ($nearbyOrders->isNotEmpty())
        <div class="card">
            <h2>Top-ups for this member within 2 minutes</h2>
            <p class="muted">A top-up writes its order, wallet debit and commissions within seconds, so these are usually what triggered this entry.</p>
            <div class="table-wrap">
                <table>
                    <thead><tr><th>Order</th><th>Time</th><th>For member</th><th>Paid by</th><th>Package</th><th class="num">Amount</th><th>Payment by</th><th>Status</th></tr></thead>
                    <tbody>
                        @foreach ($nearbyOrders as $o)
                            <tr>
                                <td>#{{ $o->id }}</td>
                                <td class="nowrap">{{ $fmt($o->created_at) }}</td>
                                <td class="nowrap">{{ $o->for_member_id ?? '—' }}<small>{{ $o->for_name }}</small></td>
                                <td class="nowrap">{{ $o->by_member_id ?? '—' }}<small>{{ $o->by_name }}</small></td>
                                <td>{{ $o->package_name ?? '—' }}</td>
                                <td class="num">₹{{ number_format($o->amount, 2) }}</td>
                                <td>{{ $o->payment_by }}</td>
                                <td><span class="pill {{ strtolower($o->status) }}">{{ ucfirst($o->status) }}</span></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    <div class="card">
        <h2>Other wallet entries within 2 minutes (all members)</h2>
        <p class="muted">Shows the chain of credits and debits written by the same action, e.g. one top-up paying direct, level and pair income to several uplines.</p>
        <div class="table-wrap">
            <table>
                <thead><tr><th>Txn</th><th>Time</th><th>Member</th><th>Type</th><th class="num">Amount</th><th>Remarks</th></tr></thead>
                <tbody>
                    @forelse ($nearbyTxns as $t)
                        @php $d = strtolower((string) $t->type) === 'debit'; @endphp
                        <tr>
                            <td><a class="link" href="{{ route('admin.transactions.show', $t->id) }}">#{{ $t->id }}</a></td>
                            <td class="nowrap">{{ $fmt($t->created_at) }}</td>
                            <td class="nowrap">{{ $t->member_id ?? '#' . $t->user_id }}<small>{{ $t->name }}</small></td>
                            <td><span class="pill type">{{ Ledger::typeLabel($t->bonus_type) }}</span></td>
                            <td class="num">{{ $d ? '−' : '+' }}₹{{ number_format($t->amount, 2) }}</td>
                            <td class="remarks">{{ $t->remarks ?: '—' }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="6">Nothing else was written within 2 minutes.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="card">
        <h2>This member's latest 15 wallet entries</h2>
        <div class="table-wrap">
            <table>
                <thead><tr><th>Txn</th><th>Time</th><th>Type</th><th class="num">Amount</th><th>Remarks</th></tr></thead>
                <tbody>
                    @foreach ($recent as $t)
                        @php $d = strtolower((string) $t->type) === 'debit'; @endphp
                        <tr @if ($t->id === $txn->id) style="outline:2px solid var(--accent);" @endif>
                            <td><a class="link" href="{{ route('admin.transactions.show', $t->id) }}">#{{ $t->id }}</a></td>
                            <td class="nowrap">{{ $fmt($t->created_at) }}</td>
                            <td><span class="pill type">{{ Ledger::typeLabel($t->bonus_type) }}</span></td>
                            <td class="num">{{ $d ? '−' : '+' }}₹{{ number_format($t->amount, 2) }}</td>
                            <td class="remarks">{{ $t->remarks ?: '—' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
