@extends('common.layout')
@section('title', 'Manage Users')
@section('main')
<style>



body {
    margin: 0;
    font-family: "Inter", sans-serif;
    background: var(--bg);
    color: var(--text);
    display: flex;
   
}
.user-info {
    background: #141c22;
    padding: 8px 14px;
    border-radius: 999px;
    color: var(--accent);
    font-weight: 600;
}

.card {
    background: var(--card);
    border: 1px solid #1f2832;
    border-radius: var(--radius);
    padding: 20px;
    box-shadow: 0 0 20px #00000050;
    margin-bottom: 24px;
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

.btn {
    padding: 8px 14px;
    border: none;
    border-radius: 8px;
    cursor: pointer;
    font-weight: 600;
    transition: .25s;
}

.btn-edit {
    background: var(--accent);
    color: #000;
}

.btn-delete {
    background: #ff4444;
    color: #fff;
}

.btn:hover {
    opacity: .9;
}

.username-link {
    color: var(--accent);
    font-weight: 600;
    text-decoration: none;
}

.username-link:hover {
    text-decoration: underline;
}

.pagination {
    display: flex;
    gap: 8px;
    justify-content: center;
    margin-top: 20px;
}

.pagination a,
.pagination span {
    padding: 6px 12px;
    border: 1px solid #1f2832;
    border-radius: 6px;
    color: var(--text);
    text-decoration: none;
}

.pagination .active span {
    background: var(--accent);
    color: #000;
}

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
/* ===============================
   SHOW ONLY « Previous / Next »
   HIDE PAGE NUMBERS
================================ */

/* Hide page numbers block */
nav[aria-label="Pagination Navigation"] > div:last-child {
    display: none !important;
}

/* Keep Previous / Next visible */
nav[aria-label="Pagination Navigation"] > div:first-child {
    display: flex !important;
    justify-content: center !important;
    align-items: center !important;
    gap: 12px;padding-top: 20px;
}

/* Style Previous / Next buttons */
nav[aria-label="Pagination Navigation"] a {
    color: var(--accent) !important;
    font-size: 14px !important;
    font-weight: 600;
    padding: 6px 12px !important;
    border-radius: 6px;
}

/* Disabled Previous / Next */
nav[aria-label="Pagination Navigation"] span[aria-disabled="true"] {
    opacity: 0.4;
    cursor: not-allowed;
}

</style>


<div class="header">
    <h1>Manage Users</h1>
    <div class="user-info">👤 {{ Auth::user()->username ?? Auth::user()->name }}</div>
</div>

<div class="card table-res">
    <!--<h2>All Users</h2>-->
    <div style="display:flex; justify-content:space-between; align-items:center;">
    <h2>All Users</h2>

    <input type="text" id="userSearch"
        placeholder="Search Username, Email or Mobile..."
        style="padding:8px 12px;border-radius:6px;border:1px solid #2a3442;background:#0f1620;color:#fff;width:280px;">
</div>
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>User Name</th>
                    <th>Email</th>
                    <th>Mobile</th>
                    <th>Wallet Balance</th>
                    <th>Withdraw (Completed)</th>
                    <th>Withdraw (Pending)</th>
                    <th>First Package</th>
                    <th>Joining Amount</th>
                    <th>Joined On</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $index => $u)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>
                        <a href="{{ route('tree.view', ['id' => $u->id]) }}"
                            class="username-link">{{ $u->username ?? $u->name }}</a>
                    </td>
                    <td>{{ $u->email }}</td>
                    <td>{{ $u->mobile ?? 'N/A' }}</td>
                    <td>₹{{ number_format(DB::table('wallets')->where('user_id',$u->id)->value('balance') ?? 0,2) }}
                    </td>
                    
                    <td style="color:#00ffb3;">
                        ₹{{ number_format($u->withdraw_completed ?? 0,2) }}
                    </td>
                    <td style="color:#ffc107;">
                        ₹{{ number_format($u->withdraw_pending ?? 0,2) }}
                    </td>
                    <td>
                        {{ $u->first_package }}
                    </td>
                    <td>
                        {{ $u->price }}
                    </td>
                    <td>{{ date('d M Y', strtotime($u->created_at)) }}</td>
                    <td>
                        <button class="btn btn-edit" onclick="editUser({{ $u->id }})">Edit</button>
                        <button class="btn" style="background:#ff9800;color:#000;"
                            onclick="openUserPasswordModal({{ $u->id }})">
                            Change Password
                        </button>
                        <button class="btn btn-delete" onclick="deleteUser({{ $u->id }})">Delete</button>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8">No users found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

</div>


<!-- User Password Change Modal -->
<div id="userPasswordModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeUserPasswordModal()">&times;</span>
        <h2>Change User Password</h2>

        <form id="userPasswordForm" method="POST" action="{{ route('admin.user.password.update') }}">
            @csrf
            <input type="hidden" name="user_id" id="password_user_id">

            <div class="form-group">
                <label>New Password</label>
                <input type="password" name="password" required minlength="6">
            </div>

            <div class="form-group">
                <label>Confirm Password</label>
                <input type="password" name="password_confirmation" required minlength="6">
            </div>

            <button type="submit" class="btn btn-edit" style="width:100%;">
                Update Password
            </button>
        </form>
    </div>
</div>

<!-- Password Change Modal -->
<div id="passwordModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeModal()">&times;</span>
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
    const userPasswordModal = document.getElementById("userPasswordModal");
    
    function openUserPasswordModal(userId) {
        document.getElementById("password_user_id").value = userId;
        userPasswordModal.style.display = "flex";
    }
    
    function closeUserPasswordModal() {
        userPasswordModal.style.display = "none";
    }
    
    // Confirm password validation
    document.getElementById("userPasswordForm").addEventListener("submit", function(e) {
        const pass = this.password.value;
        const confirm = this.password_confirmation.value;
    
        if (pass !== confirm) {
            e.preventDefault();
            alert("Passwords do not match!");
        }
    });
</script>

<script>
    document.getElementById("userSearch").addEventListener("keyup", function() {
    
        let value = this.value.toLowerCase();
        let rows = document.querySelectorAll("tbody tr");
    
        rows.forEach(row => {
    
            let username = row.children[1]?.textContent.toLowerCase() || "";
            let email = row.children[2]?.textContent.toLowerCase() || "";
            let mobile = row.children[3]?.textContent.toLowerCase() || "";
    
            if (
                username.includes(value) ||
                email.includes(value) ||
                mobile.includes(value)
            ) {
                row.style.display = "";
            } else {
                row.style.display = "none";
            }
    
        });
    });
</script>

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

function closeModal() {
    passwordModal.style.display = "none";
}

// Close when clicking outside modal
window.onclick = function(e) {
    if (e.target === passwordModal) {
        closeModal();
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


<script>
function editUser(id) {
    window.location.href = "/admin/users/" + id + "/edit";
}

function deleteUser(id) {
    if (confirm("Are you sure you want to delete this user?")) {
        fetch(`/admin/users/${id}`, {
            method: "DELETE",
            headers: {
                "X-CSRF-TOKEN": "{{ csrf_token() }}"
            }
        }).then(r => location.reload());
    }
}
</script>
@endsection