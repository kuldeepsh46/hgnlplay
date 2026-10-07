@extends('common.layout')

@section('title', 'Repurchase Wallet')

@section('main')
    <div class="header">
        <h1>Repurchase Wallet</h1>
        <div class="user-info">👤 {{ $user->name }}</div>
    </div>

    <section class="rw-summary" aria-label="Repurchase Wallet summary">
        <div class="rw-stat rw-stat--main">
            <span class="rw-stat__label">Available balance</span>
            <strong class="rw-stat__value">₹{{ number_format($balance, 2) }}</strong>
            <span class="rw-stat__hint">Usable only for the Repurchase Package</span>
        </div>
        <div class="rw-stat">
            <span class="rw-stat__label">Total credited</span>
            <strong class="rw-stat__value">₹{{ number_format($credited, 2) }}</strong>
            <span class="rw-stat__hint">{{ $percent }}% of every earning</span>
        </div>
        <div class="rw-stat">
            <span class="rw-stat__label">Total spent</span>
            <strong class="rw-stat__value">₹{{ number_format($spent, 2) }}</strong>
            <span class="rw-stat__hint">On Repurchase Package purchases</span>
        </div>
    </section>

    <section class="rw-card rw-info">
        <div>
            <h2>How it works</h2>
            <ul>
                <li>Every earning (pair, direct, level, rank, sponsor bonus and rewards) is split:
                    <strong>{{ 100 - $percent }}%</strong> to your main wallet and <strong>{{ $percent }}%</strong> here.</li>
                <li>Money added through a wallet fund request goes fully to your main wallet.</li>
                <li>This balance can only buy the
                    @if ($repurchasePackages->isNotEmpty())
                        <strong>{{ $repurchasePackages->pluck('name')->join(', ') }}</strong>
                        (₹{{ number_format($repurchasePackages->first()->actual_amount ?? $repurchasePackages->first()->amount) }}).
                    @else
                        Repurchase Package.
                    @endif
                    It can't be withdrawn or used for anything else.</li>
            </ul>
        </div>
        <a href="{{ route('member.topup') }}" class="rw-btn">Buy Repurchase Package</a>
    </section>

    <section class="rw-card" aria-labelledby="rw-statement-title">
        <div class="rw-card__head">
            <h2 id="rw-statement-title">Statement</h2>
            <nav class="rw-filter" aria-label="Filter statement">
                <a href="{{ route('repurchase.wallet') }}" @if (!$type) aria-current="page" @endif>All</a>
                <a href="{{ route('repurchase.wallet', ['type' => 'credit']) }}" @if ($type === 'credit') aria-current="page" @endif>Credits</a>
                <a href="{{ route('repurchase.wallet', ['type' => 'debit']) }}" @if ($type === 'debit') aria-current="page" @endif>Purchases</a>
            </nav>
        </div>

        <div class="table-res">
            <table class="rw-table">
                <thead>
                    <tr>
                        <th scope="col">Date</th>
                        <th scope="col">Type</th>
                        <th scope="col">Source</th>
                        <th scope="col" class="long-text">Details</th>
                        <th scope="col" class="rw-num">Amount</th>
                        <th scope="col" class="rw-num">Balance</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($entries as $e)
                        <tr>
                            <td>{{ \Carbon\Carbon::parse($e->created_at)->format('d M Y, h:i A') }}</td>
                            <td><span class="rw-badge rw-badge--{{ $e->type }}">{{ $e->type === 'credit' ? 'Credit' : 'Debit' }}</span></td>
                            <td>{{ \Illuminate\Support\Str::headline($e->source) }}</td>
                            <td class="long-text">{{ $e->remarks }}</td>
                            <td class="rw-num rw-amt--{{ $e->type }}">{{ $e->type === 'credit' ? '+' : '−' }}₹{{ number_format($e->amount, 2) }}</td>
                            <td class="rw-num">₹{{ number_format($e->balance_after, 2) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="rw-empty">No Repurchase Wallet transactions yet. {{ $percent }}% of your next earning will appear here.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{ $entries->links() }}
    </section>

    <style>
        .rw-summary { display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 14px; margin-bottom: 20px; }
        .rw-stat {
            background: var(--card, #fff); border: 1px solid var(--border, #cddfd3); border-radius: 14px;
            padding: 16px 18px; display: flex; flex-direction: column; gap: 4px;
        }
        .rw-stat--main { background: linear-gradient(135deg, #e3f3e9, #f3eefb); border-color: #a9d8bd; }
        .rw-stat__label { font-size: 12px; font-weight: 700; text-transform: uppercase; letter-spacing: .6px; color: var(--muted); }
        .rw-stat__value { font-size: 24px; color: var(--text); }
        .rw-stat--main .rw-stat__value { color: #08734f; font-size: 28px; }
        .rw-stat__hint { font-size: 13px; color: var(--muted); }

        .rw-card {
            background: var(--card, #fff); border: 1px solid var(--border, #cddfd3); border-radius: 14px;
            padding: 20px 22px; margin-bottom: 20px; box-shadow: 0 8px 24px rgba(13, 77, 51, .06);
        }
        .rw-card h2 { font-size: 18px; margin: 0 0 10px; }
        .rw-info { display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 16px; }
        .rw-info ul { margin: 0; padding-left: 20px; color: var(--muted); font-size: 14px; }
        .rw-info li { margin-bottom: 4px; }
        .rw-btn {
            display: inline-flex; align-items: center; min-height: 44px; padding: 10px 18px; border-radius: 10px;
            background: #08734f; color: #fff; font-weight: 700; text-decoration: none; white-space: nowrap;
        }
        .rw-btn:hover { background: #056344; }

        .rw-card__head { display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 10px; margin-bottom: 12px; }
        .rw-card__head h2 { margin: 0; }
        .rw-filter { display: flex; gap: 6px; }
        .rw-filter a {
            padding: 6px 14px; border-radius: 99px; border: 1px solid var(--border); color: var(--muted);
            text-decoration: none; font-size: 13px; font-weight: 600;
        }
        .rw-filter a[aria-current="page"] { background: #08734f; border-color: #08734f; color: #fff; }

        .rw-table { width: 100%; border-collapse: collapse; }
        .rw-table th, .rw-table td { padding: 10px 12px; text-align: left; border-bottom: 1px solid var(--border, #cddfd3); }
        .rw-num { text-align: right !important; }
        .rw-amt--credit { color: #08734f; font-weight: 700; }
        .rw-amt--debit { color: #a3243f; font-weight: 700; }
        .rw-badge { display: inline-block; padding: 3px 10px; border-radius: 99px; font-size: 12px; font-weight: 700; }
        .rw-badge--credit { background: #dff3e7; color: #0b5a3c; }
        .rw-badge--debit { background: #fbe9ed; color: #8f1f37; }
        .rw-empty { text-align: center !important; color: var(--muted); padding: 30px 12px !important; white-space: normal; }
    </style>
@endsection
