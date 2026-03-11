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
.sidebar{width:250px;background:var(--sidebar);border-right:1px solid #12181f;display:flex;flex-direction:column;}
.sidebar ul{list-style:none;margin:0;padding:0;}
.sidebar ul li{padding:14px 18px;color:var(--muted);cursor:pointer;}
.sidebar ul li:hover, .sidebar ul li.active{background:#141c26;color:#fff;}
.main{flex:1;padding:20px;}
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
.header h1 {

    margin: 0px;
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

</div>

<style>
.filter-btn {
    padding: 8px 14px;
    border: none;
    border-radius: 6px;
    background: #2c3e50;
    color: #fff;
    cursor: pointer;
    font-size: 14px;
}

.filter-btn:hover {
    background: #1abc9c;
}
</style>


  <div class="card table-res">
    <table>
      <thead>
        <tr>          <!-- <th>User ID</th> -->
          <th>Name</th>
          <th>Sponsor</th>
          <th>Joining Date</th>
          <th>Branch</th>
          <th>Investments</th>
          <th>Status</th>
        </tr>
      </thead>
      <tbody>
        @foreach($teamMembers as $index => $member)
        <tr 
            data-position="{{ strtolower(trim($member->position)) }}" 
            data-emi="{{ ($member->investment_count ?? 0) > 0 ? 'paid' : 'pending' }}"
        >

          <!-- <td>{{ $member->username }}</td> -->
          <td>{{ $member->name }}</td>
          <td>{{ $member->sponsor_name ?? 'N/A' }}</td>
          <td>{{ $member->created_at->format('d/m/Y') }}</td>
          <td>{{ $member->position }}</td>
          <td>{{ $member->investment_count ?? 0 }}</td>
          <td>
            @if(($member->investment_count ?? 0) === 0)
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
function filterUsers(type) {
    let rows = document.querySelectorAll("tbody tr");

    rows.forEach(row => {

        let position = row.getAttribute("data-position")?.toLowerCase();
        let emi = row.getAttribute("data-emi");

        if(type === 'all') {
            row.style.display = '';
        }

        else if(type === 'left') {
            row.style.display = (position && position.includes('left')) ? '' : 'none';
        }

        else if(type === 'right') {
            row.style.display = (position && position.includes('right')) ? '' : 'none';
        }

        else if(type === 'emi_pending') {
            row.style.display = (emi === 'pending') ? '' : 'none';
        }

        else if(type === 'emi_paid') {
            row.style.display = (emi === 'paid') ? '' : 'none';
        }
    });
}
</script>



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