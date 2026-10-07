<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>EMI Tracker - Himjan Trading</title>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
<style>
:root {--bg:#f0f8ef;--card:#ffffff;--sidebar:#ffffff;--accent:#23845b;--border:#cddfd3;--text:#203c30;--muted:#52695f;--radius:12px;}
body{margin:0;font-family:"Inter",sans-serif;background:var(--bg);color:var(--text);display:flex;min-height:100vh;}
.sidebar{width:250px;background:var(--sidebar);border-right:1px solid #ffffff;display:flex;flex-direction:column;}
.main{flex:1;padding:20px;}
.card{background:var(--card);border:1px solid #e5f2e6;border-radius:var(--radius);padding:24px;margin-bottom:24px;box-shadow:0 0 20px #00000040;}
h1,h2{color:var(--accent);}
.table{width:100%;border-collapse:collapse;margin-top:20px;}
th,td{border:1px solid #e5f2e6;padding:10px;text-align:center;}
th{background:#e5f2e6;color:#203c30;}
.status-paid{color:#00ff99;font-weight:700;}
.status-pending{color:#ff5555;font-weight:700;}
.pagination{display:flex;justify-content:center;list-style:none;gap:6px;margin-top:15px;}
.pagination li a,.pagination li span{padding:8px 12px;border:1px solid #e5f2e6;border-radius:6px;background:#ffffff;color:var(--accent);text-decoration:none;}
.pagination li.active span{background:var(--accent);color:#000;}
</style>
    <link rel="stylesheet" href="{{ asset('css/light-green-theme.css') }}">
</head>

<body>

<!-- Sidebar -->
<aside class="sidebar" id="sidebar">
  <div class="logo">HT</div>
  <div class="toggle-btn" id="toggleBtn">☰</div>
  <ul>
    <a href="{{ route('dashboard') }}" style="text-decoration:none;color:inherit;display:block;"><li>🏠 Dashboard</li></a>
    <a href="{{ route('emi.index') }}" style="text-decoration:none;color:inherit;display:block;"><li class="active">📅 EMI Tracker</li></a>
    <a href="{{ route('member.topup') }}" style="text-decoration:none;color:inherit;display:block;"><li>🔄 Member Topup</li></a>
    <a href="{{ route('mailbox') }}" style="text-decoration:none;color:inherit;display:block;"><li>📬 Mail Box</li></a>
  </ul>
</aside>

<!-- Main -->
<main class="main">
  <div class="header" style="display:flex;justify-content:space-between;align-items:center;">
    <h1>📅 EMI Tracker</h1>
    <div class="user-info" style="background:#ffffff;padding:8px 14px;border-radius:999px;color:var(--accent);font-weight:600;">
      👤 {{ $user->username ?? $user->name }}
    </div>
  </div>

  @if(session('success'))
    <div style="background:#23845b;color:#000;padding:10px 15px;border-radius:8px;margin-bottom:15px;">✅ {{ session('success') }}</div>
  @endif

  <div class="card">
    <h2>Your EMI Schedule</h2>
    <table class="table">
      <thead>
        <tr><th>#</th><th>EMI No</th><th>Amount</th><th>Due Date</th><th>Status</th></tr>
      </thead>
      <tbody>
        @forelse($emis as $index => $e)
          <tr>
            <td>{{ $emis->firstItem() + $index }}</td>
            <td>{{ $e->emi_number }}</td>
            <td>₹{{ number_format($e->amount,2) }}</td>
            <td>{{ \Carbon\Carbon::parse($e->due_date)->format('d M Y') }}</td>
            <td class="{{ $e->status == 'Paid' ? 'status-paid' : 'status-pending' }}">
              {{ $e->status }}
            </td>
          </tr>
        @empty
          <tr><td colspan="5">No EMI records found.</td></tr>
        @endforelse
      </tbody>
    </table>

    <div>{{ $emis->links('partials.pagination') }}</div>
  </div>
</main>

<script>
const sidebar=document.getElementById('sidebar');
document.getElementById('toggleBtn').addEventListener('click',()=>sidebar.classList.toggle('collapsed'));
</script>
</body>
</html>
