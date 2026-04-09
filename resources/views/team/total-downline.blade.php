@extends('common.layout')
@section('title', 'Team List')
@section('main')

<style>

body{
  background:var(--bg);
  color:var(--text);
  font-family:"Inter",sans-serif;
}

/* ===== Header ===== */
.page-header{
  display:flex;
  justify-content:space-between;
  align-items:center;
  margin-bottom:20px;
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

/* ===== Grid Wrapper ===== */
.team-grid{
  display:grid;
  grid-template-columns:repeat(auto-fill,minmax(340px,1fr));
  gap:14px;
}

/* ===== Card ===== */
.team-card{
  background:#fff;
  border:1px solid var(--border);
  border-radius:14px;
  padding:14px 16px;
  display:flex;
  align-items:center;
  justify-content:space-between;
  gap:12px;
  transition:.25s ease;
}


/* ===== Left ===== */
.tc-left{
  display:flex;
  align-items:center;
  gap:12px;
}
.tc-index{
  width:34px;
  height:34px;
  border-radius:50%;
  background:#1b2531;
  display:flex;
  align-items:center;
  justify-content:center;
  font-weight:700;
}
.tc-name{
  font-size:14px;
  color: #000;
  font-weight:600;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
  width: 100px;
}
.tc-username{
  font-size:12px;
    color: #000;;
}

/* ===== Center ===== */
.tc-center{
  text-align:center;
  min-width:90px;
}
.tc-label{
  font-size:11px;
  color: #000;;
  display:block;
}
.tc-value{
  font-size:13px;
  font-weight:700;color:#000;
}

/* ===== Right ===== */
.position{
  padding:6px 14px;
  border-radius:999px;
  font-size:12px;
  font-weight:700;
  white-space:nowrap;
}
.position.left{
  background:#1f3a1a;
  color:#a7ff1e;
}
.position.right{
  background:#183041;
  color:#5fd2ff;
}

/* ===== Empty ===== */
.empty{
  text-align:center;
  padding:40px;
  color:var(--muted);
  border:1px dashed var(--border);
  border-radius:12px;
}

/* ===== Mobile ===== */
@media(max-width:768px){
  .team-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill,minmax(293px,1fr));
    gap: 14px;
}
  .team-card{
      padding: 14px 6px;
    align-items:flex-start;
  }
  .tc-center{
    text-align:left;
  }
}
</style>

@php
    // $leftCount = $users->where('position', 'left')->count();
    // $rightCount = $users->where('position', 'right')->count();
    $leftCount = $users->where('team_side', 'left')->count();
    $rightCount = $users->where('team_side', 'right')->count();
@endphp
<!-- ===== HEADER ===== -->
<div class="page-header">
  <h2>Total Downline</h2>
  <div class="user-info">👤 {{ auth()->user()->name }}</div>
</div>

<!-- ===== FILTER BUTTONS ===== -->
<div class="filter-wrapper">
    <button class="filter-btn active" onclick="filterTeam('all', event)">All ({{ $users->count() }})</button>
    <button class="filter-btn" onclick="filterTeam('left', event)">Left ({{ $leftCount }})</button>
    <button class="filter-btn" onclick="filterTeam('right', event)">Right ({{ $rightCount }})</button>
</div>

<!-- ===== GRID ===== -->
<div class="team-grid">

@forelse($users as $key => $user)
  {{-- <div class="team-card" data-position="{{ strtolower($user->position) }}">

    <!-- LEFT -->
    <div class="tc-left">
      <div class="tc-index">{{ $key+1 }}</div>
      <div>
        <div class="tc-name">{{ $user->username }}</div>
        <div class="tc-username">{{ $user->name }}</div>
      </div>
    </div>

    <!-- CENTER -->
    <div class="tc-center">
      <span class="tc-label">Placement ID</span>
      <span class="tc-value">{{ $user->placement_id }}</span>
    </div>

    <!-- RIGHT -->
    <span class="position {{ strtolower($user->position) }}">
      {{ ucfirst($user->position) }}
    </span>

  </div> --}}

  <div class="team-card" data-position="{{ strtolower($user->team_side) }}">

    <div class="tc-left">
      {{-- <div class="tc-index">{{ $key+1 }}</div> --}}
      <div>
        <div class="tc-name">{{ $user->username }}</div>
        <div class="tc-username">{{ $user->name }}</div>
      </div>
    </div>

    <div class="tc-center">
      <span class="tc-label">Placement ID</span>
      <span class="tc-value">{{ $user->placement_id }}</span>
    </div>

    {{-- Changed from $user->position to $user->team_side --}}
    <span class="position {{ strtolower($user->team_side) }}">
      {{ ucfirst($user->team_side) }}
    </span>

</div>
@empty
  <div class="empty">No team members found.</div>
@endforelse

</div>

<!-- ===== STYLE ===== -->
<style>
.filter-wrapper {
    margin-bottom: 20px;
}

.filter-btn {
    padding: 8px 16px;
    border: none;
    background: #1f2832;
    color: #fff;
    border-radius: 6px;
    cursor: pointer;
    margin-right: 8px;
    transition: 0.3s ease;
}

.filter-btn:hover {
    background: #00c896;
}

.filter-btn.active {
    background: #00c896;
}
</style>

<!-- ===== SCRIPT ===== -->


<script>
function filterTeam(position, event) {

    const cards = document.querySelectorAll('.team-card');
    const buttons = document.querySelectorAll('.filter-btn');
    const indexes = document.querySelectorAll('.tc-index');

    // Remove active class
    buttons.forEach(btn => btn.classList.remove('active'));
    event.target.classList.add('active');

    let serial = 1; // reset counter

    cards.forEach(card => {

        if (position === 'all' || card.dataset.position === position) {
            card.style.display = 'flex';

            // update serial number
            card.querySelector('.tc-index').innerText = serial;
            serial++;

        } else {
            card.style.display = 'none';
        }

    });
}
</script>

<!-- ===== SCRIPT ===== -->
<script>
/* Logout */
document.getElementById('logout-link')?.addEventListener('click',function(e){
  e.preventDefault();
  document.getElementById('logout-form').submit();
});


</script>

@endsection
