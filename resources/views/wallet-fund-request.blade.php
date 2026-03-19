@extends('common.layout')
@section('title', 'Wallet Fund Request')
@section('main')
    <style>
        .flex-section {
            display: flex;
            width: 100%;
            justify-content: space-between;
            gap: 36px;
            align-items: stretch;
        }

        .Qr-sec {
            text-align: center;
            width: 300px;
        }

        .req-sec .grid {
            display: flex;
            width: 101%;
            flex-wrap: wrap;
        }

        .req-sec .grid div {
            width: 49% !important;
        }



        img#qr-image {
            max-width: 300px;
            width: 287px;
            height: 370px;
            object-fit: cover;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .req-sec {
            width: 76%;
        }


        .user-info {
            background: #141c22;
            padding: 8px 14px;
            border-radius: 999px;
            color: var(--accent);
            font-weight: 600;
        }

        /* Cards / form */
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
            display: block;
            margin-bottom: 6px;
        }

        input,
        select,
        textarea {
            width: 100%;
            padding: 10px;
            border-radius: 8px;
            border: 1px solid #1f2832;
            background: #141c22;
            color: #fff;
        }

        .grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 16px;
        }

        .btn {
            background: var(--accent);
            color: #000;
            border: none;
            padding: 11px 20px;
            font-weight: 700;
            border-radius: 8px;
            cursor: pointer;
        }

        .btn:hover {
            opacity: .92;
        }

        .alert {
            padding: 10px 14px;
            border-radius: 8px;
            margin-bottom: 16px;
        }

        .alert-success {
            background: #a7ff1e;
            color: #000;
        }

        /* Table */
        .table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
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


        @media(max-width:768px) {
            .Qr-sec {
                text-align: center;
                width: 100%;
            }

            .req-sec {
                width: 100%;
            }

            img#qr-image {
                max-width: 100%;
                width: 100%;
                height: auto;
                object-fit: cover;
            }

            .req-sec .grid div {
                width: 100% !important;
            }

            .flex-section {
                flex-wrap: wrap;
                gap: 13px;
                flex-direction: column-reverse;
            }


        }

        div#app {
            width: 100% !important;
            height: 100% !important;
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

        /* Responsive */
        @media only screen and (min-width: 768px) and (max-width: 1300px) {

            .req-sec .grid div {
                width: 100% !important;
                /* single column */
            }

        }

        <style>

        /* Container: Spacing and layout */
        .gateway-selector {
            display: flex;
            flex-direction: column;
            /* Stacks cards on mobile */
            gap: 15px;
            /* Space between cards */
            margin-bottom: 30px;
            max-width: 500px;
            /* Optional: limits width */
        }

        /* Make label act as a big clickable area */
        .gateway-card {
            cursor: pointer;
            position: relative;
            width: 100%;
        }

        /* STEP 1: HIDE DEFAULT RADIO CIRCLES */
        .gateway-card input {
            position: absolute;
            opacity: 0;
            width: 0;
            height: 0;
        }

        /* STEP 2: STYLE THE CARD (Label Content) */
        .card-content {
            background: #ffffff;
            /* White card against your dark background */
            border: 2px solid #e1e8ed;
            /* Subtle gray border */
            border-radius: 12px;
            padding: 16px;
            display: flex;
            align-items: center;
            /* Vertically center icon and text */
            gap: 12px;
            transition: all 0.25s ease;
            /* Smooth hover effects */
        }

        /* STEP 3: ICON STYLING */
        .gateway-card .icon {
            font-size: 24px;
            width: 32px;
            text-align: center;
        }

        /* STEP 4: TEXT STYLING */
        .gateway-card .details {
            display: flex;
            flex-direction: column;
        }

        .gateway-card .title {
            font-weight: 700;
            color: #1e293b;
            font-size: 14px;
            letter-spacing: -0.2px;
        }

        .gateway-card .subtitle {
            font-size: 11px;
            color: #64748b;
            margin-top: 1px;
        }

        /* STEP 5: OPTIONAL CHECKMARK (Hides and shows on click) */
        .gateway-card .check-mark {
            margin-left: auto;
            /* Pushes check to the far right */
            color: #2563eb;
            /* Blue check */
            font-weight: bold;
            font-size: 18px;
            opacity: 0;
            transform: scale(0.6);
            transition: 0.2s cubic-bezier(0.12, 0.4, 0.29, 1.46);
        }

        /* STEP 6: HOVER STATE */
        .gateway-card:hover .card-content {
            border-color: #cddae6;
            background: #f8fafc;
        }

        /* STEP 7: SELECTED STATE (When input is checked) */
        .gateway-card input:checked+.card-content {
            border-color: #2563eb;
            /* Blue border on click */
            background: #eff6ff;
            /* Soft blue background on click */
            box-shadow: 0 4px 12px rgba(37, 99, 235, 0.1);
        }

        .gateway-card input:checked+.card-content .check-mark {
            opacity: 1;
            transform: scale(1);
        }

        /* Optional Tablet/Desktop look */
        @media (min-width: 600px) {
            .gateway-selector {
                flex-direction: row;
                /* Switch to 2 columns on desktop */
            }
        }
    </style>
    </style>

    <!-- Header -->
    <div class="header">
        <h1> Wallet Fund Request</h1>
        <div class="user-info">👤 {{ $user->username ?? $user->name }}</div>
    </div>

    @if (session('success'))
        <div class="alert alert-success">✅ {{ session('success') }}</div>
    @endif

    <!-- Your saved bank details (from users table) -->
    <div class="card">
        <h2>Your Bank Details on File</h2>
        <div class="grid">
            <div>
                <label>Bank Name</label>
                <input type="text" value="{{ $user->bank_name }}" readonly>
            </div>
            <div>
                <label>Branch Name</label>
                <input type="text" value="{{ $user->branch_name }}" readonly>
            </div>
            <div>
                <label>IFSC Code</label>
                <input type="text" value="{{ $user->ifsc_code }}" readonly>
            </div>
            <div>
                <label>Account Holder</label>
                <input type="text" value="{{ $user->account_holder_name }}" readonly>
            </div>
            <div>
                <label>Account Number</label>
                <input type="text" value="{{ $user->account_number }}" readonly>
            </div>
        </div>
        <p style="margin-top:10px;color:var(--muted);font-size:13px">
            Tip: You can leave the “Bank Name” and “Account Number” fields empty in the form below to automatically use
            these saved values.
        </p>
    </div>

    <!-- Submit Request -->
    <div class="card">
        <h2>Submit Fund Request</h2>
        <form method="POST" action="{{ route('wallet.fund.store') }}" enctype="multipart/form-data">
            @csrf

            {{-- <div
                style="margin-bottom: 25px; display: flex; gap: 20px; justify-content: center; background: #f8fafc; padding: 15px; border-radius: 12px; border: 1px solid #e2e8f0;">
                <label style="cursor: pointer; display: flex; align-items: center; gap: 8px; font-weight: 600;">
                    <input type="radio" name="gateway_type" value="upi" checked onchange="switchQR('upi')">
                    🇮🇳 UPI (INR)
                </label>
                <label style="cursor: pointer; display: flex; align-items: center; gap: 8px; font-weight: 600;">
                    <input type="radio" name="gateway_type" value="usdt" onchange="switchQR('usdt')">
                    💎 USDT (TRC20)
                </label>
            </div> --}}
            <div class="gateway-selector">
                <label class="gateway-card">
                    <input type="radio" name="gateway_type" value="upi" checked onchange="switchQR('upi')">
                    <div class="card-content">
                        <div class="icon">🇮🇳</div>
                        <div class="details">
                            <span class="title">UPI / PhonePe</span>
                            <span class="subtitle">Pay via INR</span>
                        </div>
                        <div class="check-mark">✓</div>
                    </div>
                </label>

                <label class="gateway-card">
                    <input type="radio" name="gateway_type" value="usdt" onchange="switchQR('usdt')">
                    <div class="card-content">
                        <div class="icon">💎</div>
                        <div class="details">
                            <span class="title">USDT (TRC20)</span>
                            <span class="subtitle">Pay via Crypto</span>
                        </div>
                        <div class="check-mark">✓</div>
                    </div>
                </label>
            </div>

            <div class="flex-section">
                <div class="req-sec">
                    <div class="grid">
                        <div>
                            <label id="amount-label">Request Amount (INR)</label>
                            <input type="number" name="amount" required min="1" step="0.01"
                                placeholder="Enter amount">
                        </div>
                        <div>
                            <label>Deposit Date</label>
                            <input type="date" name="deposit_date" required value="{{ now()->toDateString() }}">
                        </div>
                        <div>
                            <label>Payment Mode</label>
                            <select name="payment_mode" required id="payment_mode_select">
                                <option value="">-- Select --</option>
                                <option value="Online Transaction">Online Transaction</option>
                            </select>
                        </div>
                        <div>
                            <label>Attach Slip / Screenshot</label>
                            <input type="file" name="attachment" required>
                        </div>
                    </div>

                    <div style="margin-top:16px">
                        <label>Transaction ID / Remark</label>
                        <textarea name="transaction_remark" rows="3" placeholder="UTR Number or Hash ID"></textarea>
                    </div>

                    <div style="margin-top:18px">
                        <button type="submit" class="btn">Submit Request</button>
                    </div>
                </div>

                <div class="Qr-sec" id="qr-payment-box" style="display:none; margin-top:15px; text-align: center;">
                    <div
                        style="display: inline-block; background: #ffffff; padding: 16px; border-radius: 20px; box-shadow: 0 10px 30px rgba(0,0,0,0.15); border: 1px solid #e1e8ed;">

                        <div style="background: #f8fafc; padding: 10px; border-radius: 12px; border: 1px solid #edf2f7;">
                            <img id="qr-display-image" src="{{ asset($upi_qr ?? 'assets/images/scanner.jpeg') }}"
                                alt="Payment QR"
                                style="width: 200px; height: 200px; display: block; object-fit: contain; border-radius: 6px;">
                        </div>

                        <div style="margin-top: 12px; border-top: 1px dashed #cbd5e0; padding-top: 10px;">
                            <span id="qr-label-text"
                                style="color: #2563eb; font-weight: 800; font-size: 14px; letter-spacing: 1px;">UPI
                                GATEWAY</span>
                            <p id="qr-sub-text"
                                style="margin: 2px 0 0 0; font-size: 10px; color: #718096; font-weight: 600;">SCAN & PAY
                                SECURELY</p>
                        </div>
                    </div>

                    <p
                        style="margin-top:12px; font-size:13px; color:var(--muted); line-height: 1.5; max-width: 280px; margin-left: auto; margin-right: auto;">
                        <i class="fas fa-info-circle"></i>
                        Please pay only to the <strong id="instruction-type">UPI</strong> address shown above and upload the
                        screenshot.
                    </p>
                </div>
            </div>
        </form>
    </div>

    <!-- Recent Requests -->
    <div class="card">
        <h2>Recent Fund Requests</h2>
        <div class="table-res">
            <table class="table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Amount</th>
                        <th>Deposit Date</th>
                        <th>Mode</th>
                        <th>Status</th>
                        <th>Bank Name</th>
                        <th>Account #</th>
                        <th>Slip</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($requests as $i => $req)
                        <tr>
                            <td>{{ $i + 1 }}</td>
                            <td>{{ $req->amount }}</td>
                            <td>{{ $req->deposit_date }}</td>
                            <td>{{ $req->payment_mode }}</td>
                            <td>{{ ucfirst($req->status) }}</td>
                            <td>{{ $req->bank_name }}</td>
                            <td>{{ $req->account_number }}</td>
                            <td>
                                @if ($req->attachment)
                                    <a href="{{ asset($req->attachment) }}" target="_blank"
                                        style="color:var(--accent)">View</a>
                                @else
                                    —
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8">No fund requests found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Password Change Modal -->
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
            @if (session('success'))
                <p style="color:#a7ff1e; text-align:center;">{{ session('success') }}</p>
            @endif
            @if (session('error'))
                <p style="color:#ff5555; text-align:center;">{{ session('error') }}</p>
            @endif
        </div>


    </div>


    <script>
        document.getElementById("passwordForm").addEventListener("submit", function(e) {
            const newPass = document.getElementById("new_password").value.trim();
            const confirmPass = document.getElementById("new_password_confirmation").value.trim();

            if (newPass !== confirmPass) {
                e.preventDefault(); // stop form submission
                alert("Passwords do not match!");
            }
        });

        // ===== Password Modal =====
        const passwordModal = document.getElementById("passwordModal");

        document.querySelectorAll("li span").forEach(item => {
            if (item.textContent.trim() === "Password") {
                item.parentElement.addEventListener("click", openModal);
            }
        });

        function openModal() {
            passwordModal.style.display = "flex";
        }

        function closeModal() {
            passwordModal.style.display = "none";
        }

        // Close when clicking outside modal
        window.onclick = function(e) {
            if (e.target === passwordModal) {
                closeModal();
            }
        };
    </script>
    <script>
        // logout
        document.getElementById('logout-link').addEventListener('click', function(e) {
            e.preventDefault();
            document.getElementById('logout-form').submit();
        });
    </script>


    <script>
        document.addEventListener("DOMContentLoaded", function() {

            // ===== Saved Bank Details (Read-only section) =====
            const savedBankName = document.querySelector(
                'input[value="{{ $user->bank_name }}"]'
            );
            const savedAccountNumber = document.querySelector(
                'input[value="{{ $user->account_number }}"]'
            );

            // ===== Submit Fund Request Fields =====
            const formBankName = document.querySelector('input[name="bank_name"]');
            const formAccountNumber = document.querySelector('input[name="account_number"]');

            // ===== Auto-fill logic =====
            if (savedBankName && savedBankName.value.trim() !== "") {
                if (formBankName && formBankName.value.trim() === "") {
                    formBankName.value = savedBankName.value.trim();
                }
            }

            if (savedAccountNumber && savedAccountNumber.value.trim() !== "") {
                if (formAccountNumber && formAccountNumber.value.trim() === "") {
                    formAccountNumber.value = savedAccountNumber.value.trim();
                }
            }

        });
    </script>

    <script>
        document.addEventListener("DOMContentLoaded", function() {

            const paymentSelect = document.querySelector('select[name="payment_mode"]');
            const qrBox = document.getElementById('qr-payment-box');

            if (!paymentSelect || !qrBox) return;

            // ===== Set Online Transaction as default =====
            paymentSelect.value = "QR Transaction";
            qrBox.style.display = "block";

            // ===== Change handler =====
            paymentSelect.addEventListener("change", function() {
                if (this.value === "QR Transaction") {
                    qrBox.style.display = "block";
                } else {
                    qrBox.style.display = "none";
                }
            });

        });
    </script>
    <script>
        // Store the paths from PHP into JS variables
        const upiImg = "{{ asset($upi_qr ?? 'assets/images/scanner.jpeg') }}";
        const usdtImg = "{{ asset($usdt_qr ?? 'assets/images/scanner.jpeg') }}";

        function switchQR(type) {
            const qrBox = document.getElementById('qr-payment-box');
            const qrImg = document.getElementById('qr-display-image');
            const qrLabel = document.getElementById('qr-label-text');
            const amountLabel = document.getElementById('amount-label');
            const instructionType = document.getElementById('instruction-type');

            // Show the box if it was hidden
            qrBox.style.display = 'block';

            if (type === 'upi') {
                qrImg.src = upiImg;
                qrLabel.innerText = "UPI GATEWAY";
                qrLabel.style.color = "#2563eb"; // Blue for UPI
                amountLabel.innerText = "Request Amount (INR)";
                instructionType.innerText = "UPI";
            } else {
                qrImg.src = usdtImg;
                qrLabel.innerText = "USDT (TRC20)";
                qrLabel.style.color = "#10b981"; // Green for USDT
                amountLabel.innerText = "Request Amount (USDT)";
                instructionType.innerText = "USDT";
            }
        }

        // Existing logic to show/hide the box based on dropdown selection
        document.getElementById('payment_mode_select').addEventListener('change', function() {
            const qrBox = document.getElementById('qr-payment-box');
            if (this.value === 'Online Transaction') {
                const selectedType = document.querySelector('input[name="gateway_type"]:checked').value;
                switchQR(selectedType);
            } else {
                qrBox.style.display = 'none';
            }
        });
    </script>
@endsection
