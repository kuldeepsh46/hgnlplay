@php
    $profileOpen = request()->routeIs('profile', 'profile.edit', 'profile.kyc');
    $teamOpen = request()->routeIs('tree', 'team.list', 'team.direct', 'team.total', 'team.level');
    $settingsOpen = request()->is('admin/settings/qr*');
    $current = fn (bool $active) => $active ? 'aria-current=page' : '';
    $homeUrl = Auth::user()->hasRole('admin') && !Auth::user()->hasRole('customer')
        ? route('admin.dashboard.new')
        : url('/dashboard');
@endphp

<!-- Mobile top bar -->
<div class="mobile-header">
    <a class="logo" href="{{ $homeUrl }}">
        <img src="{{ asset('assets/images/logo.jpeg') }}" alt="Himalaya Pay – dashboard">
    </a>
    <button type="button" class="toggle-btn" id="toggleBtn" aria-controls="sidebar" aria-expanded="false"
        aria-label="Open navigation menu">
        <span aria-hidden="true">☰</span>
    </button>
</div>

<aside class="sidebar" id="sidebar">
    <div class="logo">
        <img src="{{ asset('assets/images/logo.jpeg') }}" alt="Himalaya Pay">
    </div>

    <nav class="sidebar-nav" aria-label="Main navigation">
        <ul>
            @if (Auth::user()->hasRole('customer'))
                <li class="{{ Request::is('dashboard') ? 'active' : '' }}">
                    <a href="{{ url('/dashboard') }}" {{ $current(Request::is('dashboard')) }}>
                        <span class="nav-icon" aria-hidden="true">🏠</span> <span>Dashboard</span>
                    </a>
                </li>

                <li class="team-item {{ $profileOpen ? 'active open' : '' }}">
                    <button type="button" class="team-menu" aria-expanded="{{ $profileOpen ? 'true' : 'false' }}"
                        aria-controls="submenu-profile">
                        <span class="team-title"><span class="nav-icon" aria-hidden="true">👤</span> <span>Profile</span></span>
                        <span class="arrow" aria-hidden="true">▾</span>
                    </button>
                    <ul class="submenu" id="submenu-profile">
                        <li><a href="{{ route('profile') }}" class="{{ request()->routeIs('profile') ? 'active' : '' }}" {{ $current(request()->routeIs('profile')) }}>View Profile</a></li>
                        <li><a href="{{ route('profile.edit') }}" class="{{ request()->routeIs('profile.edit') ? 'active' : '' }}" {{ $current(request()->routeIs('profile.edit')) }}>Update Profile</a></li>
                        <li><a href="{{ route('profile.kyc') }}" class="{{ request()->routeIs('profile.kyc') ? 'active' : '' }}" {{ $current(request()->routeIs('profile.kyc')) }}>Upload KYC</a></li>
                    </ul>
                </li>

                <li class="{{ Request::routeIs('member.register') ? 'active' : '' }}">
                    <a href="{{ route('member.register') }}" {{ $current(Request::routeIs('member.register')) }}>
                        <span class="nav-icon" aria-hidden="true">📝</span> <span>Registration</span>
                    </a>
                </li>

                <li class="team-item {{ $teamOpen ? 'active open' : '' }}">
                    <button type="button" class="team-menu" aria-expanded="{{ $teamOpen ? 'true' : 'false' }}"
                        aria-controls="submenu-team">
                        <span class="team-title"><span class="nav-icon" aria-hidden="true">👥</span> <span>Team Detail</span></span>
                        <span class="arrow" aria-hidden="true">▾</span>
                    </button>
                    <ul class="submenu" id="submenu-team">
                        <li><a href="{{ route('tree') }}" class="{{ request()->routeIs('tree') ? 'active' : '' }}" {{ $current(request()->routeIs('tree')) }}>Tree View</a></li>
                        <li><a href="{{ route('team.list') }}" class="{{ request()->routeIs('team.list') ? 'active' : '' }}" {{ $current(request()->routeIs('team.list')) }}>List View</a></li>
                        <li><a href="{{ route('team.direct') }}" class="{{ request()->routeIs('team.direct') ? 'active' : '' }}" {{ $current(request()->routeIs('team.direct')) }}>Direct Referral</a></li>
                        <li><a href="{{ route('team.total') }}" class="{{ request()->routeIs('team.total') ? 'active' : '' }}" {{ $current(request()->routeIs('team.total')) }}>Total Downline</a></li>
                        <li><a href="{{ route('team.level') }}" class="{{ request()->routeIs('team.level') ? 'active' : '' }}" {{ $current(request()->routeIs('team.level')) }}>Total Level Downline</a></li>
                    </ul>
                </li>

                <li class="{{ Request::routeIs('wallet.fund') ? 'active' : '' }}">
                    <a href="{{ route('wallet.fund') }}" {{ $current(Request::routeIs('wallet.fund')) }}>
                        <span class="nav-icon" aria-hidden="true">💰</span> <span>Wallet Fund Request</span>
                    </a>
                </li>

                <li class="{{ Request::routeIs('member.topup') ? 'active' : '' }}">
                    <a href="{{ route('member.topup') }}" {{ $current(Request::routeIs('member.topup')) }}>
                        <span class="nav-icon" aria-hidden="true">🔄</span> <span>Member Topup</span>
                    </a>
                </li>

                <li class="{{ Request::routeIs('repurchase.wallet') ? 'active' : '' }}">
                    <a href="{{ route('repurchase.wallet') }}" {{ $current(Request::routeIs('repurchase.wallet')) }}>
                        <span class="nav-icon" aria-hidden="true">🛍️</span> <span>Repurchase Wallet</span>
                    </a>
                </li>

                <li class="{{ Request::routeIs('payments.due') ? 'active' : '' }}">
                    <a href="{{ route('payments.due') }}" {{ $current(Request::routeIs('payments.due')) }}>
                        <span class="nav-icon" aria-hidden="true">🧾</span> <span>Payment Dues</span>
                        @if (!empty($paymentDue['due_count']))
                            <span class="nav-badge">{{ $paymentDue['due_count'] }}<span class="sr-only"> pending</span></span>
                        @endif
                    </a>
                </li>

                <li class="{{ Request::routeIs('withdraw.index') ? 'active' : '' }}">
                    <a href="{{ route('withdraw.index') }}" {{ $current(Request::routeIs('withdraw.index')) }}>
                        <span class="nav-icon" aria-hidden="true">💸</span> <span>Withdraw</span>
                    </a>
                </li>

                <li class="{{ Request::routeIs('reports.index') ? 'active' : '' }}">
                    <a href="{{ route('reports.index') }}" {{ $current(Request::routeIs('reports.index')) }}>
                        <span class="nav-icon" aria-hidden="true">📊</span> <span>Report</span>
                    </a>
                </li>

                <li class="{{ Request::routeIs('mailbox') ? 'active' : '' }}">
                    <a href="{{ route('mailbox') }}" {{ $current(Request::routeIs('mailbox')) }}>
                        <span class="nav-icon" aria-hidden="true">📬</span> <span>Mail Box</span>
                    </a>
                </li>
            @endif

            @if (Auth::user()->hasRole('superadmin'))
                <li class="{{ Request::routeIs('account.sync*') ? 'active' : '' }}">
                    <a href="{{ route('account.sync') }}" {{ $current(Request::routeIs('account.sync*')) }}>
                        <span class="nav-icon" aria-hidden="true">⚙️</span> <span>Sync Settings</span>
                    </a>
                </li>
            @endif

            @if (Auth::user()->hasRole('admin'))
                @php
                    $adminLinks = [
                        ['url' => route('admin.dashboard.new'), 'active' => Request::routeIs('admin.dashboard.new'), 'icon' => '🧭', 'label' => 'Admin Console'],
                        ['url' => route('admin.users'), 'active' => Request::routeIs('admin.users'), 'icon' => '👥', 'label' => 'Manage Users'],
                        ['url' => route('packages.index'), 'active' => Request::routeIs('packages.*'), 'icon' => '📦', 'label' => 'Manage Packages'],
                        ['url' => route('admin.payments'), 'active' => Request::routeIs('admin.payments'), 'icon' => '💳', 'label' => 'Manage Payments'],
                        ['url' => route('admin.payouts'), 'active' => Request::routeIs('admin.payouts'), 'icon' => '🏦', 'label' => 'Manage Payouts'],
                        ['url' => route('admin.sponsor-bonus'), 'active' => Request::routeIs('admin.sponsor-bonus*'), 'icon' => '🤝', 'label' => 'Sponsor Bonus'],
                        ['url' => route('admin.transactions'), 'active' => Request::routeIs('admin.transactions*'), 'icon' => '🧾', 'label' => 'All Transactions'],
                        ['url' => route('admin.rank-rewards'), 'active' => Request::routeIs('admin.rank-rewards*'), 'icon' => '🏅', 'label' => 'Rank Rewards'],
                        ['url' => route('admin.support'), 'active' => Request::routeIs('admin.support'), 'icon' => '🆘', 'label' => 'Support Request'],
                        ['url' => route('admin.lucky.index'), 'active' => Request::routeIs('admin.lucky.index'), 'icon' => '🎁', 'label' => 'Lucky Draw'],
                    ];
                @endphp

                @foreach ($adminLinks as $link)
                    <li class="{{ $link['active'] ? 'active' : '' }}">
                        <a href="{{ $link['url'] }}" {{ $current($link['active']) }}>
                            <span class="nav-icon" aria-hidden="true">{{ $link['icon'] }}</span> <span>{{ $link['label'] }}</span>
                        </a>
                    </li>
                @endforeach

                <li class="team-item {{ $settingsOpen ? 'active open' : '' }}">
                    <button type="button" class="team-menu" aria-expanded="{{ $settingsOpen ? 'true' : 'false' }}"
                        aria-controls="submenu-settings">
                        <span class="team-title"><span class="nav-icon" aria-hidden="true">⚙️</span> <span>Settings</span></span>
                        <span class="arrow" aria-hidden="true">▾</span>
                    </button>
                    <ul class="submenu" id="submenu-settings">
                        @foreach ([1 => 'QR Code', 2 => 'USDT'] as $qrId => $qrLabel)
                            @php $qrActive = request()->fullUrl() == route('admin.settings.viewQR', $qrId); @endphp
                            <li>
                                <a href="{{ route('admin.settings.viewQR', $qrId) }}" class="{{ $qrActive ? 'active' : '' }}" {{ $current($qrActive) }}>{{ $qrLabel }}</a>
                            </li>
                        @endforeach
                    </ul>
                </li>
            @endif

            <li class="logout-item">
                <form id="logout-form" action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit">
                        <span class="nav-icon" aria-hidden="true">🚪</span> <span>Logout</span>
                    </button>
                </form>
            </li>
        </ul>
    </nav>
</aside>
