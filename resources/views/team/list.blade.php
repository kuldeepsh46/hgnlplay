@extends('common.layout')
@section('title', 'Team List')
@section('main')

    <style>
        :root {
            --bg: #0b0e12;
            --card: #10171f;
            --sidebar: #0f141b;
            --accent: #a7ff1e;
            --text: #e9eef3;
            --muted: #a0acb3;
        }

        body {
            margin: 0;
            font-family: "Inter", sans-serif;
            background: var(--bg);
            color: var(--text);
            display: flex;
            min-height: 100vh;
        }

        .sidebar {
            width: 250px;
            background: var(--sidebar);
            border-right: 1px solid #12181f;
            display: flex;
            flex-direction: column;
        }

        .sidebar ul {
            list-style: none;
            margin: 0;
            padding: 0;
        }

        .sidebar ul li {
            padding: 14px 18px;
            color: var(--muted);
            cursor: pointer;
        }

        .sidebar ul li:hover,
        .sidebar ul li.active {
            background: #141c26;
            color: #fff;
        }

        .main {
            flex: 1;
            padding: 20px;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .user-info {
            background: #141c22;
            padding: 8px 14px;
            border-radius: 999px;
            color: var(--accent);
            font-weight: 600;
        }

        .card {
            background: rgba(255, 255, 255, 0.02);
            border: 1px solid rgba(255, 255, 255, 0.05);
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.4);
            margin: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        th,
        td {
            border: 1px solid rgba(255, 255, 255, 0.1);
            padding: 10px;
            text-align: left;
            font-size: 14px;
        }

        th {
            background: #161f29;
            color: #a9b9c7;
        }

        td {
            color: #d4dee8;
        }

        tr:hover {
            background: rgba(255, 255, 255, 0.04);
        }


        To make your filter interface look modern,
        "premium," and professional,
        we will use Soft UI (Neumorphism-inspired) styles. This uses clean gradients,
        smooth transitions,
        and subtle hover effects that make the buttons feel like they are "clickable" elements. Here is the updated CSS to replace your existing <style>block. You do not need to change the function names or HTML IDs. The Advanced Modern Styles HTML <style>

        /* Container for the filter sections */
        .filter-container {
            background: #ffffff;
            padding: 20px;
            border-radius: 16px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
            margin-bottom: 25px;
        }

        .filter-group {
            display: flex;
            gap: 12px;
            margin-bottom: 15px;
            flex-wrap: wrap;
        }

        .filter-btn {
            padding: 10px 22px;
            border: 1px solid #e2e8f0;
            background: #ffffff;
            cursor: pointer;
            border-radius: 50px;
            /* Fully rounded pills */
            transition: all 0.3s ease;
            font-size: 14px;
            font-weight: 600;
            color: #64748b;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.02);
        }

        .filter-btn:hover {
            background: #f8fafc;
            border-color: #cbd5e1;
            color: #334155;
        }

        /* The Active State (The "Premium" look) */
        /* The Active State - ensured !important to override hover states */
        .filter-btn.active {
            background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%) !important;
            color: #ffffff !important;
            border-color: transparent !important;
            box-shadow: 0 4px 12px rgba(37, 99, 235, 0.3) !important;
        }

        /* Make the counts look cleaner */
        .filter-btn span {
            opacity: 0.8;
            font-weight: 400;
            margin-left: 5px;
            font-size: 12px;
        }
    </style>


    <div class="header">
        <h1>Team List</h1>
        <div class="user-info">👤 {{ $user->username ?? $user->name }}</div>
    </div>


    @php
        $leftCount = $teamMembers->where('position', 'left')->count();
        $rightCount = $teamMembers->where('position', 'right')->count();
        $emiPendingCount = $teamMembers->where('investment_count', 0)->count();
        $emiPaidCount = $teamMembers->where('investment_count', '>', 0)->count();
    @endphp

    {{-- 
    <div class="filter-buttons" style="display:flex; gap:15px; flex-wrap:wrap; margin-bottom:20px;">

        <button class="filter-btn" onclick="filterUsers('all')">
            All ({{ $teamMembers->count() }})
        </button>

        <button class="filter-btn" onclick="filterUsers('left')">
            Left Users ({{ $leftCount }})
        </button>

        <button class="filter-btn" onclick="filterUsers('right')">
            Right Users ({{ $rightCount }})
        </button>

        <button class="filter-btn" onclick="filterUsers('emi_pending')">
            EMI Pending ({{ $emiPendingCount }})
        </button>

        <button class="filter-btn" onclick="filterUsers('emi_paid')">
            EMI Paid ({{ $emiPaidCount }})
        </button>

    </div> --}}

    <!-- Category Tabs -->
    <div class="filter-group">
        <button class="filter-btn active" onclick="selectCategory(this, 'all')">All (<span id="count-all">0</span>)</button>
        <button class="filter-btn" onclick="selectCategory(this, 'left')">Left (<span id="count-left">0</span>)</button>
        <button class="filter-btn" onclick="selectCategory(this, 'right')">Right (<span id="count-right">0</span>)</button>
    </div>

    <div id="status-filters" class="filter-group" style="display:flex;">
        <button id="btn-status-all" class="filter-btn active" onclick="filterByStatus(this, 'all')">All Status (<span
                id="count-stat-all">0</span>)</button>
        <button id="btn-status-active" class="filter-btn" onclick="filterByStatus(this, 'active')">Active (<span
                id="count-stat-active">0</span>)</button>
        <button id="btn-status-pending" class="filter-btn" onclick="filterByStatus(this, 'pending')">Pending (<span
                id="count-stat-pending">0</span>)</button>
    </div>
    {{-- {{dd($teamMembers)}} --}}

    <div class="card table-res">
        <table>

            <thead>
                <tr>
                    <th>Member ID</th>
                    <th>Name</th>
                    <th>Mobile</th>
                    <th>Sponsor</th>
                    <th>Joining Date</th>
                    <th>Activation Date</th>
                    <th>EMI Progress</th>
                    <th>Branch</th>
                    <th>Investments</th>
                    <th>Status</th>
                </tr>
            </thead>

            <tbody>
                {{-- {{dd($teamMembers)}} --}}
                @foreach ($teamMembers as $member)
                    {{-- {{dd($member)}} --}}
                    @php
                        $isPaid = ($member->investment_count ?? 0) > 0;
                    @endphp
                    <tr data-position="{{ strtolower(trim($member->position)) }}"
                        data-status="{{ $isPaid ? 'active' : 'pending' }}">

                        <td>{{ $member->member_id }}</td>
                        <td>{{ $member->name }}</td>
                        <td>{{ $member->mobile ?? 'N/A' }}</td>
                        <td>{{ $member->sponsor_name ?? 'N/A' }}</td>
                        <td>{{ $member->created_at->format('d/m/Y') }}</td>
                        <td>
                            @if ($member->activation_date)
                                {{ \Carbon\Carbon::parse($member->activation_date)->format('d/m/Y') }}
                            @else
                                <span class="text-danger">Not Activated</span>
                            @endif
                        </td>
                        {{-- <td>
                            @if ($member->activation_date)
                                @php
                                    $activationDate = \Carbon\Carbon::parse($member->activation_date);

                                    $activationMonth = $activationDate->year * 12 + $activationDate->month;
                                    $currentMonth = now()->year * 12 + now()->month;

                                    $totalEmisSupposedToPay = $currentMonth - $activationMonth + 1;

                                    // Optional: maximum 12 EMIs
                                    // $totalEmisSupposedToPay = min($totalEmisSupposedToPay, 12);

                                    $totalEmisPaid = $member->total_emis_paid ?? 0;
                                @endphp

                                {{ $totalEmisPaid }}/{{ $totalEmisSupposedToPay }}
                            @else
                                N/A
                            @endif
                        </td> --}}
                        <td>
                            @if ($member->activation_date)
                                @php
                                    $activationDate = \Carbon\Carbon::parse($member->activation_date);
                                    $activationMonth = $activationDate->year * 12 + $activationDate->month;
                                    $currentMonth = now()->year * 12 + now()->month;
                                    $totalEmisSupposedToPay = $currentMonth - $activationMonth + 1;

                                    $totalEmisPaid = $member->total_emis_paid ?? 0;

                                    // Determine color: Red if behind, Green if up-to-date or ahead
                                    $statusColor = $totalEmisPaid < $totalEmisSupposedToPay ? '#ff3b3b' : '#00b050';
                                @endphp

                                <span style="font-weight: bold; color: {{ $statusColor }};">
                                    {{ $totalEmisPaid }} / {{ $totalEmisSupposedToPay }}
                                </span>

                                <div style="font-size: 10px; color: #888;">
                                    {{ $totalEmisPaid < $totalEmisSupposedToPay ? 'Pending Payments' : 'Up to Date' }}
                                </div>
                            @else
                                <span style="color: #666; font-style: italic;">N/A</span>
                            @endif
                        </td>
                        <td>{{ $member->position }}</td>
                        <td>{{ $member->investment_count ?? 0 }}</td>
                        <td>
                            @if (($member->investment_count ?? 0) === 0)
                                <span style="color:#ff3b3b;">Pending</span>
                            @elseif(($member->investment_count ?? 0) === 1)
                                <span style="color:#888;">Active 1st</span>
                            @elseif(($member->investment_count ?? 0) === 2)
                                <span style="color:orange;">Active 2nd</span>
                            @else
                                <span style="color:#00b050;">Active 3rd+</span>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <script>
        let currentCat = 'all';
        let currentStat = 'all';

        // Run immediately when page loads
        document.addEventListener('DOMContentLoaded', () => {
            updateAllCounts();
        });

        function selectCategory(el, cat) {
            currentCat = cat;
            currentStat = 'all';

            // 1. Reset ONLY the buttons in the category group
            let categoryGroup = el.closest('.filter-group');
            categoryGroup.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
            el.classList.add('active');

            // 2. Reset ONLY the buttons in the status group
            let statusGroup = document.getElementById('status-filters');
            statusGroup.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
            document.getElementById('btn-status-all').classList.add('active');

            applyFilters();
        }

        function filterByStatus(el, stat) {
            currentStat = stat;

            // 1. Reset ONLY the buttons in the status group
            let statusGroup = el.closest('.filter-group');
            statusGroup.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
            el.classList.add('active');

            applyFilters();
        }

        function applyFilters() {
            let rows = document.querySelectorAll("tbody tr");
            rows.forEach(row => {
                let pos = row.getAttribute("data-position")?.toLowerCase();
                let stat = row.getAttribute("data-status")?.toLowerCase();
                let matchCat = (currentCat === 'all' || pos.includes(currentCat));
                let matchStat = (currentStat === 'all' || stat === currentStat);
                row.style.display = (matchCat && matchStat) ? '' : 'none';
            });
            updateAllCounts();
        }

        function updateAllCounts() {
            let rows = document.querySelectorAll("tbody tr");

            // Variables to hold counts
            let counts = {
                all: 0,
                left: 0,
                right: 0,
                stat_all: 0,
                stat_active: 0,
                stat_pending: 0
            };

            rows.forEach(row => {
                let pos = row.getAttribute("data-position")?.toLowerCase();
                let stat = row.getAttribute("data-status")?.toLowerCase();

                // Total Category counts (independent of status)
                counts.all++;
                if (pos.includes('left')) counts.left++;
                if (pos.includes('right')) counts.right++;

                // Status counts (based on currently selected category)
                if (currentCat === 'all' || pos.includes(currentCat)) {
                    counts.stat_all++;
                    if (stat === 'active') counts.stat_active++;
                    if (stat === 'pending') counts.stat_pending++;
                }
            });

            // Update Category Labels
            document.getElementById('count-all').innerText = counts.all;
            document.getElementById('count-left').innerText = counts.left;
            document.getElementById('count-right').innerText = counts.right;

            // Update Status Labels
            document.getElementById('count-stat-all').innerText = counts.stat_all;
            document.getElementById('count-stat-active').innerText = counts.stat_active;
            document.getElementById('count-stat-pending').innerText = counts.stat_pending;
        }
    </script>

@endsection
