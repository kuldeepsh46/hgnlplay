@extends('common.layout')
@section('title', 'Member Registration')
@section('main')

<style>
/* ================= THEME ================= */

body{
  margin:0;
  font-family:Inter,sans-serif;
  background:var(--bg);
  color:var(--text);
}

/* ================= HEADER ================= */
.user-info{
  background:#141c22;
  padding:8px 14px;
  border-radius:999px;
  color:var(--accent);
  font-weight:600;
}

/* ================= CARD ================= */
.card{
  background:var(--card);
  border:1px solid var(--border);
  border-radius:var(--radius);
  padding:22px;
  box-shadow:0 10px 30px rgba(0,0,0,.45);
  margin-bottom:24px;
}
.card h2{
  margin-bottom:18px;

}

/* ================= FORM ================= */
.form-group{margin-bottom:16px;}
label{
  display:block;
  margin-bottom:6px;
  font-size:14px;
  color:var(--muted);
}
input, select{
  width:100%;
  padding:10px 12px;
  border-radius:8px;
  border:1px solid #2a3442;
  background:#0f1620;
  color:#fff;
  font-size:14px;
}

/* ================= SELECT STYLING ================= */
/* ===== Select dropdown option colors ===== */
select {
  background-color: #0f1620;
  color: #e9eef3;
}

/* Normal option */
select option {
  background-color: #0f1620;
  color: #e9eef3;
}

/* Hovered option (works in Firefox & some Chromium builds) */
select option:hover {
  background-color: #1b2a1f !important;
  color: var(--accent) !important;
}

/* Selected option (MOST IMPORTANT) */
select option:checked {
  background-color: #1b2a1f !important;
  color: var(--accent) !important;
}

/* Focus outline fix */
select:focus {
  outline: none;
  border-color: var(--accent);
  box-shadow: 0 0 0 2px #a7ff1e33;
}


/* ================= BUTTON ================= */
.btn-submit{
  background:linear-gradient(135deg,var(--accent),var(--accent));
  color:#fff;
  font-weight:700;
  border:none;
  border-radius:10px;
  padding:12px 30px;
  cursor:pointer;
  transition:.3s;
}
.btn-submit:hover{
  transform:translateY(-1px);
  box-shadow:0 8px 20px #a7ff1e55;
}

/* ================= ALERTS ================= */
.alert-success{
  background:#143d12;
  color:#a7ff1e;
  padding:10px 14px;
  border-radius:8px;
  margin-bottom:16px;
}

/* ================= RESPONSIVE ================= */
@media(max-width:768px){
  .header{
    flex-direction:column;
    align-items:flex-start;
    gap:8px;
  }
  select{font-size:16px;} /* iOS zoom fix */
}
</style>

<!-- ================= HEADER ================= -->
<div class="header">
  <h1>Register New Member</h1>
  <div class="user-info">👤 {{ $user->username ?? $user->name }}</div>
</div>

<!-- ================= CARD ================= -->
<div class="card">
<h2>Member Registration Form</h2>

@if(session('success'))
  <div class="alert-success">{{ session('success') }}</div>
@endif

@if ($errors->any())
  <div style="background:#3a0f0f;border:1px solid #ff5a5a;padding:12px;border-radius:8px;margin-bottom:16px;">
    <ul style="margin:0;padding-left:18px;color:#ffb3b3;">
      @foreach ($errors->all() as $error)
        <li>{{ $error }}</li>
      @endforeach
    </ul>
  </div>
@endif

<form method="POST" action="{{ route('member.store') }}">
@csrf

<div class="form-group">
  <label>Sponsor ID</label>
  <input type="text" value="{{ $user->id }}" readonly>
</div>

<div class="form-group">
  <label>Sponsor Name</label>
  <input type="text" value="{{ $user->name }}" readonly>
</div>

<div class="form-group">
  <label>Position (Leg)</label>
  <select name="position" required>
    <option value="">Select Leg</option>
    <option value="left">Left</option>
    <option value="right">Right</option>
  </select>
</div>

<div class="form-group">
  <label>Username</label>
  <input type="text" name="username" required>
</div>

<div class="form-group">
  <label>Name</label>
  <input type="text" name="name" required>
</div>

<div class="form-group">
  <label>Email</label>
  <input type="email" name="email" required>
</div>

<div class="form-group">
  <label>Mobile</label>
  <input type="text" name="mobile" required pattern="[0-9]{10,11}">
</div>

<div class="form-group">
  <label>PAN Number (optional)</label>
  <input type="text" name="pan_number">
</div>

<div class="form-group">
  <label>State</label>
  <select name="state" required>
    <option value="">Select State</option>
    <option>Delhi</option>
    <option>Punjab</option>
    <option>Rajasthan</option>
    <option>Himachal Pradesh</option>
    <option>Uttar Pradesh</option>
    <option>Maharashtra</option>
    <option>Gujarat</option>
    <option>Haryana</option>
    <option>Karnataka</option>
    <option>Tamil Nadu</option>
  </select>
</div>

<div class="form-group">
  <label>City</label>
  <input type="text" name="city">
</div>

<div class="form-group">
  <label>Password</label>
  <input type="password" name="password" required>
</div>

<div class="form-group">
  <label>Confirm Password</label>
  <input type="password" name="password_confirmation" required>
</div>

<button type="submit" class="btn-submit">Register Member</button>
</form>
</div>

@endsection
