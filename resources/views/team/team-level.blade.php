@extends('common.layout')
@section('title', 'Team List')
@section('main')

<style>
/* ===== PAGE BASE ===== */
body{
  background:var(--bg);
  color:var(--text);
  font-family:"Inter",sans-serif;
}

/* ===== HEADER ===== */
.page-header{
  display:flex;
  justify-content:space-between;
  align-items:center;
  margin-bottom:24px;
}
.page-header h2{
  margin:0;
  font-size:22px;
}
.user-info{
  background:#141c22;
  padding:8px 14px;
  border-radius:999px;
  color:var(--accent);
  font-weight:600;
}

/* ===== LEVEL CARD ===== */
.level-card{
  background:var(--card);
  border:1px solid #1f28324d;
  border-radius:16px;
  padding:20px;
  margin-bottom:24px;
}

.level-title{
  font-size:18px;
  font-weight:700;
  margin-bottom:14px;
  color:var(--accent);
}

/* ===== TABLE ===== */
.table-wrap{
  overflow-x:auto;
}

.table{
  width:100%;
  border-collapse:collapse;
  table-layout:fixed; /* 🔥 fixes alignment */
}

.table col:nth-child(1){width:60px;}
.table col:nth-child(2){width:45%;}
.table col:nth-child(3){width:45%;}

.table th,
.table td{
  padding:12px;
  border:1px solid var(--border);
  font-size:14px;
  vertical-align:middle;
}

.table th{
  background:#161f29;
  color:#a9b9c7;
  font-weight:600;
  text-align:left;
}

.table td{
  color:#e9eef3;
}

.table td:first-child,
.table th:first-child{
  text-align:center;
  font-weight:700;
  color:var(--accent);
}

.table tr:hover{
  background:#141c26;
}

/* ===== EMPTY STATE ===== */
.empty{
  padding:20px;
  border:1px dashed var(--border);
  border-radius:12px;
  color:var(--muted);
  text-align:center;
}

/* ===== RESPONSIVE ===== */
@media(max-width:768px){
  .page-header{
    flex-direction:column;
    align-items:flex-start;
    gap:10px;
  }
}
</style>

<!-- ===== HEADER ===== -->
<div class="page-header">
  <h2>Team Level Downline</h2>
  <div class="user-info">👤 {{ auth()->user()->name }}</div>
</div>

<!-- ===== LEVEL LOOP ===== -->
@forelse($levels as $level => $users)

  <div class="level-card">
    <div class="level-title">Level {{ $level }}</div>

    @if(count($users))
    <div class="table-wrap">
      <table class="table">
        <colgroup>
          <col>
          <col>
          <col>
        </colgroup>

        <thead>
          <tr>
            <th>#</th>
            <th>Name</th>
            <th>Username</th>
          </tr>
        </thead>

        <tbody>
          @foreach($users as $key => $user)
            @if(isset($user['name']) && isset($user['username']))
            <tr>
              <td>{{ $key + 1 }}</td>
              <td>{{ $user['name'] }}</td>
              <td>{{ $user['username'] }}</td>
            </tr>
            @endif
          @endforeach
        </tbody>
      </table>
    </div>
    @else
      <div class="empty">No members in this level</div>
    @endif
  </div>

@empty
  <div class="empty">No downline data available</div>
@endforelse

<!-- ===== SCRIPTS ===== -->
<script>
/* Logout */
document.getElementById('logout-link')?.addEventListener('click',function(e){
  e.preventDefault();
  document.getElementById('logout-form').submit();
});


</script>

@endsection
