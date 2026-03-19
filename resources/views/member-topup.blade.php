@extends('common.layout')
@section('title', 'Member Topup')
@section('main')
    <style>
        .card {
            background: var(--card);
            border: 1px solid #1f2832;
            border-radius: var(--radius);
            padding: 24px;
            margin-bottom: 24px;
            box-shadow: 0 0 20px #00000040;
        }

        label {
            font-size: 14px;
            color: var(--muted);
        }

        input,
        select {
            width: 100%;
            padding: 10px;
            border-radius: 8px;
            border: 1px solid #1f2832;
            background: #141c22;
            color: #fff;
        }

        .btn {
            background: var(--accent);
            color: #000;
            border: none;
            padding: 10px 20px;
            font-weight: 600;
            border-radius: 8px;
            cursor: pointer;
        }

        .btn:hover {
            opacity: .9;
        }

        .alert {
            padding: 10px 15px;
            border-radius: 8px;
            margin-bottom: 15px;
        }

        .alert-success {
            background: var(--accent);
            color: #000;
        }

        .alert-error {
            background: #ff4a4a;
            color: #fff;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th,
        td {
            border: 1px solid #1e2b36;
            padding: 10px;
            text-align: center;
            font-size: 14px;
            white-space: nowrap;
        }

        th {
            background: #161f29;
            color: #a9b9c7;
        }

        td {
            color: #d4dee8;
        }

        h2 {
            margin-top: 0px;
            font-size: 22px;
            margin: 0px;
        }

        /* Pagination Hide Page Numbers */
        nav[aria-label="Pagination Navigation"]>div:last-child {
            display: none !important;
        }

        nav[aria-label="Pagination Navigation"]>div:first-child {
            display: flex !important;
            justify-content: center !important;
            align-items: center !important;
            gap: 12px;
        }

        nav[aria-label="Pagination Navigation"] a {
            color: var(--accent) !important;
            font-size: 14px !important;
            font-weight: 600;
            padding: 6px 12px !important;
            border-radius: 6px;
        }

        nav[aria-label="Pagination Navigation"] span[aria-disabled="true"] {
            opacity: 0.4;
            cursor: not-allowed;
        }

        /* ===== Password Modal ===== */
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

        #member_status {
            padding: 5px;
            border-radius: 4px;
            display: inline-block;
        }

        #member_name_display {
            border: 2px solid #2563eb;
            font-weight: bold;
            color: #1e293b;
        }
    </style>

    <div class="header">
        <h1> Member Topup</h1>
        <div class="user-info"
            style="background:#141c22;padding:8px 14px;border-radius:999px;color:var(--accent);font-weight:600;">
            👤 {{ $user->username ?? $user->name }}
        </div>
    </div>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div class="alert alert-error">{{ session('error') }}</div>
    @endif


    <div class="card">
        <h2>EMI Progress</h2>
        <p style="font-size:16px;color:var(--accent);font-weight:600;">
            EMI Completed: {{ $user->investment_count }}/16
        </p>
        <div style="background:#141c22;border-radius:8px;overflow:hidden;margin-top:10px;">
            <div
                style="width:{{ ($user->investment_count / 16) * 100 }}%;background:#3f7871;padding:6px 0;color:#fff;text-align:center;">
                {{ round(($user->investment_count / 16) * 100) }}% Complete
            </div>
        </div>
        @if ($user->emi_status == 'completed')
            <p style="color:#00ff99;font-weight:600;">✅ All EMIs completed – Reward Released!</p>
        @endif
    </div>


    {{-- <div class="card">
        <h2>New Topup</h2>
        <form method="POST" action="{{ route('member.topup.store') }}" id="topupForm">
            @csrf
            <div class="form-group">
                <label>Member ID</label>
                <input type="text" name="member_id" id="member_id_input" placeholder="e.g. HGNL00010109" required>
            </div>
            <div class="form-group">
                <label>Package</label>
                <select name="package_id" id="packageSelect" required>
                    <option value="">-- Select Package --</option>
                    @foreach ($packages as $p)
                        <option value="{{ $p->id }}" data-amount="{{ $p->amount }}">{{ $p->name }} —
                            ₹{{ $p->amount }}</option>
                    @endforeach
                </select>
                <input type="hidden" name="final_amount" id="finalAmount" value="0">
                <div id="priceBreakdown" style="margin-top:10px;font-size:13px;color:var(--muted);"></div>
            </div>
            <div class="form-group"><label>Payment By</label>
                <select name="payment_by" required>
                    <option value="Wallet">Wallet</option>
                    <option value="Online">Online</option>
                </select>
            </div>
            <button type="submit" class="btn">Submit</button>
        </form>
    </div> --}}

    <div class="card">
        <h2>New Topup</h2>
        <form method="POST" action="{{ route('member.topup.store') }}" id="topupForm">
            @csrf
            {{-- <div class="form-group">
                <label>Member ID</label>
                <input type="text" name="member_id" id="member_id_input" placeholder="e.g. HGNL00010109" required>
            </div> --}}

            {{-- <div class="form-group">
                <label>Member ID</label>
                <input type="text" name="member_id" id="member_id_input" placeholder="e.g. HGNL00010125" required
                    autocomplete="off">
                <div id="member_status" style="margin-top:5px; font-size:13px; font-weight:600;"></div>
            </div> --}}

            <div class="form-group">
                <label>Member ID</label>
                <input type="text" name="member_id" id="member_id_input" value="{{ old('member_id') }}"
                    style="border: 1px solid {{ $errors->has('member_id') ? 'red' : '#ccc' }};"
                    placeholder="e.g. HGNL00010125" required>

                @error('member_id')
                    <div style="color: #dc3545; font-size: 13px; margin-top: 5px; font-weight: 600;">
                        {{ $message }}
                    </div>
                @enderror

                <div id="member_status" style="margin-top:5px; font-size:13px; font-weight:600;"></div>
            </div>

            <div class="form-group" id="name_group" style="display:none;">
                <label>Member Name</label>
                <input type="text" id="member_name_display" class="form-control" readonly
                    style="background-color: #f0f0f0;">
            </div>
            <div class="form-group">
                <label>Package</label>
                <select name="package_id" id="packageSelect" required>
                    <option value="">-- Select Package --</option>
                    @foreach ($packages as $p)
                        <option value="{{ $p->id }}" data-amount="{{ $p->amount }}">{{ $p->name }} —
                            ₹{{ $p->amount }}</option>
                    @endforeach
                </select>
                {{-- REMOVED hidden final_amount to prevent validation errors --}}
                <div id="priceBreakdown" style="margin-top:10px;font-size:14px;color:var(--accent);font-weight:600;"></div>
            </div>
            <div class="form-group">
                <label>Payment By</label>
                <select name="payment_by" required>
                    <option value="Wallet">Wallet</option>
                    <option value="Online">Online</option>
                </select>
            </div>
            <button type="submit" class="btn" style="width:100%">Submit Payment</button>
        </form>
    </div>

    <div class="card">
        <h2>Your Wallet Balance</h2>
        <p style="font-size:20px;color:var(--accent);font-weight:700;">₹{{ number_format($wallet->balance ?? 0, 2) }}</p>
    </div>

    <div class="card table-res">
        <h2>Recent Topups</h2>
        <div class="table-res">
            @php
                $transactions = DB::table('orders')->where('user_id', $user->id)->orderByDesc('id')->get();
            @endphp
            <table class="table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Package</th>
                        <th>Amount</th>
                        <th>Payment By</th>
                        <th>Status</th>
                        <th>Date & Time</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($transactions as $index => $t)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ DB::table('packages')->where('id', $t->package_id)->value('name') }}</td>
                            <td>₹{{ number_format($t->amount, 2) }}</td>
                            <td>{{ $t->payment_by }}</td>
                            <td>{{ ucfirst($t->status) }}</td>
                            <td>{{ \Carbon\Carbon::parse($t->created_at)->setTimezone('Asia/Kolkata')->format('d M Y, h:i A') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6">No topups yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

    </div>

    <div class="card ">
        <h2>Topups Done by My Wallet</h2>
        <div class="table-res">
            <table class="table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>To User</th>
                        <th>Package</th>
                        <th>Amount</th>
                        <th>Payment By</th>
                        <th>Status</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($walletTransactions as $index => $t)
                        @php
                            $toUser = DB::table('users')->where('id', $t->user_id)->value('member_id');
                            $packageName = DB::table('packages')->where('id', $t->package_id)->value('name');
                        @endphp
                        <tr>
                            <td>{{ $walletTransactions->firstItem() + $index }}</td>
                            <td>{{ $toUser }}</td>
                            <td>{{ $packageName }}</td>
                            <td>₹{{ number_format($t->amount, 2) }}</td>
                            <td>{{ $t->payment_by }}</td>
                            <td>{{ ucfirst($t->status) }}</td>
                            <td>{{ \Carbon\Carbon::parse($t->created_at)->format('d M Y, h:i A') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7">No wallet transactions yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{ $walletTransactions->links() }}
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
                    <input type="password" name="new_password_confirmation" id="new_password_confirmation" required
                        minlength="6">
                </div>
                <button type="submit" class="btn btn-copy" style="width:100%;">Update Password</button>
            </form>
        </div>
    </div>

    <script>
        // ✅ Keep UI logic only - Backend will handle the actual math for safety
        const investmentCount = {{ (int) $user->investment_count }};
        const registrationFee = investmentCount === 0 ? 100 : 0;
        const packageSelect = document.getElementById('packageSelect');
        const priceBreakdown = document.getElementById('priceBreakdown');
        const memberIdInput = document.getElementById('member_id_input');

        function updatePriceDisplay() {
            const selectedOption = packageSelect.options[packageSelect.selectedIndex];

            if (!selectedOption || !selectedOption.value) {
                priceBreakdown.innerHTML = '';
                return;
            }

            const baseAmount = parseFloat(selectedOption.dataset.amount) || 0;
            const totalAmount = baseAmount + registrationFee;

            let breakdown = `Payable Amount: ₹${totalAmount.toLocaleString('en-IN')}`;
            if (registrationFee > 0) {
                breakdown +=
                    ` <span style="color:var(--muted); font-weight:400; font-size:12px;">(Includes ₹100 Reg. Fee)</span>`;
            }
            priceBreakdown.innerHTML = breakdown;
        }

        packageSelect.addEventListener('change', updatePriceDisplay);
        memberIdInput.addEventListener('input', function() {
            this.value = this.value.toUpperCase();
        });
        document.addEventListener('DOMContentLoaded', updatePriceDisplay);

        // Modal Logic
        const passwordModal = document.getElementById("passwordModal");

        function openModal() {
            passwordModal.style.display = "flex";
        }

        function closeModal() {
            passwordModal.style.display = "none";
        }
        window.onclick = function(e) {
            if (e.target === passwordModal) closeModal();
        };
    </script>
    {{-- <script>
        // ✅ Configuration
        const investmentCount = {{ $user->investment_count }};
        const registrationFee = investmentCount === 0 ? 100 : 0;
        const packageSelect = document.getElementById('packageSelect');
        const priceBreakdown = document.getElementById('priceBreakdown');
        const finalAmountInput = document.getElementById('finalAmount');
        const memberIdInput = document.getElementById('member_id_input');

        function updatePriceDisplay() {
            const selectedOption = packageSelect.options[packageSelect.selectedIndex];

            if (!selectedOption || !selectedOption.value) {
                priceBreakdown.innerHTML = '';
                finalAmountInput.value = '0';
                return;
            }

            const baseAmount = parseFloat(selectedOption.dataset.amount) || 0;
            const totalAmount = baseAmount + registrationFee;

            // Update hidden input
            finalAmountInput.value = totalAmount;

            let breakdown =
                `<strong>Final Amount: ₹${totalAmount.toLocaleString('en-IN', {minimumFractionDigits: 2})}</strong>`;

            if (registrationFee > 0) {
                breakdown +=
                    `<br/>📋 Base: ₹${baseAmount.toLocaleString('en-IN', {minimumFractionDigits: 2})} + Registration Fee: ₹${registrationFee}`;
            }

            priceBreakdown.innerHTML = breakdown;
        }

        // Listen for changes
        packageSelect.addEventListener('change', updatePriceDisplay);
        
        // Auto-uppercase Member ID
        memberIdInput.addEventListener('input', function() {
            this.value = this.value.toUpperCase();
        });

        // Run on page load (handles back button / browser cache)
        document.addEventListener('DOMContentLoaded', updatePriceDisplay);

        // Pre-submit check
        document.getElementById('topupForm').addEventListener('submit', function(e) {
            if (parseFloat(finalAmountInput.value) <= 0) {
                e.preventDefault();
                alert("Please select a package.");
            }
        });

        // Password Modal logic
        const passwordModal = document.getElementById("passwordModal");
        function openModal() { passwordModal.style.display = "flex"; }
        function closeModal() { passwordModal.style.display = "none"; }
        window.onclick = function(e) { if (e.target === passwordModal) closeModal(); };

        document.getElementById("passwordForm").addEventListener("submit", function(e) {
            const newPass = document.getElementById("new_password").value.trim();
            const confirmPass = document.getElementById("new_password_confirmation").value.trim();
            if (newPass !== confirmPass) {
                e.preventDefault();
                alert("Passwords do not match!");
            }
        });

        // Handle Laravel logout securely
        document.getElementById('logout-link').addEventListener('click', function(e) {
            e.preventDefault();
            document.getElementById('logout-form').submit();
        });
    </script> --}}


    {{-- <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            const idInput = $('#member_id_input');
            const statusDiv = $('#member_status');
            const nameGroup = $('#name_group');
            const nameDisplay = $('#member_name_display');
            const submitBtn = $('#submitBtn');

            // Updated for HGNL00010125 format
            const ID_LENGTH = 12;

            idInput.on('input propertychange', function() {
                // Force uppercase for consistency (HGNL vs hgnl)
                let val = $(this).val().trim().toUpperCase();
                $(this).val(val);

                if (val.length === ID_LENGTH) {
                    validateMember(val);
                } else {
                    // Clear status if they backspace or change the ID
                    statusDiv.html('');
                    resetValidation();
                }
            });

            function validateMember(memberId) {
                statusDiv.html('<span style="color:#2563eb;">Checking Database...</span>');

                $.ajax({
                    url: "{{ route('member.check-id') }}",
                    method: "GET",
                    data: {
                        member_id: memberId
                    },
                    success: function(response) {
                        if (response.success) {
                            statusDiv.html('<span style="color:green;">✔ Member Found: ' + response
                                .name + '</span>');
                            nameDisplay.val(response.name);
                            nameGroup.slideDown(); // Smoothly show the name field
                            submitBtn.prop('disabled', false); // Unlock the button
                        } else {
                            statusDiv.html('<span style="color:red;">✘ Invalid Member ID</span>');
                            resetValidation();
                        }
                    },
                    error: function() {
                        statusDiv.html('<span style="color:red;">Connection Error</span>');
                        resetValidation();
                    }
                });
            }

            function resetValidation() {
                nameGroup.hide();
                nameDisplay.val('');
                submitBtn.prop('disabled', true);
            }
        });
    </script> --}}

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            console.log("Script Loaded & Ready"); // Debug log

            const idInput = $('#member_id_input');
            const statusDiv = $('#member_status');
            const nameGroup = $('#name_group');
            const nameDisplay = $('#member_name_display');
            const submitBtn = $('button[type="submit"]'); // Targets your submit button

            const ID_LENGTH = 12;

            idInput.on('input propertychange', function() {
                let val = $(this).val().trim().toUpperCase();
                $(this).val(val);

                console.log("Current Length: " + val.length); // Debug log

                if (val.length === ID_LENGTH) {
                    validateMember(val);
                } else {
                    statusDiv.html('');
                    nameGroup.hide();
                    submitBtn.prop('disabled', true);
                }
            });

            function validateMember(memberId) {
                statusDiv.html('<span style="color:blue;">Searching...</span>');

                $.ajax({
                    url: "{{ route('member.check-id') }}",
                    method: "GET",
                    data: {
                        member_id: memberId
                    },
                    success: function(response) {
                        console.log("Server Response:", response); // Debug log

                        if (response.success) {
                            // This is where the magic happens:
                            statusDiv.html('<span style="color:green;">✔ Member Verified</span>');
                            nameDisplay.val(response.name);
                            nameGroup.show(); // Make sure this isn't hidden by CSS
                            submitBtn.prop('disabled', false);
                        } else {
                            statusDiv.html('<span style="color:red;">✘ Invalid ID</span>');
                            nameGroup.hide();
                            submitBtn.prop('disabled', true);
                        }
                    },
                    error: function(xhr) {
                        console.error("AJAX Error:", xhr.responseText);
                        statusDiv.html('<span style="color:red;">Server Error</span>');
                    }
                });
            }
        });
    </script>
@endsection
