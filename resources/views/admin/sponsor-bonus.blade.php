@extends('common.layout')
@section('title', 'Sponsor Binary Bonus')
@section('main')
<style>
.sbb .card {
    background: var(--card);
    border: 1px solid #1f2832;
    border-radius: var(--radius);
    padding: 20px;
    margin-bottom: 24px;
}
.sbb .filter-form { display: flex; gap: 10px; flex-wrap: wrap; align-items: flex-end; margin-bottom: 10px; }
.sbb .filter-form label { display: block; font-size: 12px; margin-bottom: 5px; color: #a9b9c7; }
.sbb .filter-form input {
    padding: 8px; border-radius: 6px; background: #141c22; border: 1px solid #1f2832; color: #fff;
}
.sbb .btn { padding: 9px 16px; border: none; border-radius: 8px; cursor: pointer; font-weight: 600; text-decoration: none; display: inline-block; }
.sbb .btn-primary { background: var(--accent); color: #fff; }
.sbb .btn-dark { background: #333; color: #fff; }
.sbb .stats { display: grid; grid-template-columns: repeat(auto-fit, minmax(160px, 1fr)); gap: 12px; margin-bottom: 16px; }
.sbb .stat { background: #141c22; border: 1px solid #1f2832; border-radius: 10px; padding: 14px; }
.sbb .stat p { margin: 0; font-size: 12px; color: #a9b9c7; }
.sbb .stat h3 { margin: 6px 0 0; font-size: 20px; }
.sbb .table-wrap { overflow-x: auto; }
.sbb table { width: 100%; border-collapse: collapse; margin-top: 10px; }
.sbb th, .sbb td { border: 1px solid #1e2b36; padding: 10px; text-align: center; font-size: 14px; white-space: nowrap; }
.sbb th { background: #161f29; color: #a9b9c7; }
.sbb td { color: #d4dee8; }
.sbb td small { display: block; color: #8899a8; }
.sbb .muted { color: #8899a8; font-size: 13px; margin: 0 0 12px; }
</style>

<div class="sbb">
    <div class="header">
        <h1>Sponsor Binary Bonus</h1>
        <div class="user-info">👤 {{ Auth::user()->username ?? Auth::user()->name }}</div>
    </div>

    <div class="card">
        <p class="muted">Every 10% bonus paid to a sponsor: whose pair income it came from, for which day, and when it was credited.</p>

        <form method="GET" action="{{ route('admin.sponsor-bonus') }}" class="filter-form">
            <div>
                <label>Income date from</label>
                <input type="date" name="from" value="{{ $filters['from'] }}">
            </div>
            <div>
                <label>Income date to</label>
                <input type="date" name="to" value="{{ $filters['to'] }}">
            </div>
            <div>
                <label>Member (sponsor or downline)</label>
                <input type="text" name="member" value="{{ $filters['member'] }}" placeholder="Member ID or name">
            </div>
            <button class="btn btn-primary" type="submit">Filter</button>
            <a href="{{ route('admin.sponsor-bonus') }}" class="btn btn-dark">Reset</a>
            <button class="btn btn-primary" type="submit" formaction="{{ route('admin.sponsor-bonus.export') }}">⬇ Export CSV</button>
        </form>

        <div class="stats">
            <div class="stat"><p>Total bonus paid</p><h3>₹{{ number_format($summary->total_bonus ?? 0, 2) }}</h3></div>
            <div class="stat"><p>Payouts</p><h3>{{ number_format($summary->payouts ?? 0) }}</h3></div>
            <div class="stat"><p>Sponsors paid</p><h3>{{ number_format($summary->sponsors ?? 0) }}</h3></div>
            <div class="stat"><p>Downlines counted</p><h3>{{ number_format($summary->downlines ?? 0) }}</h3></div>
            <div class="stat"><p>Pair income behind it</p><h3>₹{{ number_format($summary->total_income ?? 0, 2) }}</h3></div>
        </div>

        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Income Date</th>
                        <th>Paid To (Sponsor)</th>
                        <th>From (Downline)</th>
                        <th>Pair Income</th>
                        <th>Counted (max ₹5,000)</th>
                        <th>Bonus (10%)</th>
                        <th>Credited On</th>
                        <th>Txn ID</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($rows as $row)
                        <tr>
                            <td>{{ ($rows->currentPage() - 1) * $rows->perPage() + $loop->iteration }}</td>
                            <td>{{ \Carbon\Carbon::parse($row->income_date)->format('d M Y') }}</td>
                            <td>{{ $row->sponsor_member_id ?? '—' }}<small>{{ $row->sponsor_name }}</small></td>
                            <td>{{ $row->source_member_id ?? '—' }}<small>{{ $row->source_name }}</small></td>
                            <td>₹{{ number_format($row->binary_income, 2) }}</td>
                            <td>₹{{ number_format($row->capped_income, 2) }}</td>
                            <td><strong>₹{{ number_format($row->bonus_amount, 2) }}</strong></td>
                            <td>{{ $row->credited_at ? \Carbon\Carbon::parse($row->credited_at)->format('d M Y, h:i A') : '—' }}</td>
                            <td>{{ $row->transaction_id ?? '—' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9">No sponsor bonus payouts found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        {{ $rows->links() }}
    </div>
</div>
@endsection
