{{-- Payment due / overdue alert shown at the top of every member page --}}
@php
    $due = $paymentDue ?? null;
@endphp

@if ($due && $due['state'] !== 'clear')
    @php
        $isOverdue = $due['state'] === 'overdue';
        $isUpcoming = $due['state'] === 'upcoming';
        $next = $due['next'];
        $shortfall = max(0, $due['pay_now_amount'] - $due['wallet_balance']);
        $dismissKey = 'pay-alert-' . $next['number'] . '-' . $next['due_date']->format('Ymd');
    @endphp

    <section class="pay-alert pay-alert--{{ $due['state'] }}" role="{{ $isOverdue ? 'alert' : 'status' }}"
        aria-labelledby="pay-alert-title" data-dismiss-key="{{ $isUpcoming ? $dismissKey : '' }}">
        <div class="pay-alert__icon" aria-hidden="true">{{ $isOverdue ? '⚠️' : ($isUpcoming ? '⏰' : '💳') }}</div>

        <div class="pay-alert__body">
            <h2 id="pay-alert-title" class="pay-alert__title">
                @if ($isOverdue)
                    Payment overdue — {{ $due['due_count'] }} EMI{{ $due['due_count'] > 1 ? 's' : '' }} pending
                @elseif ($isUpcoming)
                    Upcoming EMI due {{ $due['days_until_next'] === 0 ? 'today' : 'in ' . $due['days_until_next'] . ' day' . ($due['days_until_next'] > 1 ? 's' : '') }}
                @else
                    EMI payment due
                @endif
            </h2>

            <dl class="pay-alert__facts">
                <div>
                    <dt>Amount {{ $isUpcoming ? '' : 'due' }}</dt>
                    <dd>₹{{ number_format($isUpcoming ? $due['amount_per_emi'] : $due['total_due']) }}</dd>
                </div>
                <div>
                    <dt>{{ $isOverdue ? 'Overdue since' : 'Due date' }}</dt>
                    <dd>{{ ($isOverdue ? $due['oldest_overdue_date'] : $next['due_date'])->format('d M Y') }}</dd>
                </div>
                <div>
                    <dt>EMI</dt>
                    <dd>#{{ $next['number'] }} of {{ $due['total_emis'] }}</dd>
                </div>
                <div>
                    <dt>Wallet balance</dt>
                    <dd>₹{{ number_format($due['wallet_balance'], 2) }}</dd>
                </div>
            </dl>

            @if ($isOverdue)
                <p class="pay-alert__note">Members with pending EMIs don't receive matrix income until they're up to date.</p>
            @endif
        </div>

        <div class="pay-alert__actions">
            <a href="{{ route('payments.due') }}" class="pay-alert__btn pay-alert__btn--primary">
                {{ $isUpcoming ? 'View & pay' : 'Pay now' }}
            </a>
            @if ($shortfall > 0)
                <a href="{{ route('wallet.fund', ['amount' => $shortfall]) }}" class="pay-alert__btn">
                    Add ₹{{ number_format($shortfall) }} to wallet
                </a>
            @endif
            @if ($isUpcoming)
                <button type="button" class="pay-alert__close" aria-label="Dismiss reminder">✕</button>
            @endif
        </div>
    </section>

    <script>
        (function () {
            var el = document.currentScript.previousElementSibling;
            var key = el.getAttribute('data-dismiss-key');
            if (!key) return;
            try { if (sessionStorage.getItem(key)) { el.remove(); return; } } catch (e) {}
            el.querySelector('.pay-alert__close').addEventListener('click', function () {
                try { sessionStorage.setItem(key, '1'); } catch (e) {}
                el.remove();
            });
        })();
    </script>
@endif
