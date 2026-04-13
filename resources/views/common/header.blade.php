<!-- Sidebar -->
<div class="mobile-header">
    <div class="logo"><img src="{{ asset('assets/images/logo.jpeg') }}" alt="Himalaya Trading">
        <!-- <h1>Himalaya <span class="brand-green">Trading</span></h1> -->
    </div>
    <div class="toggle-btn" id="toggleBtn">☰</div>
</div>
<aside class="sidebar" id="sidebar">
    <div class="logo"><img src="{{ asset('assets/images/logo.jpeg') }}" alt="Himalaya Trading">
        <!-- <h1>Himalaya <span class="brand-green">Trading</span></h1> -->
    </div>
    <div class="toggle-btn" id="toggleBtnd">☰</div>
    <ul>
        @if (Auth::user()->hasRole('customer'))
            <a href="{{ url('/dashboard') }}" style="text-decoration:none;color:inherit;display:block;">
                <li class="{{ Request::is('dashboard') ? 'active' : '' }}">🏠 <span>Dashboard</span></li>
            </a>

            <li class="{{ request()->routeIs('profile') || request()->routeIs('profile.edit') || request()->routeIs('profile.kyc') ? 'active' : '' }}"
                style="display: block;">
                <span><a href="{{ route('profile') }}" style="text-decoration:none; color:inherit;display:block;">👤
                        Profile</a></span>
                <ul
                    style="list-style:none; padding-left:10px; margin-top:10px; display:{{ request()->routeIs('profile') || request()->routeIs('profile.edit') || request()->routeIs('profile.kyc') ? 'block' : 'none' }};">
                    <li class="{{ request()->routeIs('profile') ? 'active' : '' }}">
                        <a href="{{ route('profile') }}"
                            style="text-decoration:none; color:inherit; display:block;">View
                            Profile</a>
                    </li>
                    <li class="{{ request()->routeIs('profile.edit') ? 'active' : '' }}">
                        <a href="{{ route('profile.edit') }}"
                            style="text-decoration:none; color:inherit; display:block;">Update Profile</a>
                    </li>
                    <li class="{{ request()->routeIs('profile.kyc') ? 'active' : '' }}">
                        <a href="{{ route('profile.kyc') }}"
                            style="text-decoration:none; color:inherit; display:block;">Upload KYC</a>
                    </li>
                </ul>
            </li>


            <a href="{{ route('member.register') }}" style="text-decoration: none; color: inherit; display: block;">
                <li class="{{ Request::routeIs('member.register') ? 'active' : '' }}">📝 <span>Registration</span></li>
            </a>
            <li class="team-item {{ request()->routeIs('tree', 'team.list', 'team.direct', 'team.total', 'team.level') ? 'active open' : '' }}"
                style="display:block;padding:0px;">
                <div class="team-menu">
                    <span class="team-title">
                        👥 <span>Team Detail</span>
                    </span>
                    <span class="arrow">▾</span>
                </div>

                <ul class="submenu">
                    <li>
                        <a href="{{ route('tree') }}">🌳 Tree View</a>
                    </li>
                    <li>
                        <a href="{{ route('team.list') }}">📋 List View</a>
                    </li>
                    <li>
                        <a href="{{ route('team.direct') }}">📋 Direct Referral</a>
                    </li>
                    <li>
                        <a href="{{ route('team.total') }}">📋 Total Downline</a>
                    </li>
                    <li>
                        <a href="{{ route('team.level') }}">📋 Total Level Downline</a>
                    </li>
                </ul>
            </li>


            <a href="{{ route('wallet.fund') }}" style="text-decoration:none;color:inherit;display:block;">
                <li class="{{ Request::routeIs('wallet.fund') ? 'active' : '' }}">💰 <span>Wallet Fund Request</span>
                </li>
            </a>

            <a href="{{ route('member.topup') }}" style="text-decoration:none;color:inherit;display:block;">
                <li class="{{ Request::routeIs('member.topup') ? 'active' : '' }}">🔄 <span>Member Topup</span></li>
            </a>

            <a href="{{ route('withdraw.index') }}" style="text-decoration:none;color:inherit;display:block;">
                <li class="{{ Request::routeIs('withdraw.index') ? 'active' : '' }}">💸 <span>Withdraw</span></li>
            </a>

            <a href="{{ route('reports.index') }}" style="text-decoration:none;color:inherit;display:block;">
                <li class="{{ Request::routeIs('reports.index') ? 'active' : '' }}">📊 <span>Report</span></li>
            </a>

            <!-- <li>🎁 <span>Reward Bonus</span></li> -->

            <a href="{{ route('mailbox') }}" style="text-decoration:none;color:inherit;display:block;">
                <li class="{{ Request::routeIs('mailbox') ? 'active' : '' }}">📬 <span>Mail Box</span></li>
            </a>
        @endif

        @if (Auth::user()->hasRole('admin'))
            <a href="{{ url('/dashboard') }}" style="text-decoration:none;color:inherit;display:block;">
                <li class="{{ Request::is('dashboard') ? 'active' : '' }}">🧭 <span>Admin Console</span></li>
            </a>

            <a href="{{ route('admin.users') }}" style="text-decoration:none;color:inherit;display:block;">
                <li class="{{ Request::routeIs('admin.users') ? 'active' : '' }}">👥 <span>Manage Users</span></li>
            </a>

            <a href="{{ route('packages.index') }}" style="text-decoration:none;color:inherit;display:block;">
    <li class="{{ Request::routeIs('packages.*') ? 'active' : '' }}">📦 <span>Manage Packages</span></li>
</a>

            <a href="{{ route('admin.payments') }}" style="text-decoration:none;color:inherit;display:block;">
                <li class="{{ Request::routeIs('admin.payments') ? 'active' : '' }}">💳 <span>Manage Payments</span>
                </li>
            </a>

            <a href="{{ route('admin.payouts') }}" style="text-decoration:none;color:inherit;display:block;">
                <li class="{{ Request::routeIs('admin.payouts') ? 'active' : '' }}">🏦 <span>Manage Payouts</span></li>
            </a>

            <a href="{{ route('admin.support') }}" style="text-decoration:none;color:inherit;display:block;">
                <li class="{{ Request::routeIs('admin.support') ? 'active' : '' }}">🆘 <span>Support Request</span>
                </li>
            </a>

            <a href="{{ route('admin.lucky.index') }}" style="text-decoration:none;color:inherit;display:block;">
                <li class="{{ Request::routeIs('admin.lucky.index') ? 'active' : '' }}">🎁 <span>Lucky Draw</span></li>
            </a>

            <li class="team-item {{ request()->is('admin/settings/qr*') || request()->routeIs('team.list') ? 'active open' : '' }}"
                style="display:block;padding:0px;">

                <div class="team-menu">
                    <span class="team-title">
                        ⚙️ <span>Settings</span>
                    </span>
                    <span class="arrow">▾</span>
                </div>

                <ul class="submenu">
                    <li>
                        <a href="{{ route('admin.settings.viewQR', 1) }}"
                            class="{{ request()->fullUrl() == route('admin.settings.viewQR', 1) ? 'active' : '' }}">
                            QR Code
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.settings.viewQR', 2) }}"
                            class="{{ request()->fullUrl() == route('admin.settings.viewQR', 2) ? 'active' : '' }}">
                            USDT
                        </a>
                    </li>
                </ul>
            </li>
        @endif





        <a href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
            style="text-decoration: none; color: inherit; display: block;">
            <li>🚪 <span>Logout</span></li>
        </a>

        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
            @csrf
        </form>

    </ul>


</aside>
