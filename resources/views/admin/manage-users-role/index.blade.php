@extends('common.layout')
@section('title', 'Superadmin Control')

<style>
    /* .pagination {
    display: flex;
    justify-content: center;
    list-style: none;
    padding: 20px 0;
    gap: 10px;
}

.pagination li {
    display: inline-block;
}

.pagination li a, 
.pagination li span {
    background: var(--card-bg) !important;
    border: 1px solid #233145 !important;
    color: var(--text-dim) !important;
    padding: 10px 18px;
    border-radius: 8px;
    text-decoration: none;
    font-weight: 800;
    font-family: 'JetBrains Mono', monospace;
    transition: 0.3s;
}

/* Current Page */
.pagination li.active span {
    background: var(--neon-green) !important;
    color: #000 !important;
    border-color: var(--neon-green) !important;
    box-shadow: 0 0 15px var(--border-glow);
}

/* Hover State */
.pagination li a:hover {
    border-color: var(--neon-green) !important;
    color: #fff !important;
}

/* Fix Giant Arrows (Laravel SVG fix) */
.pagination svg {
    width: 20px !important;
    height: 20px !important;
} */

/* Hide the "Showing X to Y" text that Laravel sometimes forces */
nav div.hidden {
    display: none !important;
}
    :root {
        --neon-green: #00ffa3;
        --neon-red: #ff2e55;
        --panel-bg: #0b111b;
        --card-bg: #151f2e;
        --border-glow: rgba(0, 255, 163, 0.1);
    }

    body {
        background-color: var(--panel-bg);
        font-family: 'JetBrains Mono', 'Inter', monospace;
    }

    .admin-wrapper { padding: 40px; }

    /* --- TOP COMMAND BAR --- */
    .command-center {
        background: linear-gradient(180deg, #151f2e 0%, #0b111b 100%);
        border: 1px solid #233145;
        border-radius: 20px;
        padding: 30px;
        box-shadow: 0 20px 50px rgba(0,0,0,0.5);
        margin-bottom: 40px;
    }

    .header-tag {
        font-size: 10px;
        letter-spacing: 2px;
        color: var(--neon-green);
        text-transform: uppercase;
        margin-bottom: 10px;
    }

    .search-hex {
        display: flex;
        background: #070c14;
        border: 1px solid #233145;
        border-radius: 12px;
        padding: 5px;
        transition: 0.3s;
    }

    .search-hex:focus-within {
        border-color: var(--neon-green);
        box-shadow: 0 0 15px var(--border-glow);
    }

    .search-hex input {
        background: transparent;
        border: none;
        color: #fff;
        padding: 12px 20px;
        flex: 1;
        outline: none;
    }

    /* --- INSPECTION CARDS (The "Glow" Look) --- */
    .inspect-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 20px;
        margin-top: 25px;
    }

    .data-hex {
        background: var(--card-bg);
        border: 1px solid #233145;
        padding: 20px;
        border-radius: 15px;
        position: relative;
        transition: 0.3s;
    }

    .data-hex:hover {
        border-color: var(--neon-green);
        transform: translateY(-5px);
    }

    .hex-label { color: #64748b; font-size: 11px; text-transform: uppercase; font-weight: 800; }
    .hex-value { color: #fff; font-size: 20px; font-weight: 900; display: block; margin-top: 5px; }

    /* --- TAB NAVIGATION (Modern Underline) --- */
    .tab-nav {
        display: flex;
        gap: 30px;
        margin-bottom: 25px;
        border-bottom: 1px solid #233145;
    }

    .tab-btn {
        background: none;
        border: none;
        color: #64748b;
        padding: 15px 5px;
        font-weight: 700;
        cursor: pointer;
        position: relative;
        transition: 0.3s;
        text-decoration: none;
    }

    .tab-btn.active {
        color: var(--neon-green);
    }

    .tab-btn.active::after {
        content: '';
        position: absolute;
        bottom: -1px;
        left: 0;
        width: 100%;
        height: 2px;
        background: var(--neon-green);
        box-shadow: 0 0 10px var(--neon-green);
    }

    /* --- DATA TABLE --- */
    .table-container {
        background: #0b111b;
        border: 1px solid #233145;
        border-radius: 15px;
        overflow: hidden;
    }

    .cyber- shadow-table {
        width: 100%;
        border-collapse: collapse;
    }

    .cyber-table th {
        background: #151f2e;
        color: #64748b;
        text-align: left;
        padding: 20px;
        font-size: 12px;
        text-transform: uppercase;
    }

    .cyber-table td {
        padding: 20px;
        border-bottom: 1px solid #1a2433;
        color: #cbd5e1;
        font-size: 14px;
    }

    .cyber-table tr:hover td {
        background: rgba(0, 255, 163, 0.02);
    }

    .id-tag {
        color: var(--neon-green);
        background: rgba(0, 255, 163, 0.05);
        padding: 5px 10px;
        border-radius: 5px;
        font-weight: 800;
        font-size: 12px;
    }

    .btn-cyber {
        background: transparent;
        border: 1px solid var(--neon-green);
        color: var(--neon-green);
        padding: 6px 15px;
        border-radius: 5px;
        font-size: 11px;
        font-weight: 700;
        cursor: pointer;
        transition: 0.3s;
        text-transform: uppercase;
    }

    .btn-cyber:hover {
        background: var(--neon-green);
        color: #000;
        box-shadow: 0 0 15px var(--neon-green);
    }

    .btn-wipe {
        border-color: var(--neon-red);
        color: var(--neon-red);
    }

    .btn-wipe:hover {
        background: var(--neon-red);
        color: #fff;
        box-shadow: 0 0 15px var(--neon-red);
    }
</style>

@section('main')
<div class="admin-wrapper">
    
    <div class="command-center">
        <div class="header-tag">System Core // Admin Privileges Active</div>
        <h2 style="color: #fff; margin-bottom: 25px; font-weight: 900;">SUPERADMIN POWER PANEL</h2>

        <form action="{{ route('admin.manage.role.index') }}" method="GET" class="search-hex">
            <input type="text" name="search" placeholder="SYSTEM_RECORDS_SEARCH: ENTER MEMBER_ID..." value="{{ request('search') }}">
            <button type="submit" class="btn-cyber" style="margin: 5px; padding: 0 30px;">Initialize Scan</button>
        </form>

        @if($user)
            <div class="inspect-grid">
                <div class="data-hex">
                    <span class="hex-label">Subject ID</span>
                    <span class="hex-value">{{ $user->username }}</span>
                </div>
                <div class="data-hex">
                    <span class="hex-label">Points Matched</span>
                    <span class="hex-value" style="color: var(--neon-green);">{{ $user->investment_count ?? 0 }}</span>
                </div>
                <div class="data-hex">
                    <span class="hex-label">Ongoing EMIs</span>
                    <span class="hex-value" style="color: #ffb800;">{{ $user->emi_status ?? 0 }}</span>
                </div>
                <div class="data-hex" style="background: rgba(255, 46, 85, 0.05); border-color: rgba(255, 46, 85, 0.2);">
                    <form action="{{ route('admin.manage.reverseAll') }}" method="POST" onsubmit="return confirm('CRITICAL: Purge all data for this subject?')">
                        @csrf <input type="hidden" name="user_id" value="{{ $user->id }}">
                        <span class="hex-label">Terminal Action</span>
                        <button type="submit" class="hex-value btn-wipe" style="border: none; background: none; cursor: pointer; text-align: left; width: 100%;">PURGE RECORDS</button>
                    </form>
                </div>
            </div>
        @endif
    </div>

    <div class="tab-nav">
        <a href="{{ route('admin.manage.role.index') }}" class="tab-btn {{ !request('page') ? 'active' : '' }}">
            LIVE_ACTIVITY_LOG
        </a>
        <a href="{{ route('admin.manage.role.index', ['page' => 1]) }}" class="tab-btn {{ request('page') ? 'active' : '' }}">
            GLOBAL_USER_DATABASE
        </a>
    </div>

    <div class="table-container">
        <table class="cyber-table" style="width: 100%;">
            <thead>
                @if(request('page'))
                    <tr><th>Identity</th><th>Username</th><th>Volume</th><th>EMI_Stat</th><th>Controls</th></tr>
                @else
                    <tr><th>Identity</th><th>Action_Type</th><th>Value</th><th>Timestamp</th><th>Override</th></tr>
                @endif
            </thead>
            <tbody>
                @if(request('page'))
                    @foreach($allUsers as $u)
                    <tr onclick="window.location.href='?search={{ $u->member_id }}'" style="cursor: pointer;">
                        <td><span class="id-tag">{{ $u->member_id }}</span></td>
                        <td style="font-weight: 700;">{{ $u->username }}</td>
                        <td>{{ $u->investment_count ?? 0 }} Pts</td>
                        <td style="color: #ffb800;">{{ $u->emi_status ?? 0 }} ACTIVE</td>
                        <td><a href="?search={{ $u->member_id }}" class="btn-cyber">Inspect</a></td>
                    </tr>
                    @endforeach
                @else
                    @foreach($orders as $order)
                    <tr onclick="window.location.href='?search={{ $order->user_id }}'" style="cursor: pointer;">
                        <td><span class="id-tag">{{ $order->user_id }}</span></td>
                        <td><span style="opacity: 0.6; font-size: 11px;">[ PACKAGE_TOPUP ]</span></td>
                        <td style="font-weight: 900; color: #fff;">₹{{ number_format($order->amount) }}</td>
                        <td style="color: #64748b; font-size: 12px;">{{ \Carbon\Carbon::parse($order->created_at)->diffForHumans() }}</td>
                        <td>
                            <form action="{{ route('admin.delete.package') }}" method="POST">
                                @csrf <input type="hidden" name="order_id" value="{{ $order->id }}">
                                <button type="submit" class="btn-cyber btn-wipe">Reverse</button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                @endif
            </tbody>
        </table>
    </div>

    @if(request('page'))
        <div style="margin-top: 30px;">
            {{ $allUsers->links() }}
        </div>
    @endif
</div>
@endsection