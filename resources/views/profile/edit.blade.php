@extends('common.layout')
@section('title', 'Update Profile')
@section('main')

<style>


/* Layout helpers */

.user-info{
  background:#141c22;
  padding:8px 14px;
  border-radius:999px;
  color:var(--accent);
  font-weight:600;
  font-size:14px;
}

/* Card */
.card{
  background:var(--card);
  border:1px solid var(--border);
  border-radius:var(--radius);
  padding:22px;
  margin-bottom:24px;
  box-shadow:0 10px 30px rgba(0,0,0,.35);
}

/* Section title */
.section-title{
  font-size:15px;
  font-weight:600;
  color:#fff;
  background:var(--accent);
  padding:8px 14px;
  border-radius:8px;
  display:inline-flex;
  align-items:center;
  gap:8px;
  margin-bottom:18px;
}

/* Form grid */
.form-grid{
  display:grid;
  grid-template-columns:repeat(3,1fr);
  gap:18px;
}
.form-group{
  display:flex;
  flex-direction:column;
}
.form-group label{
  font-size:13px;
  color:var(--muted);
  margin-bottom:6px;
}
.form-group input,
.form-group textarea,
.form-group select{
  background:#141c22;
  border:1px solid var(--border);
  border-radius:10px;
  padding:10px 12px;
  color:#fff;
  font-size:14px;
}
.form-group.full{
  grid-column:1/-1;
}
textarea{resize:none}

/* Radio */
.form-group.full label{
  display:inline-flex;
  align-items:center;
  gap:6px;
  margin-right:18px;
  font-size:14px;
}

/* Footer */
.form-footer{
  display:flex;
  justify-content:flex-end;
  margin-top:24px;
}

/* Button */
.btn-primary{
  background:var(--accent);
  color:#000;
  font-weight:600;
  border:none;
  border-radius:10px;
  padding:12px 34px;
  cursor:pointer;
  transition:.25s;
}
.btn-primary:hover{
  opacity:.9;
  transform:translateY(-1px);
}

/* Checkbox */
.checkbox-group{
  display:flex;
  align-items:center;
  gap:10px;
  margin-top:10px;
  color:var(--muted);
}

/* ===== Password Modal ===== */
.modal{
  display:none;
  position:fixed;
  inset:0;
  background:rgba(11,14,18,.9);
  backdrop-filter:blur(4px);
  z-index:999;
  justify-content:center;
  align-items:center;
}
.modal-content{
  background:var(--card);
  border:1px solid var(--border);
  border-radius:var(--radius);
  padding:24px;
  width:90%;
  max-width:400px;
  box-shadow:0 0 30px rgba(0,0,0,.6);
  position:relative;
}
.modal-content h2{
  text-align:center;
  color:var(--accent);
  margin-bottom:20px;
}
.close{
  position:absolute;
  right:16px;
  top:10px;
  font-size:22px;
  cursor:pointer;
  color:var(--muted);
}

/* Messages */
.success-msg{color:#a7ff1e;text-align:center;margin-top:10px;}
.error-msg{color:#ff5555;text-align:center;margin-top:10px;}

/* Responsive */
@media(max-width:992px){
  .form-grid{grid-template-columns:repeat(2,1fr);}
}
@media(max-width:576px){
  .header{align-items:flex-start;gap:8px;}
  .form-grid{grid-template-columns:1fr;}
  .form-footer{justify-content:start;}
}
input[type="date"] {
    background-color: #141c22;
    color: #ffffff;
    border: 1px solid #1f2832;
}

input[type="date"]::-webkit-calendar-picker-indicator {
    filter: invert(1);
    opacity: 0.9;
}

</style>

<div class="header">
  <h1>Update Profile</h1>
  <div class="user-info">👤 {{ $user->username ?? $user->name }}</div>
</div>

<form method="POST" action="{{ route('profile.update') }}">
@csrf

<!-- Profile Detail -->
<div class="card">
  <div class="section-title">👤 Profile Detail</div>
  <div class="form-grid">
    <div class="form-group"><label>Name*</label><input type="text" name="name" value="{{ old('name',$user->name) }}"></div>
    <div class="form-group"><label>Mobile No*</label><input type="text" name="mobile" value="{{ old('mobile',$user->mobile) }}"></div>
    <div class="form-group"><label>Email</label><input type="email" name="email" value="{{ old('email',$user->email) }}"></div>
    <div class="form-group"><label>Date of Birth</label><input type="date" name="dob" value="{{ old('dob',$user->dob) }}"></div>
    <div class="form-group"><label>PAN Card Number</label><input type="text" name="pan_number" value="{{ old('pan_number',$user->pan_number) }}"></div>
    <div class="form-group"><label>State*</label><input type="text" name="state" value="{{ old('state',$user->state) }}"></div>
    <div class="form-group"><label>City*</label><input type="text" name="city" value="{{ old('city',$user->city) }}"></div>
    <div class="form-group"><label>Pin Code*</label><input type="text" name="pincode" value="{{ old('pincode',$user->pincode) }}"></div>
    <div class="form-group full"><label>Address</label><textarea rows="2" name="address">{{ old('address',$user->address) }}</textarea></div>
  </div>
</div>

<!-- Account Detail -->
<div class="card">
  <div class="section-title">🏦 Account Detail</div>
  <div class="form-grid">
    <div class="form-group"><label>Account Number</label><input type="text" name="account_number" value="{{ old('account_number',$user->account_number) }}"></div>
    <div class="form-group"><label>Account Holder Name</label><input type="text" name="account_holder_name" value="{{ old('account_holder_name',$user->account_holder_name) }}"></div>
    <div class="form-group"><label>Bank Name</label><input type="text" name="bank_name" value="{{ old('bank_name',$user->bank_name) }}"></div>
    <div class="form-group"><label>Branch Name</label><input type="text" name="branch_name" value="{{ old('branch_name',$user->branch_name) }}"></div>
    <div class="form-group"><label>Bank Address</label><input type="text" name="bank_address" value="{{ old('bank_address',$user->bank_address) }}"></div>
    <div class="form-group"><label>IFSC Code</label><input type="text" name="ifsc_code" value="{{ old('ifsc_code',$user->ifsc_code) }}"></div>
  </div>
</div>

<!-- Nominee -->
<div class="card">
  <div class="section-title">🧍 Nominee Detail</div>
  <div class="form-grid">
    <div class="form-group"><label>Nominee Name</label><input type="text" name="nominee_name" value="{{ old('nominee_name',$user->nominee_name) }}"></div>
    <div class="form-group"><label>Relation</label><input type="text" name="relation" value="{{ old('relation',$user->relation) }}"></div>
    <div class="form-group"><label>Date of Birth</label><input type="date" name="nominee_dob" value="{{ old('nominee_dob',$user->nominee_dob) }}"></div>
    <div class="form-group full">
      <label><input type="radio" name="nominee_gender" value="male" {{ old('nominee_gender',$user->nominee_gender)=='male'?'checked':'' }}> Male</label>
      <label><input type="radio" name="nominee_gender" value="female" {{ old('nominee_gender',$user->nominee_gender)=='female'?'checked':'' }}> Female</label>
    </div>
  </div>
</div>

<!-- Login Access -->
<div class="card">
  <div class="section-title">🔐 Login Access</div>
  <div class="checkbox-group">
    <input type="checkbox" required> I accept terms & conditions
  </div>
  <div class="form-footer">
    <button class="btn-primary">Update Profile</button>
  </div>
</div>

</form>

@endsection
