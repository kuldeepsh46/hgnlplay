@extends('common.layout')
@section('title', 'Support Request')
@section('main')
<style>


body {
    margin: 0;
    font-family: "Inter", sans-serif;
    background: var(--bg);
    color: var(--text);
    display: flex;
    min-height: 100vh;
}

.header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
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
    overflow-x: auto;
}
.tab-btn {
    padding: 10px 18px;
    border: none;
    color:#000;
    border-radius: 8px;
    cursor: pointer;
    font-weight: 600;    white-space: nowrap;
}


.tab-btn.active {
    background: var(--accent);
    color: #ffffffff;
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

.btn-view {
    background: var(--accent);
    color: #ffffffff;
    border: none;
    padding: 5px 10px;
    border-radius: 6px;
    cursor: pointer;
    font-weight: 600;
}

.modal {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.7);
    justify-content: center;
    align-items: center;
}

.modal-content {
    background: var(--card);
    padding: 20px;
    border-radius: var(--radius);
    width: 500px;
    max-width: 90%;
    box-shadow: 0 0 20px #00000080;
}

.modal-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 10px;
}

.close {
    cursor: pointer;
    font-size: 20px;
    color: var(--muted);
}

textarea {
    width: 100%;
    height: 100px;
    background: #141c22;
    border: 1px solid #1f2832;
    border-radius: 8px;
    color: #fff;
    padding: 8px;
    margin-top: 6px;
}

button.send-btn {
    background: var(--accent);
    color: #000;
    border: none;
    padding: 8px 14px;
    border-radius: 6px;
    margin-top: 10px;
    font-weight: 600;
    cursor: pointer;
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
</style>

<div class="header">
    <h1>Support Request</h1>
    <div class="user-info">👤 {{ Auth::user()->username ?? Auth::user()->name }}</div>
</div>

<div class="card">
    <div class="tabs">
        <button class="tab-btn active" id="tabAll">All</button>
        <button class="tab-btn" id="tabPending">Yet to Respond</button>
        <button class="tab-btn" id="tabResponded">Responded</button>
    </div>

    <div id="allTab">@include('admin.partials.support-table', ['data'=>$all])</div>
    <div id="pendingTab" style="display:none;">@include('admin.partials.support-table', ['data'=>$pending])</div>
    <div id="respondedTab" style="display:none;">@include('admin.partials.support-table', ['data'=>$responded])</div>
</div>


<!-- Password Change Modal -->
<div id="passwordModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeModaleses()">&times;</span>
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
            <button type="submit" class="btn-view" style="width:100%;">Update Password</button>
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
        item.parentElement.addEventListener("click", openModales);
    }
});

function openModales() {
    passwordModal.style.display = "flex";
}

function closeModaleses() {
    passwordModal.style.display = "none";
}

// Close when clicking outside modal
window.onclick = function(e) {
    if (e.target === passwordModal) {
        closeModaleses();
    }
};
</script>

<script>
// Handle Laravel logout securely
document.getElementById('logout-link').addEventListener('click', function(e) {
    e.preventDefault();
    document.getElementById('logout-form').submit();
});
</script>
</main>

<!-- 🧩 Modal -->
<div class="modal" id="messageModal">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Query Details</h3>
            <span class="close" onclick="closeModal()">×</span>
        </div>
        <p><strong>Subject:</strong> <span id="modalSubject"></span></p>
        <p><strong>Message:</strong></p>
        <p id="modalMessage" style="background:#141c22;padding:10px;border-radius:6px;"></p>
        <form id="replyForm" method="POST">
            @csrf
            <label>Response by Admin</label>
            <textarea name="admin_reply" id="modalReply" placeholder="Type your reply..."></textarea>
            <button class="send-btn">Send</button>
        </form>
    </div>
</div>

<script>
const tabs = ['All', 'Pending', 'Responded'];
tabs.forEach(name => {
    document.getElementById('tab' + name).onclick = () => {
        tabs.forEach(n => {
            document.getElementById(n.toLowerCase() + 'Tab').style.display = (n === name) ?
                'block' : 'none';
            document.getElementById('tab' + n).classList.toggle('active', n === name);
        });
    };
});

function openModal(id) {
    fetch(`/support/message/${id}`)
        .then(r => r.json())
        .then(data => {
            document.getElementById('modalSubject').innerText = data.subject;
            document.getElementById('modalMessage').innerText = data.message;
            document.getElementById('modalReply').value = data.admin_reply ?? '';
            document.getElementById('replyForm').action = `/support/reply/${data.id}`;
            document.getElementById('messageModal').style.display = 'flex';
        });
}

function closeModal() {
    document.getElementById('messageModal').style.display = 'none';
}
</script>

@endsection