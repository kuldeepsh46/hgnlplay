@extends('common.layout')
@section('title', 'All Transactions')
@section('main')
@include('admin.transactions._styles')
@php
    use App\Http\Controllers\Admin\TransactionLedgerController as Ledger;
    $statusOptions = [
        'topups' => ['completed', 'pending', 'rejected'],
        'withdrawals' => ['pending', 'completed', 'rejected'],
        'funds' => ['pending', 'completed', 'rejected'],
    ];
@endphp

<div class="atx">
    <div class="header">
        <h1>All Transactions</h1>
        <div class="user-info">👤 {{ Auth::user()->username ?? Auth::user()->name }}</div>
    </div>

    <div class="card">
        <p class="muted">Every money movement in the system. Open any wallet entry to see who it was for, why it happened, how it was calculated and what else happened at the same moment.</p>

        <div class="tabs">
            @foreach (Ledger::TABS as $key => $label)
                <a class="tab {{ $tab === $key ? 'on' : '' }}" href="{{ route('admin.transactions', ['tab' => $key]) }}">{{ $label }}</a>
            @endforeach
        </div>

        <form method="GET" action="{{ route('admin.transactions') }}" class="filters">
            <input type="hidden" name="tab" value="{{ $tab }}">
            <div><label>From date</label><input type="date" name="from" value="{{ $filters['from'] }}"></div>
            <div><label>To date</label><input type="date" name="to" value="{{ $filters['to'] }}"></div>
            <div><label>Member ID / name</label><input type="text" name="member" value="{{ $filters['member'] }}" placeholder="e.g. HGNL1005"></div>
            <div><label>Min amount (₹)</label><input type="number" step="0.01" name="min" value="{{ $filters['min'] }}"></div>
            <div><label>Max amount (₹)</label><input type="number" step="0.01" name="max" value="{{ $filters['max'] }}"></div>

            @if ($tab === 'wallet')
                <div>
                    <label>Direction</label>
                    <select name="direction">
                        <option value="">Credit + Debit</option>
                        <option value="credit" @selected($filters['direction'] === 'credit')>Credit only</option>
                        <option value="debit" @selected($filters['direction'] === 'debit')>Debit only</option>
                    </select>
                </div>
                <div><label>Remarks contain</label><input type="text" name="q" value="{{ $filters['q'] }}" placeholder="e.g. EMI, HGNL1003"></div>
                <details class="types" {{ $filters['types'] ? 'open' : '' }}>
                    <summary>Transaction types {{ $filters['types'] ? '(' . count($filters['types']) . ' selected)' : '(all)' }}</summary>
                    <div class="type-grid">
                        @foreach ($typeOptions as $key => $label)
                            <label><input type="checkbox" name="types[]" value="{{ $key }}" @checked(in_array($key, $filters['types'], true))> {{ $label }}</label>
                        @endforeach
                    </div>
                </details>
            @else
                <div>
                    <label>Status</label>
                    <select name="status">
                        <option value="">All</option>
                        @foreach ($statusOptions[$tab] as $s)
                            <option value="{{ $s }}" @selected($filters['status'] === $s)>{{ ucfirst($s) }}</option>
                        @endforeach
                    </select>
                </div>
                @if ($tab !== 'withdrawals')
                    <div><label>Search</label><input type="text" name="q" value="{{ $filters['q'] }}" placeholder="{{ $tab === 'topups' ? 'Package or payment by' : 'Remark or mode' }}"></div>
                @endif
            @endif

            <div class="actions">
                <button class="btn btn-primary" type="submit">Filter</button>
                <a class="btn btn-dark" href="{{ route('admin.transactions', ['tab' => $tab]) }}">Reset</a>
                <button class="btn btn-primary" type="submit" formaction="{{ route('admin.transactions.export') }}">⬇ Export CSV</button>
            </div>
        </form>

        <div class="stats">
            @foreach ($summary as $label => $value)
                <div class="stat"><p>{{ $label }}</p><h3>{{ $value }}</h3></div>
            @endforeach
        </div>

        @if ($tab === 'wallet' && $breakdown->isNotEmpty())
            <details>
                <summary class="muted" style="cursor:pointer;">Breakdown by type for these filters</summary>
                <div class="table-wrap">
                    <table>
                        <thead><tr><th>Type</th><th>Direction</th><th class="num">Entries</th><th class="num">Total</th></tr></thead>
                        <tbody>
                            @foreach ($breakdown as $b)
                                <tr>
                                    <td>{{ Ledger::typeLabel($b->bt === '__none' ? null : $b->bt) }}</td>
                                    <td><span class="pill {{ $b->dir }}">{{ ucfirst($b->dir) }}</span></td>
                                    <td class="num">{{ number_format($b->n) }}</td>
                                    <td class="num">₹{{ number_format($b->total, 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </details>
        @endif
    </div>

    <div class="card">
        <div class="table-wrap">
            <table>
                @if ($tab === 'wallet')
                    <thead><tr><th>Txn</th><th>Date &amp; time</th><th>Member</th><th>Type</th><th>Direction</th><th class="num">Amount</th><th>Remarks</th><th>Action</th></tr></thead>
                    <tbody>
                        @forelse ($rows as $r)
                            <tr>
                                <td class="nowrap">#{{ $r->id }}</td>
                                <td class="nowrap">{{ $r->created_at ? date('d M Y, h:i A', strtotime($r->created_at)) : '—' }}</td>
                                <td class="nowrap">{{ $r->member_id ?? '#' . $r->user_id }}<small>{{ $r->name }}</small></td>
                                <td><span class="pill type">{{ Ledger::typeLabel($r->bonus_type) }}</span></td>
                                <td><span class="pill {{ $r->direction }}">{{ ucfirst($r->direction) }}</span></td>
                                <td class="num">{{ $r->direction === 'debit' ? '−' : '+' }}₹{{ number_format($r->amount, 2) }}</td>
                                <td class="remarks">{{ $r->remarks ?: '—' }}</td>
                                <td class="act"><a class="link" href="{{ route('admin.transactions.show', $r->id) }}">Details →</a></td>
                            </tr>
                        @empty
                            <tr><td colspan="8">No transactions match these filters.</td></tr>
                        @endforelse
                    </tbody>
                @elseif ($tab === 'topups')
                    <thead><tr><th>Order</th><th>Date &amp; time</th><th>For member</th><th>Paid by</th><th>Package</th><th class="num">Amount</th><th>Payment by</th><th>Status</th></tr></thead>
                    <tbody>
                        @forelse ($rows as $r)
                            <tr>
                                <td class="nowrap">#{{ $r->id }}</td>
                                <td class="nowrap">{{ $r->created_at ? date('d M Y, h:i A', strtotime($r->created_at)) : '—' }}</td>
                                <td class="nowrap">{{ $r->member_id ?? '—' }}<small>{{ $r->name }}</small></td>
                                <td class="nowrap">{{ $r->by_member_id ?? '—' }}<small>{{ $r->by_name }}</small></td>
                                <td>{{ $r->package_name ?? '—' }}</td>
                                <td class="num">₹{{ number_format($r->amount, 2) }}</td>
                                <td>{{ $r->payment_by }}</td>
                                <td><span class="pill {{ strtolower($r->status) }}">{{ ucfirst($r->status) }}</span></td>
                            </tr>
                        @empty
                            <tr><td colspan="8">No top-ups match these filters.</td></tr>
                        @endforelse
                    </tbody>
                @elseif ($tab === 'withdrawals')
                    <thead><tr><th>Request</th><th>Requested</th><th>Member</th><th class="num">Amount</th><th class="num">Tax</th><th class="num">Net paid</th><th>Status</th><th>Last updated</th><th>Bank</th></tr></thead>
                    <tbody>
                        @forelse ($rows as $r)
                            <tr>
                                <td class="nowrap">#{{ $r->id }}</td>
                                <td class="nowrap">{{ $r->created_at ? date('d M Y, h:i A', strtotime($r->created_at)) : '—' }}</td>
                                <td class="nowrap">{{ $r->member_id ?? '—' }}<small>{{ $r->name }}</small></td>
                                <td class="num">₹{{ number_format($r->amount, 2) }}</td>
                                <td class="num">₹{{ number_format($r->tax_amount ?? 0, 2) }}</td>
                                <td class="num">₹{{ number_format($r->net_amount ?? 0, 2) }}</td>
                                <td><span class="pill {{ strtolower($r->status) }}">{{ ucfirst($r->status) }}</span></td>
                                <td class="nowrap">{{ $r->updated_at ? date('d M Y, h:i A', strtotime($r->updated_at)) : '—' }}</td>
                                <td class="nowrap">{{ $r->bank_name ?? '—' }}<small>{{ $r->account_number }} {{ $r->ifsc_code }}</small></td>
                            </tr>
                        @empty
                            <tr><td colspan="9">No withdrawals match these filters.</td></tr>
                        @endforelse
                    </tbody>
                @else
                    <thead><tr><th>Request</th><th>Requested</th><th>Member</th><th class="num">Amount</th><th>Deposit date</th><th>Mode</th><th>Bank / account</th><th>Remark</th><th>Proof</th><th>Status</th></tr></thead>
                    <tbody>
                        @forelse ($rows as $r)
                            <tr>
                                <td class="nowrap">#{{ $r->id }}</td>
                                <td class="nowrap">{{ $r->created_at ? date('d M Y, h:i A', strtotime($r->created_at)) : '—' }}</td>
                                <td class="nowrap">{{ $r->member_id ?? '—' }}<small>{{ $r->name }}</small></td>
                                <td class="num">₹{{ number_format($r->amount, 2) }}</td>
                                <td class="nowrap">{{ $r->deposit_date }}</td>
                                <td>{{ $r->payment_mode }}</td>
                                <td class="nowrap">{{ $r->bank_name ?? '—' }}<small>{{ $r->account_number }}</small></td>
                                <td class="remarks">{{ $r->transaction_remark ?: '—' }}</td>
                                <td>@if ($r->attachment)<a class="link" href="{{ asset($r->attachment) }}" target="_blank" rel="noopener">View</a>@else — @endif</td>
                                <td><span class="pill {{ strtolower($r->status) }}">{{ ucfirst($r->status) }}</span></td>
                            </tr>
                        @empty
                            <tr><td colspan="10">No fund requests match these filters.</td></tr>
                        @endforelse
                    </tbody>
                @endif
            </table>
        </div>
        {{ $rows->links() }}
    </div>
</div>
@endsection
