@extends('common.layout')

@section('title', 'Payment Dues')

@section('main')
    <div class="header">
        <h1>Payment Dues</h1>
        <div class="user-info">👤 {{ $user->name }}</div>
    </div>

    @if (session('success'))
        <div class="pd-flash pd-flash--ok" role="status">{{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div class="pd-flash pd-flash--err" role="alert">{{ session('error') }}</div>
    @endif
    @if ($errors->any())
        <div class="pd-flash pd-flash--err" role="alert">{{ $errors->first() }}</div>
    @endif

    @if (!$due)
        <section class="pd-card pd-empty">
            <div class="pd-empty__icon" aria-hidden="true">✅</div>
            <h2>No payments due</h2>
            <p>
                @if ($user->emi_status === 'completed')
                    You've completed all your EMIs. Thank you!
                @else
                    You don't have any EMIs scheduled yet. They start after your first package activation.
                @endif
            </p>
        </section>
    @else
        @php
            $next = $due['next'];
            $canPayFromWallet = $due['wallet_balance'] >= $due['amount_per_emi'];
            $shortfall = max(0, $due['pay_now_amount'] - $due['wallet_balance']);
            $statusLabels = [
                'paid' => 'Paid',
                'overdue' => 'Overdue',
                'due' => 'Due',
                'upcoming' => 'Upcoming',
            ];
        @endphp

        {{-- Summary --}}
        <section class="pd-summary" aria-label="Payment summary">
            <div class="pd-stat pd-stat--{{ $due['due_count'] > 0 ? 'alert' : 'ok' }}">
                <span class="pd-stat__label">Amount due now</span>
                <strong class="pd-stat__value">₹{{ number_format($due['total_due']) }}</strong>
                <span class="pd-stat__hint">{{ $due['due_count'] }} EMI{{ $due['due_count'] === 1 ? '' : 's' }} pending</span>
            </div>
            <div class="pd-stat">
                <span class="pd-stat__label">Next EMI</span>
                <strong class="pd-stat__value">#{{ $next['number'] }} · ₹{{ number_format($next['amount']) }}</strong>
                <span class="pd-stat__hint">
                    Due {{ $next['due_date']->format('d M Y') }}
                    @if ($due['days_until_next'] < 0)
                        ({{ abs($due['days_until_next']) }} day{{ abs($due['days_until_next']) === 1 ? '' : 's' }} late)
                    @elseif ($due['days_until_next'] === 0)
                        (today)
                    @else
                        (in {{ $due['days_until_next'] }} day{{ $due['days_until_next'] === 1 ? '' : 's' }})
                    @endif
                </span>
            </div>
            <div class="pd-stat">
                <span class="pd-stat__label">EMIs paid</span>
                <strong class="pd-stat__value">{{ $due['paid'] }} / {{ $due['total_emis'] }}</strong>
                <div class="pd-progress" role="progressbar" aria-valuemin="0" aria-valuemax="{{ $due['total_emis'] }}"
                    aria-valuenow="{{ $due['paid'] }}" aria-label="EMIs paid">
                    <span style="width: {{ round($due['paid'] / $due['total_emis'] * 100) }}%"></span>
                </div>
            </div>
            <div class="pd-stat">
                <span class="pd-stat__label">Wallet balance</span>
                <strong class="pd-stat__value">₹{{ number_format($due['wallet_balance'], 2) }}</strong>
                <span class="pd-stat__hint">Activated {{ $due['activation_date']->format('d M Y') }}</span>
            </div>
        </section>

        {{-- Payment options --}}
        <section class="pd-card" aria-labelledby="pay-options-title">
            <h2 id="pay-options-title">Make a payment</h2>

            <div class="pd-options">
                <div class="pd-option">
                    <h3><span aria-hidden="true">👛</span> Option 1 — Pay from wallet</h3>
                    <p>Pays EMI #{{ $next['number'] }} instantly using your wallet balance.
                        @if ($due['due_count'] > 1)
                            Pay once per EMI — you have {{ $due['due_count'] }} pending.
                        @endif
                    </p>

                    @if ($due['starter_package_id'])
                        <form method="POST" action="{{ route('member.topup.store') }}" class="pd-pay-form"
                            data-confirm="Pay ₹{{ number_format($due['amount_per_emi']) }} from your wallet for EMI #{{ $next['number'] }}?">
                            @csrf
                            <input type="hidden" name="member_id" value="{{ $user->member_id }}">
                            <input type="hidden" name="package_id" value="{{ $due['starter_package_id'] }}">
                            <input type="hidden" name="payment_by" value="Wallet">
                            <button type="submit" class="pd-btn pd-btn--primary" @disabled(!$canPayFromWallet)>
                                Pay EMI #{{ $next['number'] }} — ₹{{ number_format($due['amount_per_emi']) }}
                            </button>
                        </form>
                        @unless ($canPayFromWallet)
                            <p class="pd-muted">Your wallet balance is too low. Add money first (Option 2).</p>
                        @endunless
                    @else
                        <a href="{{ route('member.topup') }}" class="pd-btn pd-btn--primary">Go to Member Topup</a>
                    @endif
                </div>

                <div class="pd-option">
                    <h3><span aria-hidden="true">🏦</span> Option 2 — Add money (UPI / USDT)</h3>
                    <ol class="pd-steps">
                        <li>Scan a QR code below and pay
                            <strong>₹{{ number_format($shortfall > 0 ? $shortfall : $due['pay_now_amount']) }}</strong>.</li>
                        <li>Submit a wallet fund request with the UTR / transaction ID and screenshot.</li>
                        <li>Once approved, come back here and pay from your wallet.</li>
                    </ol>

                    <div class="pd-qrs">
                        <figure>
                            <img src="{{ asset($upi_qr ?? 'assets/images/scanner.jpeg') }}" alt="UPI payment QR code" loading="lazy">
                            <figcaption>UPI</figcaption>
                        </figure>
                        @if ($usdt_qr)
                            <figure>
                                <img src="{{ asset($usdt_qr) }}" alt="USDT payment QR code" loading="lazy">
                                <figcaption>USDT</figcaption>
                            </figure>
                        @endif
                    </div>

                    <a href="{{ route('wallet.fund', ['amount' => $shortfall > 0 ? $shortfall : $due['pay_now_amount']]) }}"
                        class="pd-btn">Submit fund request</a>
                </div>
            </div>
        </section>

        {{-- Schedule --}}
        <section class="pd-card" aria-labelledby="schedule-title">
            <h2 id="schedule-title">EMI schedule</h2>
            <div class="table-res">
                <table class="pd-table">
                    <thead>
                        <tr>
                            <th scope="col">EMI</th>
                            <th scope="col">Due date</th>
                            <th scope="col">Amount</th>
                            <th scope="col">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($due['schedule'] as $row)
                            <tr class="{{ $row['number'] === $next['number'] ? 'is-next' : '' }}">
                                <td>#{{ $row['number'] }}</td>
                                <td>{{ $row['due_date']->format('d M Y') }}</td>
                                <td>₹{{ number_format($row['amount']) }}</td>
                                <td><span class="pd-badge pd-badge--{{ $row['status'] }}">{{ $statusLabels[$row['status']] }}</span></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </section>
    @endif

    <style>
        .pd-flash { padding: 12px 16px; border-radius: 10px; margin-bottom: 16px; font-weight: 600; }
        .pd-flash--ok { background: #dff3e7; color: #0b5a3c; border: 1px solid #a9d8bd; }
        .pd-flash--err { background: #fbe9ed; color: #8f1f37; border: 1px solid #efb9c5; }

        .pd-card {
            background: var(--card, #fff);
            border: 1px solid var(--border, #cddfd3);
            border-radius: 14px;
            padding: 22px;
            margin-bottom: 20px;
            box-shadow: 0 8px 24px rgba(13, 77, 51, .06);
        }
        .pd-card h2 { font-size: 18px; margin: 0 0 16px; }

        .pd-empty { text-align: center; padding: 40px 22px; }
        .pd-empty__icon { font-size: 40px; }
        .pd-empty p { color: var(--muted); margin: 0; }

        .pd-summary {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 14px;
            margin-bottom: 20px;
        }
        .pd-stat {
            background: var(--card, #fff);
            border: 1px solid var(--border, #cddfd3);
            border-radius: 14px;
            padding: 16px 18px;
            display: flex;
            flex-direction: column;
            gap: 4px;
        }
        .pd-stat--alert { border-color: #e6a2b1; background: #fff6f8; }
        .pd-stat--ok { border-color: #a9d8bd; background: #f3fbf6; }
        .pd-stat__label { font-size: 12px; font-weight: 700; text-transform: uppercase; letter-spacing: .6px; color: var(--muted); }
        .pd-stat__value { font-size: 22px; color: var(--text); }
        .pd-stat--alert .pd-stat__value { color: #a3243f; }
        .pd-stat__hint { font-size: 13px; color: var(--muted); }

        .pd-progress { height: 8px; border-radius: 99px; background: #dcebe2; overflow: hidden; margin-top: 6px; }
        .pd-progress span { display: block; height: 100%; background: #08734f; border-radius: inherit; }

        .pd-options { display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 18px; }
        .pd-option { border: 1px solid var(--border, #cddfd3); border-radius: 12px; padding: 18px; }
        .pd-option h3 { font-size: 16px; margin: 0 0 8px; }
        .pd-option p { margin: 0 0 14px; color: var(--muted); font-size: 14px; }
        .pd-muted { margin-top: 10px !important; font-size: 13px !important; color: #8f1f37 !important; }
        .pd-steps { margin: 0 0 14px; padding-left: 20px; font-size: 14px; color: var(--muted); }
        .pd-steps li { margin-bottom: 4px; }
        .pd-qrs { display: flex; gap: 14px; flex-wrap: wrap; margin-bottom: 14px; }
        .pd-qrs figure { margin: 0; text-align: center; }
        .pd-qrs img { width: 140px; height: 140px; object-fit: contain; background: #fff; border: 1px solid var(--border); border-radius: 10px; padding: 6px; }
        .pd-qrs figcaption { font-size: 12px; font-weight: 700; color: var(--muted); margin-top: 4px; }

        .pd-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-height: 44px;
            padding: 10px 18px;
            border-radius: 10px;
            border: 2px solid #08734f;
            background: #fff;
            color: #066344;
            font-weight: 700;
            font-size: 15px;
            text-decoration: none;
            cursor: pointer;
        }
        .pd-btn:hover { background: #e3f3e9; }
        .pd-btn--primary { background: #08734f; color: #fff; }
        .pd-btn--primary:hover { background: #056344; }
        .pd-btn:disabled { opacity: .5; cursor: not-allowed; }

        .pd-table { width: 100%; border-collapse: collapse; }
        .pd-table th, .pd-table td { padding: 10px 12px; text-align: left; border-bottom: 1px solid var(--border, #cddfd3); }
        .pd-table tr.is-next td { background: #f1faf5; font-weight: 600; }

        .pd-badge { display: inline-block; padding: 3px 10px; border-radius: 99px; font-size: 12px; font-weight: 700; }
        .pd-badge--paid { background: #dff3e7; color: #0b5a3c; }
        .pd-badge--overdue { background: #fbe9ed; color: #8f1f37; }
        .pd-badge--due { background: #fff1d6; color: #7a4a05; }
        .pd-badge--upcoming { background: #eef1f4; color: #4a5560; }
    </style>

    <script>
        document.querySelectorAll('.pd-pay-form').forEach(function (form) {
            form.addEventListener('submit', function (e) {
                if (!confirm(form.dataset.confirm)) { e.preventDefault(); return; }
                var btn = form.querySelector('button');
                btn.disabled = true;
                btn.textContent = 'Processing…';
            });
        });
    </script>
@endsection
