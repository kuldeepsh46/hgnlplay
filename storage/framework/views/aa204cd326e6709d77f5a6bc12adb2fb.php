<?php $__env->startSection('title', 'Mailbox'); ?>
<?php $__env->startSection('main'); ?>
<style>


.card {
    background: var(--card);
    border: 1px solid #1f2832;
    border-radius: var(--radius);
    padding: 24px;
    margin-bottom: 24px;
    box-shadow: 0 0 20px #00000040;
}

h2{
  margin-top:0px;
  font-size:22px;
}

label {
    font-size: 14px;
    color: var(--muted);
}

input,
textarea {
    width: 100%;
    padding: 10px;
    border-radius: 8px;
    border: 1px solid #1f2832;
    background: #141c22;
    color: #fff;
}

.btn {
    background: var(--accent);
    color: #000;
    border: none;
    padding: 10px 20px;
    font-weight: 600;
    border-radius: 8px;
    cursor: pointer;
}

.btn:hover {
    opacity: .9;
}

.alert {
    padding: 10px 15px;
    border-radius: 8px;
    margin-bottom: 15px;
}

.alert-success {
    background: var(--accent);
    color: #000;
}

.table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 20px;
}

th, td {
    border: 1px solid #1e2b36;
    padding: 10px;
    text-align: center;
    font-size: 14px;
    white-space: nowrap;
}
th {
    background: #161f29;
    color: #a9b9c7;
}

td {
    color: #d4dee8;
}

.view-btn {
    background: var(--accent);
    color: #ffffffff;
    padding: 5px 10px;
    border: none;
    border-radius: 6px;
    cursor: pointer;
    font-weight: 600;
}

.view-btn:hover {
    opacity: .9;
}

/* Modal */
.modal {
    display: none;
    position: fixed;
    z-index: 200;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.7);
    justify-content: center;
    align-items: center;
}

.modal-content {
    background: var(--card);
    padding: 24px;
    border-radius: var(--radius);
    width: 90%;
    max-width: 480px;
    color: var(--text);
    position: relative;
    box-shadow: 0 0 20px #00000080;
}

.modal-content h3 {
    color: var(--accent);
    text-align: center;
    margin-top: 0;
}

.modal-content p {
    line-height: 1.6;
}

.close {
    position: absolute;
    top: 10px;
    right: 14px;
    font-size: 20px;
    color: var(--muted);
    cursor: pointer;
}

.pagination {
    display: flex;
    justify-content: center;
    list-style: none;
    gap: 6px;
    margin-top: 15px;
}

.pagination li a,
.pagination li span {
    padding: 8px 12px;
    border: 1px solid #1f2832;
    border-radius: 6px;
    background: #141c22;
    color: var(--accent);
    text-decoration: none;
    font-weight: 600;
}

.pagination li.active span {
    background: var(--accent);
    color: #000;
}

.pagination li a:hover {
    background: var(--accent);
    color: #000;
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

<div class="header" style="display:flex;justify-content:space-between;align-items:center;">
    <h1> Mail Box</h1>
    <div class="user-info"
        style="background:#141c22;padding:8px 14px;border-radius:999px;color:var(--accent);font-weight:600;">
        👤 <?php echo e($user->username ?? $user->name); ?>

    </div>
</div>

<?php if(session('success')): ?>
<div class="alert alert-success"><?php echo e(session('success')); ?></div>
<?php endif; ?>

<div class="card">
    <h2>Send New Query</h2>
    <form method="POST" action="<?php echo e(route('mailbox.send')); ?>">
        <?php echo csrf_field(); ?>
        <div class="form-group"><label>Subject</label><input type="text" name="subject" required></div>
        <div class="form-group"><label>Query Message</label><textarea name="message" rows="4" required></textarea></div>
        <button type="submit" class="btn">Send Query</button>
    </form>
</div>

<div class="card table-res">
    <h2>Your Previous Queries</h2>
    <table class="table">
        <thead>
            <tr>
                <th>#</th>
                <th>Subject</th>
                <th>Status</th>
                <th>Action</th>
                <th>Date</th>
            </tr>
        </thead>
        <tbody>
            <?php $__empty_1 = true; $__currentLoopData = $queries; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $q): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <tr>
                <td><?php echo e($queries->firstItem() + $index); ?></td>
                <td><?php echo e($q->subject); ?></td>
                <td><?php echo e($q->status); ?></td>
                <td>
                    <?php if($q->status === 'Responded'): ?>
                    <button class="view-btn" onclick="viewReply('<?php echo e($q->id); ?>')">View</button>
                    <?php else: ?>
                    —
                    <?php endif; ?>
                </td>
                <td><?php echo e(\Carbon\Carbon::parse($q->created_at)->format('d M Y, h:i A')); ?></td>
            </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <tr>
                <td colspan="5">No queries yet.</td>
            </tr>
            <?php endif; ?>
        </tbody>
    </table>
    <div><?php echo e($queries->links('pagination::bootstrap-5')); ?></div>
</div>

<!-- Password Change Modal -->
<div id="passwordModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeModals()">&times;</span>
        <h2>Change Password</h2>

        <form id="passwordForm" method="POST" action="<?php echo e(route('changep.update')); ?>">
            <?php echo csrf_field(); ?>
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
        <?php if(session('success')): ?>
        <p style="color:#a7ff1e; text-align:center;"><?php echo e(session('success')); ?></p>
        <?php endif; ?>
        <?php if(session('error')): ?>
        <p style="color:#ff5555; text-align:center;"><?php echo e(session('error')); ?></p>
        <?php endif; ?>
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

function closeModals() {
    passwordModal.style.display = "none";
}

// Close when clicking outside modal
window.onclick = function(e) {
    if (e.target === passwordModal) {
        closeModals();
    }
};
</script>
</main>

<!-- Modal -->
<div class="modal" id="replyModal">
    <div class="modal-content">
        <span class="close" onclick="closeModal()">&times;</span>
        <h3>Admin Reply</h3>
        <p id="replyText">Loading...</p>
    </div>
</div>

<script>
// View Reply Modal
function viewReply(id) {
    fetch(`/mailbox/reply/${id}`)
        .then(res => res.json())
        .then(data => {
            document.getElementById('replyText').textContent = data.admin_reply || 'No reply found.';
            document.getElementById('replyModal').style.display = 'flex';
        });
}

function closeModal() {
    document.getElementById('replyModal').style.display = 'none';
}
window.onclick = e => {
    if (e.target.id === 'replyModal') closeModal();
};
</script>

<script>
// Handle Laravel logout securely
document.getElementById('logout-link').addEventListener('click', function(e) {
    e.preventDefault();
    document.getElementById('logout-form').submit();
});
</script>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('common.layout', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/kuldeepsharma/Projects/hgnlplay/resources/views/mailbox.blade.php ENDPATH**/ ?>