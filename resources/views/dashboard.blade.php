@extends('common.layout')
@section('title', 'Dashboard')
@section('main')
<style>


body{
  margin:0;
  font-family:"Inter",sans-serif;
  background:var(--bg);
  color:var(--text);
  display:flex;
  min-height:100vh;
}


/* ===== Main ===== */
.main{
  flex:1;
  padding:20px;
  overflow-x:hidden;
}

/* Header */
.header{
  display:flex;
  justify-content:space-between;
  align-items:center;
  margin-bottom:20px;
}
.header h1{font-size:22px}
.user-info{
  background:#141c22;
  padding:8px 14px;
  border-radius:999px;
  display:flex;
  align-items:center;
  gap:8px;
  color:var(--accent);
  font-weight:600;
}

/* ===== Referral Section ===== */
.card{
  background:var(--card);
  border:1px solid #1f2832;
  border-radius:var(--radius);
  padding:20px;
  box-shadow:0 0 20px #00000050;
  margin-bottom:24px;
}
.card h2{font-size:18px;margin-top:0}
.referral-controls{
  display:flex;flex-wrap:wrap;gap:14px;align-items:center;margin-top:10px;
}
.referral-controls input{
  width:100%;
  max-width:420px;
  background:#0f1620;
  border:1px solid #2a3442;
  color:#fff;
  padding:10px 12px;
  border-radius:8px;
  font-size:14px;
}
.btn{
  padding:10px 18px;
  border:none;
  border-radius:8px;
  cursor:pointer;
  font-weight:600;
  transition:.25s;
}
.btn-copy{
  background:linear-gradient(90deg,var(--accent),var(--accent));
  color:#000;
}
.btn-copy:hover{opacity:.9}
.btn-whatsapp{
  background:#e84e6d !important;color:#fff;
}
.btn-whatsapp:hover{opacity:.9}

/* ===== Performance Cards ===== */
.grid{
  display:grid;
  grid-template-columns:repeat(auto-fit,minmax(220px,1fr));
  gap:18px;
}
.stat{
  background:#131a23;
  border:1px solid #1f2832;
  border-radius:var(--radius);
  padding:18px;
  text-align:center;
  position:relative;
  overflow:hidden;
  box-shadow:0 0 15px #00000030 inset;
}
.stat h3{margin:0;font-size:26px;color:var(--accent);}
.stat p{margin:6px 0 0;color:var(--muted);font-size:14px;}
.stat::after{
  content:"";
  position:absolute;inset:-30px;
  background:radial-gradient(600px 300px at 80% 0%,#a7ff1e0d,transparent 70%);
  filter:blur(4px);z-index:0;
}

/* ===== Tables ===== */
.table-wrap{
  overflow-x:auto;
}
table{
  width:100%;
  border-collapse:collapse;
  margin-top:10px;
}
th,td{
  border:1px solid #1e2b36;
  padding:10px;
  text-align:center;
  font-size:14px;
}
th{
  background:#161f29;
  color:#a9b9c7;
}
td{color:#d4dee8}

/* ===== Responsive ===== */
@media(max-width:768px){

  .main{padding:80px 16px}
 
}

canvas#growthChart {
  background:#0b0e12;
  border:1px solid #1f2832;
  border-radius:12px;
  padding:10px;
}

.performance-card {
  background: #12181f;
  border-radius: 12px;
  padding: 25px;
  margin-top: 20px;
  color: #e9eef3;
  box-shadow: 0 2px 8px rgba(0,0,0,0.4);
}

.section-title {
  font-size: 18px;
  font-weight: 600;
  margin-bottom: 20px;
  border-left: 4px solid var(--accent);
  padding-left: 10px;
}

.performance-grid {
  display: flex;
  gap: 30px;
  flex-wrap: wrap;
}

.col {
  flex: 1;
  display: grid;
  gap: 15px;
}

.stat-box {
  background: #10171f;
  border: 1px solid #1b222b;
  border-radius: 10px;
  padding: 20px;
  text-align: center;
  transition: 0.3s ease;
  box-shadow: inset 0 0 10px rgba(167,255,30,0.05);
}

.stat-box:hover {
  background: #162029;
  box-shadow: 0 0 10px rgba(167,255,30,0.2);
}

.stat-box h3 {
  color: #ffffffff;
  font-size: 22px;
  margin: 0;
  font-weight: 600;
}

.stat-box p {
  color: #a0acb3;
  margin-top: 6px;
  font-size: 14px;
}

/* Responsive */
@media (max-width: 768px) {
  .performance-grid {
    flex-direction: column;
  }
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







.lucky-modal {
    display: none;
    position: fixed;
    inset: 0;
    background: rgba(0,0,0,0.75);
    z-index: 9999;
    align-items: center;
    justify-content: center;
}

.lucky-modal-content {
    background: linear-gradient(145deg, #020617, #0f172a);
    width: 92%;
    max-width: 720px;
    border-radius: 18px;
    padding: 24px;
    color: #e5e7eb;
    box-shadow: 0 30px 80px rgba(0,0,0,0.6);
    position: relative;
}

.lucky-close {
    position: absolute;
    right: 20px;
    top: 16px;
    font-size: 28px;
    cursor: pointer;
    color: #94a3b8;
}

.reward-banner {
    background: #020617;
    border-radius: 14px;
    padding: 16px;
    margin-bottom: 18px;
    text-align: center;
}

.reward-banner.winner {
    background: linear-gradient(135deg, #22c55e, #16a34a);
    color: #022c22;
}

.summary-grid {
    display: grid;
    grid-template-columns: repeat(3,1fr);
    gap: 12px;
    margin-bottom: 22px;
}

.summary-grid div {
    background: #020617;
    padding: 14px;
    border-radius: 12px;
    text-align: center;
}

.accordion-item {
    background: #020617;
    border-radius: 12px;
    margin-bottom: 12px;
}

.accordion-header {
    padding: 14px;
    cursor: pointer;
    font-weight: 600;
}

.accordion-body {
    display: none;
    padding: 14px;
    border-top: 1px solid #1e293b;
}

.voucher-pill {
    display: inline-block;
    padding: 8px 12px;
    margin: 6px;
    border-radius: 20px;
    background: #1e293b;
    font-size: 13px;
}

.voucher-pill.used {
    background: #334155;
    color: #94a3b8;
}

.voucher-pill.unused {
    background: #22c55e;
    color: #022c22;
}

</style>

<style>
/* ===============================
   MODAL BACKDROP
================================= */
.custom-modal {
    display: none;
    position: fixed;
    inset: 0;
    background: rgba(0, 0, 0, 0.75);
    backdrop-filter: blur(6px);
    z-index: 9999;
    justify-content: center;
    align-items: center;
    animation: fadeIn 0.2s ease-in-out;
}

/* ===============================
   MODAL CONTAINER
================================= */
.custom-modal-content {
    width: 700px;
    max-height: 85vh;
    background: #0f1b26;
    border-radius: 12px;
    box-shadow: 0 0 25px rgba(0, 255, 170, 0.15);
    border: 1px solid rgba(0, 255, 170, 0.15);
    display: flex;
    flex-direction: column;
    overflow: hidden;
}

/* ===============================
   MODAL HEADER
================================= */
.modal-header {
    padding: 18px 20px;
    background: linear-gradient(90deg, #0f1b26, #122635);
    border-bottom: 1px solid rgba(0, 255, 170, 0.15);
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.modal-header h3 {
    color: #ffffff;
    font-weight: 600;
    font-size: 18px;
}

/* Close Button */
#closeModal {
    background: #ff3b3b;
    border: none;
    width: 32px;
    height: 32px;
    border-radius: 6px;
    color: #fff;
    font-weight: bold;
    cursor: pointer;
    transition: 0.2s ease;
}

#closeModal:hover {
    background: #ff0000;
    transform: scale(1.05);
}

/* ===============================
   MODAL BODY
================================= */
.modal-body {
    padding: 15px;
    overflow-y: auto;
}

/* Scrollbar styling */
.modal-body::-webkit-scrollbar {
    width: 6px;
}

.modal-body::-webkit-scrollbar-track {
    background: #0f1b26;
}

.modal-body::-webkit-scrollbar-thumb {
    background: #00ffb3;
    border-radius: 6px;
}

/* ===============================
   TABLE STYLING
================================= */
.modal-body table {
    width: 100%;
    border-collapse: collapse;
    color: #cfd8dc;
    font-size: 14px;
}

.modal-body th {
    background: #122635;
    color: #00ffb3;
    text-align: left;
    padding: 12px;
    position: sticky;
    top: 0;
    z-index: 2;
}

.modal-body td {
    padding: 10px 12px;
    border-bottom: 1px solid rgba(255,255,255,0.05);
}

.modal-body tr:hover {
    background: rgba(0, 255, 170, 0.05);
}

/* Amount Column Highlight */
.modal-body td:nth-child(3) {
    color: #00ffb3;
    font-weight: 600;
}

/* ===============================
   ANIMATION
================================= */
@keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
}

/* ===============================
   RESPONSIVE
================================= */
@media (max-width: 768px) {
    .custom-modal-content {
        width: 95%;
    }
}
</style>

@if(Auth::user()->hasRole('customer'))
<!-- Main Content -->
  <div class="header">
    <h1>Dashboard</h1>
    <div class="user-info">
      <span>👤 {{ Auth::user()->uname ?? Auth::user()->name }}</span>
    </div>
  </div>

  <!-- Referral -->
  <div class="card">
    <h2>Referral Center</h2>

    <div>
      <label>
        <input type="radio" name="leg" value="1" checked> Left
      </label>
      <label>
        <input type="radio" name="leg" value="2"> Right
      </label>
    </div>

    <div class="referral-controls">
      <input 
        type="text" 
        id="refLink"
        value="{{ url('/register') }}?refid={{ Auth::user()->id }}&leg=1&name={{ urlencode(Auth::user()->username ?? Auth::user()->name) }}"
        readonly
      >
      <button class="btn btn-copy" onclick="copyLink()">Copy Link</button>
      <button class="btn btn-whatsapp" onclick="shareWhatsApp()">Share</button>
    </div>
  </div>
  <!-- Performance -->
<div class="card performance-card">
  <h2 class="section-title">Performance</h2>
  <div class="performance-grid">
    <div class="col">
      <div class="stat-box">
        <h3>{{ $payoutReceived ?? 0 }}</h3>
        <p>Payout Received</p>
      </div>
      <div class="stat-box">
        <h3>{{ $payoutPending ?? 0 }}</h3>
        <p>Payout Pending</p>
      </div>
      <div class="stat-box">
        <h3>{{ $totalDownline ?? 0 }}</h3>
        <p>Total Downline</p>
      </div>
      <div class="stat-box">
        <h3>₹{{ number_format($directIncome ?? 0, 2) }}</h3>
        <p>Direct Income</p>
      </div>
    </div>

    <div class="col">
      <div class="stat-box">
        <h3>{{ number_format($payoutReceived ?? 0) }}</h3>
        <p>Total Withdraw</p>
      </div>
      <div class="stat-box">
        <h3>₹{{ number_format($walletBalance ?? 0, 2) }}</h3>
        <p>Topup Wallet</p>
      </div>
      <div class="stat-box">
        <h3>₹{{ number_format($pairIncome ?? 0, 2) }}</h3>
        <p>Pair Income</p>
    </div>
    <div class="stat-box">
        <h3>
            ₹{{ number_format(($directIncome ?? 0) + ($pairIncome ?? 0), 2) }}
        </h3>
        <p>Total Earning</p>
    </div>
  </div>
</div>


@if($cycle)
<div class="card performance-card mt-4">
  <h2 class="section-title">Lucky Vouchers & Rewards</h2>

  <div class="performance-grid">
    <div class="col">
      <div class="stat-box" onclick="openLuckyModal()">
        <h3>{{ $cycle->current_month }} / 16</h3>
        <p>Months Completed</p>
      </div>

      <div class="stat-box" onclick="openLuckyModal()">
        <h3>{{ $totalVouchers }}</h3>
        <p>Total Vouchers Earned</p>
      </div>

      <div class="stat-box" onclick="openLuckyModal()">
        <h3>{{ $unusedVouchers }}</h3>
        <p>Active Vouchers</p>
      </div>
    </div>

    <div class="col">
      <div class="stat-box" onclick="openLuckyModal()">
        <h3>{{ $rewardStatus }}</h3>
        <p>Reward Status</p>
      </div>

      <div class="stat-box" onclick="openLuckyModal()">
        <h3>{{ $rewardText }}</h3>
        <p>Reward Details</p>
      </div>
    </div>
  </div>
</div>
@endif

<div id="luckyModal" class="lucky-modal">
  <div class="lucky-modal-content">

    <span class="lucky-close" onclick="closeLuckyModal()">×</span>

    <h2>🎟 Lucky Vouchers & Rewards</h2>

    @if($cycle)
    {{-- Reward Highlight --}}
    <div class="reward-banner {{ $cycle->status === 'won' ? 'winner' : '' }}">
        <h3>{{ $rewardStatus }}</h3>
        <p>{{ $rewardText }}</p>
    </div>

    {{-- Summary --}}
    <div class="summary-grid">
        <div>
            <strong>{{ $cycle->current_month }}/16</strong>
            <span>Months Completed</span>
        </div>
        <div>
            <strong>{{ $totalVouchers }}</strong>
            <span>Total Vouchers</span>
        </div>
        <div>
            <strong>{{ $unusedVouchers }}</strong>
            <span>Active Vouchers</span>
        </div>
    </div>
    @else
    <div class="alert alert-info">
        <p>No active voucher cycle found. Complete EMI/Topup to activate vouchers.</p>
    </div>
    @endif

    {{-- Accordion --}}
    @if($cycle && $voucherGroups)
    <div class="accordion">
        @foreach($voucherGroups as $month => $vouchers)
            <div class="accordion-item">
                <div class="accordion-header" onclick="toggleAccordion(this)">
                    Month {{ $month }} Vouchers
                </div>
                <div class="accordion-body">
                    @foreach($vouchers as $v)
                        <div class="voucher-pill {{ $v->status }}">
                            {{ $v->voucher_code }}
                        </div>
                    @endforeach
                </div>
            </div>
        @endforeach
    </div>
    @endif

  </div>
</div>


<script>
function openLuckyModal() {
    document.getElementById('luckyModal').style.display = 'flex';
}

function closeLuckyModal() {
    document.getElementById('luckyModal').style.display = 'none';
}

function toggleAccordion(el) {
    const body = el.nextElementSibling;
    body.style.display = body.style.display === 'block' ? 'none' : 'block';
}
</script>


@if(!$cycle)
<div class="card performance-card mt-4">
  <h2 class="section-title">🎟 Lucky Vouchers & Rewards</h2>
  <p class="text-muted mt-2">
    Purchase ₹50,000 or ₹1,00,000 package to participate in Lucky Draw Rewards.
  </p>
</div>
@endif




  <!-- Tables -->
  <div class="card" style="display: none;">
    <h2>Topup Status After Closing</h2>
    <div class="table-wrap">
      <table>
        <tr><th></th><th>Left</th><th>Right</th></tr>
        <tr><td>Topup</td><td>0</td><td>0</td></tr>
        <tr><td>Topup Amount</td><td>0.00</td><td>0.00</td></tr>
        <tr><td>Direct Topup</td><td>0</td><td>0</td></tr>
        <tr><td>Direct Topup Amount</td><td>0.00</td><td>0.00</td></tr>
      </table>
    </div>
  </div>

  <div class="card" style="display: none;">
    <h2>Total Topup Status</h2>
    <div class="table-wrap">
      <table>
        <tr><th></th><th>Left</th><th>Right</th></tr>
        <tr><td>Topup</td><td>0</td><td>0</td></tr>
        <tr><td>Topup Amount</td><td>0.00</td><td>0.00</td></tr>
        <tr><td>Direct Topup</td><td>0</td><td>0</td></tr>
        <tr><td>Direct Topup Amount</td><td>0.00</td><td>0.00</td></tr>
      </table>
    </div>
  </div>
@endif


@if(Auth::user()->hasRole('admin'))
  <div class="header">
    <h1>Admin Console</h1>
    <div class="user-info">👤 {{ Auth::user()->username ?? Auth::user()->name }}</div>
  </div>

  <!-- Stats -->
  <div class="card">
    <h2>System Overview</h2>
    <div class="grid">
      <div class="stat"><h3>{{ $totalUsers }}</h3><p>Total Users</p></div>
      <div class="stat"><h3>₹{{ number_format($totalWallet,2) }}</h3><p>Total Wallet Balance</p></div>
      <div class="stat"><h3>{{ $pendingWithdraws }}</h3><p>Pending Withdrawals</p></div>
      <div class="stat"><h3>{{ $completedWithdraws }}</h3><p>Completed Withdrawals</p></div>
      <div class="stat"><h3>{{ $totalTopups }}</h3><p>Total Top-ups</p></div>
    </div>
  </div>

    <div class="card">
        <h2>📈 Monthly Growth Overview</h2>
        <canvas id="growthChart" style="width:100%;max-height:350px;"></canvas>
    </div>
    <div class="card">
        <h2>Package based sales</h2>
        <div class="grid">
    
            <div class="stat package-card" data-package="starter">
                <h3>₹{{ number_format($starterTotal,2) }}</h3>
                <p>Starter Package (1000)</p>
            </div>
    
            <div class="stat package-card" data-package="7000">
                <h3>₹{{ number_format($sevenTotal,2) }}</h3>
                <p>Seven + One Package (7000)</p>
            </div>
    
            <div class="stat package-card" data-package="13000">
                <h3>₹{{ number_format($thirteenTotal,2) }}</h3>
                <p>Thirteen + Three Package (13000)</p>
            </div>
    
            <div class="stat package-card" data-package="50000">
                <h3>₹{{ number_format($fiftyKTotal,2) }}</h3>
                <p>Fifty Thousand Golden Package (50000)</p>
            </div>
    
            <div class="stat package-card" data-package="100000">
                <h3>₹{{ number_format($oneLakhTotal,2) }}</h3>
                <p>One Lakh Super Golden Package (100000)</p>
            </div>
    
        </div>
    </div>

@endif

<!-- Package Users Modal -->
<div id="packageModal" class="custom-modal">
    <div class="custom-modal-content">
        <div class="modal-header">
            <h3 id="modalTitle">Package Users</h3>
            <button id="closeModal">X</button>
        </div>

        <div class="modal-body" id="modalBody">
            <!-- Dynamic Data Here -->
        </div>
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
        <input type="password" name="new_password_confirmation" id="new_password_confirmation" required minlength="6">
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
    const packageUsers = @json($packageUsers);

    document.querySelectorAll('.package-card').forEach(card => {
        card.addEventListener('click', function () {

            let packageType = this.getAttribute('data-package');
            let users = [];

            // 🔥 Merge Starter Package (1000 + 1100)
            if (packageType === 'starter') {
                users = [
                    ...(packageUsers['1000'] || []),
                    ...(packageUsers['1100'] || [])
                ];
            } else {
                users = packageUsers[packageType] || [];
            }

            let modal = document.getElementById('packageModal');
            let modalBody = document.getElementById('modalBody');
            let modalTitle = document.getElementById('modalTitle');

            // ✅ Dynamic Title with count
            modalTitle.innerText = `Users for Package (${users.length})`;

            let html = `
                <table width="100%" border="1" cellpadding="8">
                    <tr>
                        <th>User Name</th>
                        <th>Email</th>
                        <th>Date</th>
                    </tr>
            `;

            if (users.length > 0) {
                users.forEach(user => {
                    html += `
                        <tr>
                            <td>${user.name ?? ''}</td>
                            <td>${user.email ?? ''}</td>
                            <td>${new Date(user.created_at).toLocaleDateString('en-GB', {
                                day: '2-digit',
                                month: 'short',
                                year: 'numeric'
                            })}</td>
                        </tr>
                    `;
                });
            } else {
                html += `
                    <tr>
                        <td colspan="4" style="text-align:center;">No users found</td>
                    </tr>
                `;
            }

            html += `</table>`;

            modalBody.innerHTML = html;
            modal.style.display = "flex";
        });
    });

    // ✅ Close only via button
    document.getElementById('closeModal').addEventListener('click', function () {
        document.getElementById('packageModal').style.display = "none";
    });

    // ✅ Prevent closing on outside click
    document.getElementById('packageModal').addEventListener('click', function (e) {
        if (e.target === this) {
            e.stopPropagation();
        }
    });
</script>

<script>
  document.getElementById("passwordForm").addEventListener("submit", function (e) {
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
window.onclick = function (e) {
  if (e.target === passwordModal) {
    closeModal();
  }
};

</script>

<script>

  // Handle Laravel logout securely
document.getElementById('logout-link').addEventListener('click', function (e) {
  e.preventDefault();
  document.getElementById('logout-form').submit();
});

</script>



<script>
const ctx = document.getElementById('growthChart').getContext('2d');

const growthChart = new Chart(ctx, {
  type: 'line',
  data: {
    labels: {!! json_encode($labels) !!},
    datasets: [
      {
        label: '👥 New Users',
        data: {!! json_encode($userData) !!},
        borderColor: '#a7ff1e',
        backgroundColor: 'rgba(167,255,30,0.15)',
        tension: 0.3,
        fill: true,
        borderWidth: 2
      },
      {
        label: '💰 Funds Added (₹)',
        data: {!! json_encode($fundData) !!},
        borderColor: '#2ee6a6',
        backgroundColor: 'rgba(46,230,166,0.15)',
        tension: 0.3,
        fill: true,
        borderWidth: 2
      }
    ]
  },
  options: {
    responsive: true,
    maintainAspectRatio: false,
    scales: {
      x: {
        grid: { color: '#1f2832' },
        ticks: { color: '#a0acb3' }
      },
      y: {
        grid: { color: '#1f2832' },
        ticks: { color: '#a0acb3' }
      }
    },
    plugins: {
      legend: {
        labels: { color: '#e9eef3' }
      },
      title: {
        display: true,
        text: 'User & Fund Growth (Current Month)',
        color: '#a7ff1e'
      }
    }
  }
});
</script>


<script>
  const baseUrl = "{{ url('/register') }}";
  const refId   = "{{ Auth::user()->id }}";
  const name    = "{{ urlencode(Auth::user()->username ?? Auth::user()->name) }}";

  function updateReferralLink() {
    const leg = document.querySelector('input[name="leg"]:checked').value;

    document.getElementById('refLink').value =
      `${baseUrl}?refid=${refId}&leg=${leg}&name=${name}`;
  }

  // Initial load
  updateReferralLink();

  // Listen for radio changes
  document.querySelectorAll('input[name="leg"]').forEach(radio => {
    radio.addEventListener('change', updateReferralLink);
  });

  function copyLink() {
    const input = document.getElementById("refLink");
    input.select();
    document.execCommand("copy");
    alert("Referral link copied!");
  }

  function shareWhatsApp() {
    const link = document.getElementById("refLink").value;
    window.open(`https://wa.me/?text=${encodeURIComponent(link)}`, "_blank");
  }
</script>


<script>


function copyLink(){
  const link = document.getElementById('refLink');
  link.select();
  document.execCommand('copy');
  alert('Referral link copied!');
}
function shareWhatsApp(){
  const link = document.getElementById('refLink').value;
  const msg = encodeURIComponent(`Join Himjan Trading using my referral: ${link}`);
  window.open(`https://wa.me/?text=${msg}`,'_blank');
}
</script>

@endsection
