<?php $__env->startSection('title', 'Member Topup'); ?>
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


label {
    font-size: 14px;
    color: var(--muted);
}

input,
select {
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

.alert-error {
    background: #ff4a4a;
    color: #fff;
}

.table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 20px;
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
    color: #d4dee8;
}

h2{
  margin-top:0px;
  font-size:22px;
  margin:0px;
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
    gap: 12px;
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
    <h1> Member Topup</h1>
    <div class="user-info"
        style="background:#141c22;padding:8px 14px;border-radius:999px;color:var(--accent);font-weight:600;">
        👤 <?php echo e($user->username ?? $user->name); ?>

    </div>
</div>

<?php if(session('success')): ?>
<div class="alert alert-success"><?php echo e(session('success')); ?></div>
<?php endif; ?>
<?php if(session('error')): ?>
<div class="alert alert-error"><?php echo e(session('error')); ?></div>
<?php endif; ?>


<div class="card">
    <h2>EMI Progress</h2>
    <p style="font-size:16px;color:var(--accent);font-weight:600;">
        EMI Completed: <?php echo e($user->investment_count); ?>/16
    </p>
    <div style="background:#141c22;border-radius:8px;overflow:hidden;margin-top:10px;">
        <div
            style="width:<?php echo e(($user->investment_count/16)*100); ?>%;background:#3f7871;padding:6px 0;color:#fff;text-align:center;">
            <?php echo e(round(($user->investment_count/16)*100)); ?>% Complete
        </div>
    </div>
    <?php if($user->emi_status == 'completed'): ?>
    <p style="color:#00ff99;font-weight:600;">✅ All EMIs completed – Reward Released!</p>
    <?php endif; ?>
</div>


<div class="card">
    <h2>New Topup</h2>
    <form method="POST" action="<?php echo e(route('member.topup.store')); ?>">
        <?php echo csrf_field(); ?>
        <div class="form-group"><label>User Email</label><input type="email" name="email" required></div>
        <div class="form-group">
            <label>Package</label>
            <select name="package_id" id="packageSelect" required>
                <option value="">-- Select Package --</option>
                <?php $__currentLoopData = $packages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <option value="<?php echo e($p->id); ?>" data-amount="<?php echo e($p->amount); ?>"><?php echo e($p->name); ?> — ₹<?php echo e($p->amount); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
            <input type="hidden" name="final_amount" id="finalAmount" value="0">
            <div id="priceBreakdown" style="margin-top:10px;font-size:13px;color:var(--muted);"></div>
        </div>
        <div class="form-group"><label>Payment By</label>
            <select name="payment_by" required>
                <option value="Wallet">Wallet</option>
                <option value="Online">Online</option>
            </select>
        </div>
        <button type="submit" class="btn">Submit</button>
    </form>
</div>

<div class="card">
    <h2>Your Wallet Balance</h2>
    <p style="font-size:20px;color:var(--accent);font-weight:700;">₹<?php echo e(number_format($wallet->balance ?? 0, 2)); ?></p>
</div>

<div class="card table-res">
    <h2>Recent Topups</h2>
   <div class="table-res">
     <?php
    $transactions = DB::table('orders')->where('user_id', $user->id)->orderByDesc('id')->get();
    ?>
    <table class="table">
        <thead>
            <tr>
                <th>#</th>
                <th>Package</th>
                <th>Amount</th>
                <th>Payment By</th>
                <th>Status</th>
                <th>Date & Time</th> 
            </tr>
        </thead>
        <tbody>
            <?php $__empty_1 = true; $__currentLoopData = $transactions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $t): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <tr>
                <td><?php echo e($index + 1); ?></td>
                <td><?php echo e(DB::table('packages')->where('id', $t->package_id)->value('name')); ?></td>
                <td>₹<?php echo e(number_format($t->amount, 2)); ?></td>
                <td><?php echo e($t->payment_by); ?></td>
                <td><?php echo e(ucfirst($t->status)); ?></td>
                <td><?php echo e(\Carbon\Carbon::parse($t->created_at)->setTimezone('Asia/Kolkata')->format('d M Y, h:i A')); ?>

                </td> 
            </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <tr>
                <td colspan="6">No topups yet.</td>
            </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

</div>

<div class="card ">
    <h2>Topups Done by My Wallet</h2>
   <div class="table-res">
     <table class="table">
        <thead>
            <tr>
                <th>#</th>
                <th>To User</th>
                <th>Package</th>
                <th>Amount</th>
                <th>Payment By</th>
                <th>Status</th>
                <th>Date</th>
            </tr>
        </thead>
        <tbody>
            <?php $__empty_1 = true; $__currentLoopData = $walletTransactions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $t): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <?php
            $toUser = DB::table('users')->where('id', $t->user_id)->value('email');
            $packageName = DB::table('packages')->where('id', $t->package_id)->value('name');
            ?>
            <tr>
                <td><?php echo e($walletTransactions->firstItem() + $index); ?></td>
                <td><?php echo e($toUser); ?></td>
                <td><?php echo e($packageName); ?></td>
                <td>₹<?php echo e(number_format($t->amount, 2)); ?></td>
                <td><?php echo e($t->payment_by); ?></td>
                <td><?php echo e(ucfirst($t->status)); ?></td>
                <td><?php echo e(\Carbon\Carbon::parse($t->created_at)->format('d M Y, h:i A')); ?></td>
            </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <tr>
                <td colspan="7">No wallet transactions yet.</td>
            </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

    <?php echo e($walletTransactions->links()); ?>

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
// ✅ Registration Fee Logic (100 if EMI count is 0)
const investmentCount = <?php echo e($user->investment_count); ?>;
const registrationFee = investmentCount === 0 ? 100 : 0;
const packageSelect = document.getElementById('packageSelect');
const priceBreakdown = document.getElementById('priceBreakdown');

function updatePriceDisplay() {
    const selectedOption = packageSelect.options[packageSelect.selectedIndex];
    
    if (!selectedOption.value) {
        priceBreakdown.innerHTML = '';
        document.getElementById('finalAmount').value = '0';
        return;
    }
    
    const baseAmount = parseFloat(selectedOption.dataset.amount);
    const totalAmount = baseAmount + registrationFee;
    
    // ✅ Set the hidden input with the actual amount to be deducted
    document.getElementById('finalAmount').value = totalAmount;
    
    let breakdown = `<strong>Final Amount: ₹${totalAmount.toLocaleString('en-IN', {minimumFractionDigits: 2})}</strong>`;
    
    if (registrationFee > 0) {
        breakdown += `<br/>📋 Base: ₹${baseAmount.toLocaleString('en-IN', {minimumFractionDigits: 2})} + Registration Fee: ₹${registrationFee}`;
    }
    
    priceBreakdown.innerHTML = breakdown;
}

packageSelect.addEventListener('change', updatePriceDisplay);

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






// Handle Laravel logout securely
document.getElementById('logout-link').addEventListener('click', function(e) {
    e.preventDefault();
    document.getElementById('logout-form').submit();
});
</script>

</main>



<?php $__env->stopSection(); ?>
<?php echo $__env->make('common.layout', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/kuldeepsharma/Projects/hgnlplay/resources/views/member-topup.blade.php ENDPATH**/ ?>