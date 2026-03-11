@extends('common.layout')
@section('title', 'Upload KYC')
@section('main')

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

<style>
/* ================= ROOT THEME ================= */


/* ================= GLOBAL FIX ================= */


* {
  box-sizing: border-box;
  max-width: 100%;
}

body{
  background:var(--bg);
  color:var(--text);
  font-family:Inter,sans-serif;
}

/* ================= HEADER ================= */
.header{
  display:flex;
  justify-content:space-between;
  align-items:center;
  margin-bottom:20px;
  gap:10px;
}
.header h1{font-size:22px;}
.user-info{
  background:#141c22;
  padding:8px 14px;
  border-radius:999px;
  color:var(--accent);
  font-weight:600;
  white-space:nowrap;
}

/* ================= CARD ================= */
.card{
  background:var(--card);
  border:1px solid var(--border);
  border-radius:var(--radius);
  padding:22px;
  box-shadow:0 10px 30px rgba(0,0,0,.45);
  margin-bottom:24px;
  width:100%;
}

/* ================= SECTION TITLE ================= */
.section-title{
  font-size:16px;
  font-weight:600;
  color:var(--accent);
  margin-bottom:16px;
  display:flex;
  align-items:center;
  gap:8px;
}

/* ================= FORM GRID ================= */
.form-grid{
  display:grid;
  grid-template-columns:repeat(auto-fit,minmax(220px,1fr));
  gap:18px;
  width:100%;
}

/* ================= FORM GROUP ================= */
.form-group label{
  font-size:13px;
  color:var(--muted);
  margin-bottom:6px;
  display:block;
}

.form-group input[type="file"]{
  width:100%;
  background:#141c22;
  border:1px dashed #2a3442;
  padding:12px;
  border-radius:10px;
  color:#fff;
  cursor:pointer;
}

/* File button */
.form-group input[type="file"]::file-selector-button{
  background:var(--accent);
  color:#fff;
  border:none;
  padding:6px 14px;
  border-radius:6px;
  margin-right:10px;
  cursor:pointer;
  font-weight:600;
}

/* ================= FOOTER ================= */
.form-footer{
  margin-top:22px;
  text-align:right;
}

.btn-primary{
  background:var(--accent);
  color:#000;
  border:none;
  border-radius:10px;
  padding:12px 32px;
  font-weight:700;
  cursor:pointer;
}

.btn-primary:hover{opacity:.9}

/* ================= KYC PREVIEW ================= */
.kyc-preview{
  margin-top:22px;
  background:#0f1620;
  border:1px solid var(--border);
  border-radius:12px;
  padding:16px;
}

.kyc-preview h3{
  font-size:15px;
  margin-bottom:10px;
}

.kyc-preview ul{
  list-style:none;
  padding:0;
  margin:0;
}

.kyc-preview li{
  display:flex;
  align-items:center;
  gap:10px;
  padding:8px 0;
  border-bottom:1px dashed #222c36;
  flex-wrap:wrap;
}

.kyc-preview li:last-child{border-bottom:none}

.kyc-preview a{
  color:var(--accent);
  font-weight:600;
  text-decoration:none;
}

.kyc-preview a:hover{text-decoration:underline}

/* ================= SUCCESS ALERT ================= */
.alert-success{
  background:var(--accent);
  color:#000;
  padding:12px 16px;
  border-radius:10px;
  font-weight:600;
  margin-bottom:16px;
}

/* ================= MODAL ================= */
.modal{
  display:none;
  position:fixed;
  inset:0;
  background:rgba(0,0,0,.85);
  justify-content:center;
  align-items:center;
  z-index:999;
}

.modal-content{
  background:var(--card);
  border:1px solid var(--border);
  padding:22px;
  border-radius:16px;
  width:90%;
  max-width:400px;
  position:relative;
}

.modal-content h2{
  text-align:center;
  color:var(--accent);
}

.close{
  position:absolute;
  top:10px;
  right:14px;
  font-size:22px;
  cursor:pointer;
}

/* ================= MOBILE FIX ================= */
@media(max-width:768px){
  .header{
    flex-direction:column;
    align-items:flex-start;
  }

  .card{
    padding:16px;
  }

  .form-footer{
    text-align:center;
  }

  .btn-primary{
    width:100%;
  }
}
</style>

<!-- ================= HEADER ================= -->
<div class="header">
  <h1>Upload KYC Documents</h1>
  <div class="user-info">👤 {{ $user->username ?? $user->name }}</div>
</div>

@if(session('success'))
  <div class="alert-success">✅ {{ session('success') }}</div>
@endif

<!-- ================= FORM ================= -->
<div class="card">
<form method="POST" action="{{ route('profile.kyc.upload') }}" enctype="multipart/form-data">
@csrf

<div class="section-title">📄 Upload Documents</div>

<div class="form-grid">
  <div class="form-group">
    <label>ID Proof *</label>
    <input type="file" name="id_proof">
  </div>

  <div class="form-group">
    <label>Address Proof</label>
    <input type="file" name="address_proof">
  </div>

  <div class="form-group">
    <label>Account Proof</label>
    <input type="file" name="account_proof">
  </div>
</div>

@if($user->id_proof || $user->address_proof || $user->account_proof)
<div class="kyc-preview">
  <h3>📂 Uploaded Documents</h3>
  <ul>
    @if($user->id_proof)
      <li><i class="fa fa-id-badge"></i> ID Proof <a href="{{ url('public/'.$user->id_proof) }}" target="_blank">View</a></li>
    @endif
    @if($user->address_proof)
      <li><i class="fa fa-home"></i> Address Proof <a href="{{ url('public/'.$user->address_proof) }}" target="_blank">View</a></li>
    @endif
    @if($user->account_proof)
      <li><i class="fa fa-bank"></i> Account Proof <a href="{{ url('public/'.$user->account_proof) }}" target="_blank">View</a></li>
    @endif
  </ul>
</div>
@endif

<div class="form-footer">
  <button type="submit" class="btn-primary">Upload Documents</button>
</div>

</form>
</div>

<!-- ================= PASSWORD MODAL ================= -->
<div id="passwordModal" class="modal">
  <div class="modal-content">
    <span class="close" onclick="closeModal()">&times;</span>
    <h2>Change Password</h2>

    <form id="passwordForm" method="POST" action="{{ route('changep.update') }}">
      @csrf
      <div class="form-group">
        <label>New Password</label>
        <input type="password" id="new_password" name="new_password" required>
      </div>
      <div class="form-group">
        <label>Confirm Password</label>
        <input type="password" id="new_password_confirmation" name="new_password_confirmation" required>
      </div>
      <button type="submit" class="btn-primary" style="width:100%">Update Password</button>
    </form>
  </div>
</div>

<script>
const passwordModal=document.getElementById("passwordModal");

function openModal(){passwordModal.style.display="flex";}
function closeModal(){passwordModal.style.display="none";}

window.onclick=e=>{if(e.target===passwordModal)closeModal();}

document.getElementById("passwordForm").addEventListener("submit",function(e){
  if(new_password.value!==new_password_confirmation.value){
    e.preventDefault();
    alert("Passwords do not match!");
  }
});
</script>

@endsection
