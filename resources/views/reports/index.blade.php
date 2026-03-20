@extends('common.layout')
@section('title', 'Reports')
@section('main')
    <style>
        /* Your Original CSS Section */
        body {
            margin: 0;
            font-family: "Inter", sans-serif;
            background: var(--bg);
            color: var(--text);
            display: flex;
            min-height: 100vh;
        }

        .user-info {
            background: #141c22;
            padding: 8px 14px;
            border-radius: 999px;
            display: flex;
            align-items: center;
            gap: 8px;
            color: var(--accent);
            font-weight: 600;
        }

        .card {
            background: var(--card);
            border: 1px solid #1f2832;
            border-radius: var(--radius);
            padding: 20px;
            box-shadow: 0 0 20px #00000050;
            margin-bottom: 24px;
        }

        .card h2 {
            font-size: 18px;
            margin-top: 0
        }

        .btn {
            padding: 10px 18px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
            transition: .25s;
        }

        .btn-copy {
            background: linear-gradient(90deg, var(--accent), var(--accent));
            color: #000;
            white-space: nowrap;
            text-decoration: none;
        }

        .btn-whatsapp {
            background: var(--accent);
            color: #fff;
            white-space: nowrap;
            text-decoration: none;
        }

        .table-wrap {
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        th, td {
            border: 1px solid #1e2b36;
            padding: 10px;
            text-align: center;
            font-size: 14px;
        }

        th {
            background: #161f29;
            color: #a9b9c7;
        }

        td {
            color: #d4dee8
        }

        /* Password Modal CSS */
        .modal {
            display: none;
            position: fixed;
            z-index: 100;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background: rgba(11, 14, 18, 0.9);
            justify-content: center;
            align-items: center;
            backdrop-filter: blur(4px);
        }

        .modal-content {
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            padding: 24px;
            width: 90%;
            max-width: 400px;
            color: var(--text);
            position: relative;
            box-shadow: 0 0 30px #00000080;
        }

        .modal-content h2 {
            margin-top: 0;
            color: var(--accent);
            text-align: center;
        }

        .modal-content .close {
            position: absolute;
            top: 10px;
            right: 16px;
            font-size: 22px;
            cursor: pointer;
            color: var(--muted);
        }

        .form-group {
            margin-bottom: 16px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: var(--muted);
            font-size: 14px;
        }

        .form-group input {
            width: 100%;
            padding: 10px;
            border-radius: 8px;
            border: 1px solid #2a3442;
            background: #0f1620;
            color: #fff;
        }
    </style>

    <div class="header">
        <h1>Reports</h1>
        <div class="user-info">👤 {{ $user->username ?? $user->name }}</div>
    </div>

    <div class="card">
        <h2>Income Reports</h2>

        <form id="filterForm" method="GET" action="{{ route('reports.index') }}"
            style="margin-bottom:20px;display:flex;gap:10px;flex-wrap:wrap;align-items:flex-end;">
            <div>
                <label style="display:block; font-size:12px; margin-bottom:5px;">From:</label>
                <input type="date" name="from" value="{{ $from }}" onchange="this.form.submit()"
                    style="padding:8px;border-radius:6px;background:#141c22;border:1px solid #1f2832;color:#fff;">
            </div>
            <div>
                <label style="display:block; font-size:12px; margin-bottom:5px;">To:</label>
                <input type="date" name="to" value="{{ $to }}" onchange="this.form.submit()"
                    style="padding:8px;border-radius:6px;background:#141c22;border:1px solid #1f2832;color:#fff;">
            </div>
            
            <button class="btn btn-copy" type="submit">Filter</button>
            <a href="{{ route('reports.index') }}" class="btn btn-whatsapp">Reset</a>

            <button type="submit" id="dynamicExportBtn" formaction="{{ route('reports.export', 'matching') }}" class="btn btn-copy">
                ⬇ Export CSV
            </button>
        </form>

        <div style="display:flex;gap:10px;margin-bottom:16px;">
            <button class="btn" id="matchingBtn">Matching Income</button>
            <button class="btn" id="directBtn" style="background:#333;color:#fff;">Direct Income</button>
        </div>

        <div id="matchingTab">
            <h3 style="color:var(--accent)">Matching Income</h3>
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Date</th>
                            <th>Amount</th>
                            <th>Remarks</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($matchingIncomes as $r)
                            <tr>
                                <td>{{ ($matchingIncomes->currentPage() - 1) * $matchingIncomes->perPage() + $loop->iteration }}</td>
                                <td>{{ date('d M Y, h:i A', strtotime($r->created_at)) }}</td>
                                <td>₹{{ number_format($r->amount, 2) }}</td>
                                <td>{{ $r->remarks }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="4">No records found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            {{ $matchingIncomes->appends(request()->query())->links() }}
        </div>

        <div id="directTab" style="display:none;">
            <h3 style="color:var(--accent)">Direct Income</h3>
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Date</th>
                            <th>Amount</th>
                            <th>Remarks</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($directIncomes as $r)
                            <tr>
                                <td>{{ ($directIncomes->currentPage() - 1) * $directIncomes->perPage() + $loop->iteration }}</td>
                                <td>{{ date('d M Y, h:i A', strtotime($r->created_at)) }}</td>
                                <td>₹{{ number_format($r->amount, 2) }}</td>
                                <td>{{ $r->remarks }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="4">No records found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            {{ $directIncomes->appends(request()->query())->links() }}
        </div>
    </div>

    <div id="passwordModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal()">&times;</span>
            <h2>Change Password</h2>
            <form id="passwordForm" method="POST" action="{{ route('changep.update') }}">
                @csrf
                <div class="form-group">
                    <label for="new_password">New Password</label>
                    <input type="password" name="new_password" id="new_password" required minlength="6">
                </div>
                <div class="form-group">
                    <label for="new_password_confirmation">Confirm Password</label>
                    <input type="password" name="new_password_confirmation" id="new_password_confirmation" required minlength="6">
                </div>
                <button type="submit" class="btn btn-copy" style="width:100%;">Update Password</button>
            </form>
        </div>
    </div>

    <script>
        const matchTab = document.getElementById('matchingTab');
        const directTab = document.getElementById('directTab');
        const matchBtn = document.getElementById('matchingBtn');
        const directBtn = document.getElementById('directBtn');
        const exportBtn = document.getElementById('dynamicExportBtn');

        // Routes for dynamic export
        const matchingExportRoute = "{{ route('reports.export', 'matching') }}";
        const directExportRoute = "{{ route('reports.export', 'direct') }}";

        function setMatchActive() {
            matchTab.style.display = 'block'; 
            directTab.style.display = 'none';
            matchBtn.style.background = 'var(--accent)'; 
            matchBtn.style.color = '#000';
            directBtn.style.background = '#333'; 
            directBtn.style.color = '#fff';
            // Update button to export matching data
            exportBtn.setAttribute('formaction', matchingExportRoute);
        }

        function setDirectActive() {
            matchTab.style.display = 'none'; 
            directTab.style.display = 'block';
            directBtn.style.background = 'var(--accent)'; 
            directBtn.style.color = '#000';
            matchBtn.style.background = '#333'; 
            matchBtn.style.color = '#fff';
            // Update button to export direct data
            exportBtn.setAttribute('formaction', directExportRoute);
        }

        matchBtn.onclick = setMatchActive;
        directBtn.onclick = setDirectActive;

        // Persist tab on page load
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.has('direct_page')) { 
            setDirectActive(); 
        } else { 
            setMatchActive(); 
        }

        /* Original Password Modal Logic */
        const passwordModal = document.getElementById("passwordModal");
        document.querySelectorAll("li span").forEach(item => {
            if (item.textContent.trim() === "Password") {
                item.parentElement.addEventListener("click", () => passwordModal.style.display = "flex");
            }
        });
        function closeModal() { passwordModal.style.display = "none"; }
        window.onclick = (e) => { if (e.target === passwordModal) closeModal(); };

        /* Original Logout Logic */
        const logoutLink = document.getElementById('logout-link');
        if(logoutLink) {
            logoutLink.addEventListener('click', function(e) {
                e.preventDefault();
                document.getElementById('logout-form').submit();
            });
        }
    </script>
@endsection