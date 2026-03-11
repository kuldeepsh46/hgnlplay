@extends('common.layout')
@section('title', 'Team List')
@section('main')
<style>
:root {
  --bg:#0b0e12;
  --card:#10171f;
  --sidebar:#0f141b;
  --accent:#a7ff1e;
  --text:#e9eef3;
  --muted:#a0acb3;
}
body {margin:0;font-family:"Inter",sans-serif;background:var(--bg);color:var(--text);display:flex;min-height:100vh;}


.header{display:flex;justify-content:space-between;align-items:center;margin-bottom:20px;}
.user-info{background:#141c22;padding:8px 14px;border-radius:999px;color:var(--accent);font-weight:600;}
.card{background:rgba(255,255,255,0.02);border:1px solid rgba(255,255,255,0.05);padding:20px;border-radius:10px;box-shadow:0 0 20px rgba(0,0,0,0.4);margin:auto;}
table{width:100%;border-collapse:collapse;margin-top:10px;}
th,td{border:1px solid rgba(255,255,255,0.1);padding:10px;text-align:left;font-size:14px;}
th{background:#161f29;color:#a9b9c7;}
td{color:#d4dee8;}
tr:hover{background:rgba(255,255,255,0.04);}

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
/* ===== Header ===== */
.header{
  display:flex;
  justify-content:space-between;
  align-items:center;
  margin-bottom:20px;
}
.user-info{
  background:#141c22;
  padding:8px 14px;
  border-radius:999px;
  color:var(--accent);
  font-weight:600;
}

/* ===== Card ===== */
.card{
  background:var(--card);
  border:1px solid var(--border);
  border-radius:14px;
  padding:20px;
  box-shadow:0 0 24px rgba(0,0,0,.45);
}

/* ===== Responsive Table Wrapper ===== */
.table-res{
  width:100%;
  overflow-x:auto;
  -webkit-overflow-scrolling:touch;
}

/* ===== Table ===== */
.table{
  width:100%;
  min-width:700px;
  border-collapse:collapse;
}

.table th,
.table td{
  padding:12px 14px;
  border-bottom:1px solid rgba(255,255,255,0.08);
  text-align:left;
  font-size:14px;
  white-space:nowrap;
}

.table th{
  background:#161f29;
  color:#a9b9c7;
  font-weight:600;
}

.table td{
  color:#e3ebf3;
}

.table tbody tr:hover{
  background:rgba(255,255,255,0.04);
}

/* ===== Position Badge ===== */
.badge{
  padding:6px 14px;
  border-radius:999px;
  font-size:12px;
  font-weight:700;
}
.badge.left{
  background:#1f3a1a;
  color:#a7ff1e;
}
.badge.right{
  background:#183041;
  color:#5fd2ff;
}

/* ===== Mobile Enhancements ===== */
@media(max-width:768px){
  .card{
    padding:10px;
  }
  h2{
    font-size:18px;
  }
}
</style>



<h2>Direct Referral</h2>

<div class="card table-res">
  <table class="table">
    <thead>
        <tr>
            <th>#</th>
            <th>Username</th>
            <th>Name</th>
            <th>Joining Date</th>
            <th>Position</th>
            <th>No of EMI Paid</th>
        </tr>
    </thead>
    <tbody>
        @foreach($directs as $key => $user)
        <tr>
            <td>{{ $key+1 }}</td>
            <td>{{ $user->username }}</td>
            <td>{{ $user->name }}</td>
            <td>{{ $user->created_at->format('d M Y') }}</td>
            <td> <span class="badge {{ strtolower($user->position) }}">
                {{ ucfirst($user->position) }}
              </span></td>
              <td>{{ $user->investment_count ?? 0 }}</td>
        </tr>
        @endforeach
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
document.getElementById('logout-link').addEventListener('click', function(e) {
  e.preventDefault();
  document.getElementById('logout-form').submit();
});
</script>


@endsection