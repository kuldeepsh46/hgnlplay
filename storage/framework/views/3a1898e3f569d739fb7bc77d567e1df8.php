<?php $__env->startSection('title', 'Withdraw'); ?>
<?php $__env->startSection('main'); ?>
<style>
  h2{
    margin-top:0px;
    font-size:22px;
  }
.card {
    background: var(--card);
    border: 1px solid #1f2832;
    border-radius: var(--radius);
    padding: 20px;
    margin-bottom: 24px;
    box-shadow: 0 0 20px #00000050;
}

.btn {
    padding: 10px 18px;
    border: none;
    border-radius: 8px;
    cursor: pointer;
    font-weight: 600;
    transition: .25s;
}

.btn-copy {
    background: linear-gradient(90deg, var(--accent),var(--accent));
    color: #000;
}

.btn-copy:hover {
    opacity: .9
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
    white-space: nowrap;
}

th {
    background: #161f29;
    color: #a9b9c7;
}

td {
    color: #d4dee8
}

input {
    width: 100%;
    padding: 10px;
    border-radius: 8px;
    border: 1px solid #2a3442;
    background: #0f1620;
    color: #fff;
}

.alert {
    padding: 10px 15px;
    border-radius: 8px;
    margin-bottom: 15px;
}

.alert-success {
    background: #a7ff1e;
    color: #000;
}

.alert-error {
    background: #ff5555;
    color: #fff;
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
    <h1>Withdrawal Request</h1>
    <div class="user-info">👤 <?php echo e($user->username ?? $user->name); ?></div>
</div>

<?php if(session('success')): ?>
<div class="alert alert-success"><?php echo e(session('success')); ?></div>
<?php endif; ?>
<?php if(session('error')): ?>
<div class="alert alert-error"><?php echo e(session('error')); ?></div>
<?php endif; ?>

<!-- Withdraw Form -->
<div class="card">
    <h2>New Withdrawal Request</h2>
    <p>Available Balance: ₹<?php echo e(number_format($balance, 2)); ?></p>
    <form method="POST" action="<?php echo e(route('withdraw.store')); ?>">
        <?php echo csrf_field(); ?>
        <div class="form-group">
            <label>Amount</label>
            <input type="number" name="amount" placeholder="Enter Withdraw Amount" required>
        </div>
        <div class="form-group">
            <label>Password</label>
            <input type="password" name="password" placeholder="Enter Your Password" required>
        </div>
        <button type="submit" class="btn btn-copy">Submit</button>
    </form>
</div>

<!-- Withdraw History -->
<div class="card">
    <h2>Withdrawal History</h2>
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Total Amount</th>
                    <th>Admin Charges (10%)</th>
                    <th>Net Amount</th>
                    <th>Status</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                <?php $__empty_1 = true; $__currentLoopData = $withdraws; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $w): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr>
                    <td><?php echo e($i+1); ?></td>
                    <td>₹<?php echo e(number_format($w->amount,2)); ?></td>
                    <td style="color:#ff5555;">₹<?php echo e(number_format($w->tax_amount,2)); ?></td>
                    <td style="color:#a7ff1e;">₹<?php echo e(number_format($w->net_amount,2)); ?></td>
                    <td style="color:<?php echo e($w->status == 'completed' ? '#a7ff1e' : '#ffcc00'); ?>">
                        <?php echo e(ucfirst($w->status)); ?>

                    </td>
                    <td><?php echo e(\Carbon\Carbon::parse($w->created_at)->timezone('Asia/Kolkata')->format('d M Y, h:i A')); ?>

                    </td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr>
                    <td colspan="6">No withdrawal history yet.</td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Password Change Modal -->
<div id="passwordModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeModal()">&times;</span>
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
const sidebar = document.getElementById('sidebar');
const toggleBtn = document.getElementById('toggleBtn');
toggleBtn.addEventListener('click', () => {
    sidebar.classList.toggle('collapsed');
});
document.getElementById('logout-link').addEventListener('click', function(e) {
    e.preventDefault();
    document.getElementById('logout-form').submit();
});
</script>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('common.layout', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/kuldeepsharma/Projects/hgnlplay/resources/views/withdraw/index.blade.php ENDPATH**/ ?>