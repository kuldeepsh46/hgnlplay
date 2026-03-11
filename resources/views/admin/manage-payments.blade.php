@extends('common.layout')
@section('title', 'Edit User')
@section('main')
<style>


body {
    margin: 0;
    font-family: "Inter", sans-serif;
    background: var(--bg);
    color: var(--text);
    display: flex;
 
}


.card {
    background: var(--card);
    border: 1px solid #1f2832;
    border-radius: var(--radius);
    padding: 20px;
    margin-bottom: 24px;
}

.tabs {
    display: flex;
    gap: 10px;
    margin-bottom: 16px;
}

.tab-btn {
    padding: 10px 18px;
    border: none;
    border-radius: 8px;
    cursor: pointer;
    font-weight: 600;
}

.tab-btn.active {
    background: var(--accent);
    color: #000;
}

.table-wrap {
    overflow-x: auto;
}

table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 10px;
}

th,
td {
    border: 1px solid #1e2b36;
    padding: 10px;
    text-align: center;
    font-size: 14px;
}

th {
    background: #161f29;
    color: #a9b9c7;
}

td {
    color: #d4dee8;
}

.btn-action {
    padding: 6px 10px;
    border: none;
    border-radius: 6px;
    cursor: pointer;
    font-weight: 600;
}

.btn-approve {
    background:var(--accent);
    color: #000;
}

.btn-reject {
    background: #ff4a4a;
    color: #fff;
}

.filter-form {
    display: flex;
    gap: 10px;
    flex-wrap: wrap;
    margin-bottom: 10px;
}

input[type=date] {
    padding: 8px;
    border-radius: 6px;
    background: #141c22;
    border: 1px solid #1f2832;
    color: #fff;
}

/* ===== Attachment View Modal ===== */
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
    padding: 20px;
    max-width: 90%;
    max-height: 90%;
    overflow: auto;
    position: relative;
}

.modal-content img,
.modal-content iframe {
    width: 100%;
    height: auto;
    max-height: 80vh;
    border-radius: 8px;
}

.modal-close {
    position: absolute;
    top: 10px;
    right: 20px;
    font-size: 24px;
    color: var(--muted);
    cursor: pointer;
    font-weight: bold;
}

.btn-view {
    background: #2ee6a6;
    color: #000;
}

.btn-view:hover {
    opacity: 0.9;
}

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

td form button {
    display: block;
    width: 80px;
    margin: 3px 0px;
}
</style>

<div class="header">
    <h1>Manage Payments</h1>
    <div class="user-info">👤 {{ Auth::user()->username ?? Auth::user()->name }}</div>
</div>

<!-- Attachment Modal -->
<div id="attachmentModal" class="modal">
    <div class="modal-content">
        <span class="modal-close" onclick="closeModal()">&times;</span>
        <div id="attachmentPreview"></div>
    </div>
</div>



<div class="card">
    <form method="GET" class="filter-form">
        <div><label>From:</label><input type="date" name="from" value="{{ $r->from }}"></div>
        <div><label>To:</label><input type="date" name="to" value="{{ $r->to }}"></div>
        <button class="tab-btn active" type="submit">Filter</button>
        <a href="{{ route('admin.payments') }}" class="tab-btn" style="background:#333;color:#fff;">Reset</a>
    </form>

    <div class="tabs">
        <button class="tab-btn active" id="tabAll">All</button>
        <button class="tab-btn" id="tabPending">Pending</button>
        <button class="tab-btn" id="tabCompleted">Completed</button>
        <button class="tab-btn" id="tabRejected">Rejected</button>
    </div>

    <div id="allTab" class="table-wrap">@include('admin.partials.payment-table',['data'=>$all])</div>
    <div id="pendingTab" class="table-wrap" style="display:none;">
        @include('admin.partials.payment-table',['data'=>$pending,'pending'=>true])</div>
    <div id="completedTab" class="table-wrap" style="display:none;">
        @include('admin.partials.payment-table',['data'=>$completed])</div>
    <div id="rejectedTab" class="table-wrap" style="display:none;">
        @include('admin.partials.payment-table',['data'=>$rejected])</div>
</div>



<!-- Password Change Modal -->
<div id="passwordModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeModales()">&times;</span>
        <h2>Change Password</h2>

        <form id="passwordForm" method="POST" action="{{ route('changep.update') }}">
            @csrf
            <div class="form-group">
                <label for="new_password">New Password</label>
                <input type="password" name="new_password" id="new_password" required minlength="6">
            </div>
            <div class="form-group">
                <label for="new_password_confirmation">Confirm Password</label>
                <input type="password" name="new_password_confirmation" id="new_password_confirmation" required
                    minlength="6">
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
document.getElementById("passwordForm").addEventListener("submit", function(e) {
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

function closeModales() {
    passwordModal.style.display = "none";
}

// Close when clicking outside modal
window.onclick = function(e) {
    if (e.target === passwordModal) {
        closeModales();
    }
};
</script>

<script>
const tabs = ['All', 'Pending', 'Completed', 'Rejected'];
tabs.forEach(name => {
    document.getElementById('tab' + name).onclick = () => {
        tabs.forEach(n => {
            document.getElementById(n.toLowerCase() + 'Tab').style.display = (n === name) ?
                'block' : 'none';
            document.getElementById('tab' + n).classList.toggle('active', n === name);
        });
    };
});
</script>

<script>
function viewAttachment(url) {
    const modal = document.getElementById('attachmentModal');
    const preview = document.getElementById('attachmentPreview');
    const ext = url.split('.').pop().toLowerCase();

    if (['jpg', 'jpeg', 'png', 'gif', 'webp'].includes(ext)) {
        preview.innerHTML = `<img src="${url}" alt="Attachment">`;
    } else if (['pdf'].includes(ext)) {
        preview.innerHTML = `<iframe src="${url}" frameborder="0"></iframe>`;
    } else {
        preview.innerHTML = `<p><a href="${url}" target="_blank">Open File</a></p>`;
    }

    modal.style.display = 'flex';
}

function closeModal() {
    document.getElementById('attachmentModal').style.display = 'none';
}
window.onclick = function(e) {
    const modal = document.getElementById('attachmentModal');
    if (e.target === modal) closeModal();
};










document.getElementById('logout-link').addEventListener('click', function(e) {
    e.preventDefault();
    document.getElementById('logout-form').submit();
});
</script>

@endsection