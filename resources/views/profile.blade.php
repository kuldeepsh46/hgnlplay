@extends('common.layout')
@section('title', 'My Profile')

@section('main')

<!-- ================= PROFILE HEADER ================= -->
<div class="profile-header">
    <h1>My Profile</h1>
    <span class="profile-user">👤 {{ $user->username ?? $user->name }}</span>
</div>

<!-- ================= PROFILE CARD ================= -->
<div class="profile-card">

    <h2 class="card-title">Account Information</h2>

    <div class="profile-grid">
        <div class="profile-item">
            <label>Username</label>
            <p>{{ $user->username }}</p>
        </div>

        <div class="profile-item">
            <label>Full Name</label>
            <p>{{ $user->name }}</p>
        </div>

        <div class="profile-item">
            <label>Email</label>
            <p>{{ $user->email }}</p>
        </div>

        <div class="profile-item">
            <label>Mobile</label>
            <p>{{ $user->mobile }}</p>
        </div>

        <div class="profile-item">
            <label>PAN Number</label>
            <p>{{ $user->pan_number }}</p>
        </div>

        <div class="profile-item">
            <label>State</label>
            <p>{{ $user->state }}</p>
        </div>

        <div class="profile-item">
            <label>City</label>
            <p>{{ $user->city }}</p>
        </div>

        <div class="profile-item">
            <label>Sponsor ID</label>
            <p>{{ $user->sponsor_id }}</p>
        </div>

        <div class="profile-item">
            <label>Sponsor Name</label>
            <p>{{ $user->sponsor_name }}</p>
        </div>

        <div class="profile-item">
            <label>Position</label>
            <p>{{ ucfirst($user->position) }}</p>
        </div>

        <div class="profile-item highlight">
            <label>Wallet Balance</label>
            <p>₹ {{ number_format($walletBalance, 2) }}</p>
        </div>
    </div>

    <div class="profile-actions">
        <a href="#" id="openPasswordModal" class="btn-primary">Change Password</a>
    </div>
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
                <input type="password" name="new_password" id="new_password" required minlength="6">
            </div>

            <div class="form-group">
                <label>Confirm Password</label>
                <input type="password" name="new_password_confirmation" id="new_password_confirmation" required minlength="6">
            </div>

            <button type="submit" class="btn-primary" style="width:100%;">Update Password</button>
        </form>

        @if (session('success'))
            <p class="success-msg">{{ session('success') }}</p>
        @endif

        @if (session('error'))
            <p class="error-msg">{{ session('error') }}</p>
        @endif
    </div>
</div>

<!-- ================= CSS ================= -->
<style>
/* Layout */
.profile-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
}

.profile-header h1 {
    font-size: 26px;
}

.profile-user {
    color: var(--muted);
    font-size: 14px;
}

/* Card */
.profile-card {
    background: var(--card);
    border: 1px solid var(--border);
    border-radius: 16px;
    padding: 24px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.35);
}

.card-title {
    font-size: 22px;
    margin-bottom: 20px;
    color:#fff;
}

/* Grid */
.profile-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 16px;
}

.profile-item {
    background: #0f1620;
    border: 1px solid #2a3442;
    border-radius: 10px;
    padding: 14px;
}

.profile-item label {
    font-size: 12px;
    color: var(--muted);
    margin-bottom: 6px;
    display: block;
}

.profile-item p {
    margin: 0;
    font-size: 14px;
    font-weight: 500;
}

.profile-item.highlight {
    background: linear-gradient(135deg, #ff9800, #e68900);
    border: none;
}

.profile-item.highlight label,
.profile-item.highlight p {
    color: #000;
    font-weight: 600;
}

/* Actions */
.profile-actions {
    margin-top: 24px;
    text-align: right;
}

.btn-primary {
    background: var(--accent);
    color: #000;
    padding: 10px 18px;
    border-radius: 8px;
    font-weight: 600;
    text-decoration: none;
    border: none;
    cursor: pointer;
}

.btn-primary:hover {
    opacity: 0.9;
}

/* Modal */
.modal {
    display: none;
    position: fixed;
    inset: 0;
    background: rgba(0,0,0,0.85);
    justify-content: center;
    align-items: center;
    z-index: 999;
}

.modal-content {
    background: var(--card);
    border: 1px solid var(--border);
    padding: 24px;
    border-radius: 16px;
    width: 90%;
    max-width: 400px;
    position: relative;
}

.modal-content h2 {
    text-align: center;
    color: var(--accent);
}

.close {
    position: absolute;
    top: 10px;
    right: 14px;
    font-size: 22px;
    cursor: pointer;
}

/* Form */
.form-group {
    margin-bottom: 16px;
}

.form-group label {
    font-size: 14px;
    color: var(--muted);
    margin-bottom: 6px;
    display: block;
}

.form-group input {
    width: 100%;
    padding: 10px;
    border-radius: 8px;
    border: 1px solid #2a3442;
    background: #0f1620;
    color: #fff;
}

/* Messages */
.success-msg {
    color: #a7ff1e;
    text-align: center;
    margin-top: 10px;
}

.error-msg {
    color: #ff5555;
    text-align: center;
    margin-top: 10px;
}

/* Responsive */
@media (max-width: 992px) {
    .profile-grid {
        grid-template-columns: repeat(2, 1fr);
    }
}

@media (max-width: 576px) {
    /* .profile-header {
        flex-direction: column;
        align-items: flex-start;
        gap: 6px;
    } */
        .profile-header h1 {
    font-size: 23px;
    margin: 0px;
}
.profile-item {
    padding: 9px;
}
    .profile-grid {
        grid-template-columns: 1fr;    gap: 9px;
    }

    .profile-actions {
        text-align: center;
    }
    .profile-card{
      padding:10px;
    }
}
</style>

<!-- ================= JS ================= -->
<script>
const passwordModal = document.getElementById("passwordModal");
const openPasswordModal = document.getElementById("openPasswordModal");

openPasswordModal.addEventListener("click", function(e){
    e.preventDefault();
    passwordModal.style.display = "flex";
});

function closeModal() {
    passwordModal.style.display = "none";
}

window.onclick = function(e) {
    if (e.target === passwordModal) closeModal();
};

// Password match validation
document.getElementById("passwordForm").addEventListener("submit", function(e){
    const p1 = document.getElementById("new_password").value;
    const p2 = document.getElementById("new_password_confirmation").value;

    if (p1 !== p2) {
        e.preventDefault();
        alert("Passwords do not match!");
    }
});
</script>

@endsection
