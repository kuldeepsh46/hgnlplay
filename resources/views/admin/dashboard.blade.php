<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Admin Console - HGNL Pay</title>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

<style>
:root {
  --bg:#0b0e12;
  --card:#10171f;
  --sidebar:#0f141b;
  --accent:#a7ff1e;
  --accent2:#2ee6a6;
  --border:#1b222b;
  --text:#e9eef3;
  --muted:#a0acb3;
  --radius:12px;
}
*{box-sizing:border-box}
body{
  margin:0;
  font-family:"Inter",sans-serif;
  background:var(--bg);
  color:var(--text);
  display:flex;
  min-height:100vh;
}

/* Sidebar */
.sidebar{
  width:250px;
  background:var(--sidebar);
  border-right:1px solid #12181f;
  display:flex;
  flex-direction:column;
  transition:.3s ease;
  position:relative;
}
.sidebar.collapsed{width:72px}
.sidebar .logo{
  text-align:center;
  font-weight:800;
  font-size:18px;
  color:var(--accent);
  padding:18px 0;
  border-bottom:1px solid #1b2330;
}
.sidebar ul{list-style:none;margin:0;padding:0;flex-grow:1;}
.sidebar ul li{
  display:flex;
  align-items:center;
  gap:10px;
  padding:14px 18px;
  color:var(--muted);
  cursor:pointer;
  transition:.25s;
  border-left:4px solid transparent;
}
.sidebar ul li:hover, .sidebar ul li.active{
  background:#141c26;
  color:#fff;
  border-left:4px solid var(--accent);
}
.sidebar ul li span{transition:.3s;}
.sidebar.collapsed ul li span{display:none}

/* Sidebar Toggle Button */
.toggle-btn{
  position:absolute;
  top:16px;
  right:-18px;
  background:var(--accent);
  color:#000;
  width:34px;height:34px;
  border-radius:50%;
  display:grid;
  place-items:center;
  cursor:pointer;
  box-shadow:0 0 10px #a7ff1e66;
}

/* Main */
.main{flex:1;padding:20px;overflow-x:hidden;}
.header{display:flex;justify-content:space-between;align-items:center;margin-bottom:20px;}
.header h1{font-size:22px}
.user-info{background:#141c22;padding:8px 14px;border-radius:999px;display:flex;align-items:center;gap:8px;color:var(--accent);font-weight:600;}

/* Cards */
.card{
  background:var(--card);
  border:1px solid #1f2832;
  border-radius:var(--radius);
  padding:20px;
  box-shadow:0 0 20px #00000050;
  margin-bottom:24px;
}
.grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:18px;}
.stat{background:#131a23;border:1px solid #1f2832;border-radius:var(--radius);padding:18px;text-align:center;position:relative;overflow:hidden;}
.stat h3{margin:0;font-size:26px;color:var(--accent);}
.stat p{margin:6px 0 0;color:var(--muted);font-size:14px;}
.stat::after{content:"";position:absolute;inset:-30px;background:radial-gradient(600px 300px at 80% 0%,#a7ff1e0d,transparent 70%);filter:blur(4px);z-index:0;}

/* Responsive */
@media(max-width:768px){
  .sidebar{position:fixed;z-index:20;left:-260px;top:0;bottom:0}
  .sidebar.open{left:0}
  .main{padding:80px 16px}
  .toggle-btn{right:-10px;top:10px}
}
</style>
</head>
<body>

<!-- Sidebar -->
<aside class="sidebar" id="sidebar">
  <div class="logo">HT</div>
  <div class="toggle-btn" id="toggleBtn">☰</div>
  <ul>
    @if(Auth::user()->hasRole('admin'))
      <li class="active">🧭 <span>Admin Console</span></li>
      <a href="{{ route('admin.dashboard') }}" style="text-decoration:none;color:inherit;display:block;">
        <li>📊 <span>Overview</span></li>
      </a>
      <a href="{{ route('admin.users') }}" style="text-decoration:none;color:inherit;display:block;">
        <li>👥 <span>Manage Users</span></li>
      </a>
      <a href="{{ route('admin.withdraws') }}" style="text-decoration:none;color:inherit;display:block;">
        <li>🏦 <span>Manage Payouts</span></li>
      </a>
      <a href="{{ route('admin.support') }}" style="text-decoration:none;color:inherit;display:block;">
        <li>🆘 <span>Support Request</span></li>
      </a>
      {{-- <a href="{{ route('admin.settings') }}" style="text-decoration:none;color:inherit;display:block;">
        <li>🆘 <span>Settings</span></li>
      </a> --}}
    @endif
    <a href="{{ route('logout') }}" id="logout-link" style="text-decoration:none;color:inherit;display:block;">
      <li>🚪 <span>Logout</span></li>
    </a>
    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display:none;">@csrf</form>
  </ul>
</aside>

<!-- Main -->
<main class="main">
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
</main>

<script>
const sidebar=document.getElementById('sidebar');
const toggleBtn=document.getElementById('toggleBtn');
toggleBtn.addEventListener('click',()=>{sidebar.classList.toggle(window.innerWidth<768?'open':'collapsed');});
document.getElementById('logout-link').addEventListener('click',e=>{e.preventDefault();document.getElementById('logout-form').submit();});
</script>
</body>
</html>
