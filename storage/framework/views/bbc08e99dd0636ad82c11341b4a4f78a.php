<?php $__env->startSection('title', 'Wallet Fund Request'); ?>
<?php $__env->startSection('main'); ?>
<style>

.flex-section {
    display: flex;
    width: 100%;
    justify-content: space-between;
    gap: 36px;
    align-items: stretch;
}
.Qr-sec {
    text-align: center;
    width: 300px;
}
.req-sec .grid {
    display: flex;
    width: 101%;
    flex-wrap: wrap;
}

.req-sec .grid div {
    width: 49% !important;
}



img#qr-image {
    max-width: 300px;
    width: 287px;
    height: 370px;
    object-fit: cover;
    display: flex;
    align-items: center;
    justify-content: center;
}

.req-sec {
    width: 76%;
}


.user-info {
    background: #141c22;
    padding: 8px 14px;
    border-radius: 999px;
    color: var(--accent);
    font-weight: 600;
}

/* Cards / form */
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
    display: block;
    margin-bottom: 6px;
}

input,
select,
textarea {
    width: 100%;
    padding: 10px;
    border-radius: 8px;
    border: 1px solid #1f2832;
    background: #141c22;
    color: #fff;
}

.grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
    gap: 16px;
}

.btn {
    background: var(--accent);
    color: #000;
    border: none;
    padding: 11px 20px;
    font-weight: 700;
    border-radius: 8px;
    cursor: pointer;
}

.btn:hover {
    opacity: .92;
}

.alert {
    padding: 10px 14px;
    border-radius: 8px;
    margin-bottom: 16px;
}

.alert-success {
    background: #a7ff1e;
    color: #000;
}

/* Table */
.table {
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
    color: #d4dee8;
}


@media(max-width:768px) {
  .Qr-sec {
    text-align: center;
    width: 100%;
}
  .req-sec {
    width: 100%;
}
img#qr-image {
    max-width: 100%;
    width: 100%;
    height:auto;
    object-fit: cover;
}
  .req-sec .grid div {
    width: 100% !important;
}
  .flex-section {
    flex-wrap: wrap;
        gap: 13px;
    flex-direction: column-reverse;
}
  
   
}

div#app {
    width: 100% !important;
    height: 100% !important;
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
/* Responsive */
@media only screen and (min-width: 768px) and (max-width: 1300px) {

    .req-sec .grid div {
        width: 100% !important; /* single column */
    }

}

</style>

<!-- Header -->
<div class="header">
    <h1> Wallet Fund Request</h1>
    <div class="user-info">👤 <?php echo e($user->username ?? $user->name); ?></div>
</div>

<?php if(session('success')): ?>
<div class="alert alert-success">✅ <?php echo e(session('success')); ?></div>
<?php endif; ?>

<!-- Your saved bank details (from users table) -->
<div class="card">
    <h2>Your Bank Details on File</h2>
    <div class="grid">
        <div>
            <label>Bank Name</label>
            <input type="text" value="<?php echo e($user->bank_name); ?>" readonly>
        </div>
        <div>
            <label>Branch Name</label>
            <input type="text" value="<?php echo e($user->branch_name); ?>" readonly>
        </div>
        <div>
            <label>IFSC Code</label>
            <input type="text" value="<?php echo e($user->ifsc_code); ?>" readonly>
        </div>
        <div>
            <label>Account Holder</label>
            <input type="text" value="<?php echo e($user->account_holder_name); ?>" readonly>
        </div>
        <div>
            <label>Account Number</label>
            <input type="text" value="<?php echo e($user->account_number); ?>" readonly>
        </div>
    </div>
    <p style="margin-top:10px;color:var(--muted);font-size:13px">
        Tip: You can leave the “Bank Name” and “Account Number” fields empty in the form below to automatically use
        these saved values.
    </p>
</div>

<!-- Submit Request -->
<div class="card">
    <h2>Submit Fund Request</h2>
    <form method="POST" action="<?php echo e(route('wallet.fund.store')); ?>" enctype="multipart/form-data">
        <?php echo csrf_field(); ?>

        <div class="flex-section">
            <div class="req-sec">
          <div class="grid">
            <div>
                <label>Request Amount</label>
                <input type="number" name="amount" required min="1" step="1" placeholder="Enter amount">
            </div>
            <div>
                <label>Deposit Date</label>
                <input type="date" name="deposit_date" required value="<?php echo e(now()->toDateString()); ?>">
            </div>
            <div>
                <label>Payment Mode</label>
                <select name="payment_mode" required>
                    <option value="">-- Select --</option>
                    <!--<option value="Cash">Cash</option>-->
                    <option value="QR Transaction">Online Transaction</option>
                </select>
            </div>
            <div>
                <label>Bank Name</label>
                <input type="text" name="bank_name" value="<?php echo e(old('bank_name')); ?>">
            </div>
            <div>
                <label>Acc Number</label>
                <input type="text" name="account_number" value="<?php echo e(old('account_number')); ?>">
            </div>
              <div>
            <label>Attach Slip / Screenshot (jpg, png, pdf up to 2MB)</label>
            <input type="file" name="attachment">
        </div>
        </div>

        <div style="margin-top:16px">
            <label>Transaction ID / Remark</label>
            <textarea name="transaction_remark" rows="3"
                placeholder="e.g., UTR number or notes"><?php echo e(old('transaction_remark')); ?></textarea>
        </div>
<div style="margin-top:18px">
            <button type="submit" class="btn">Submit</button>
        </div>
    
            </div>

            <!--  -->
            <div class="Qr-sec" id="qr-payment-box" style="display:none; margin-top:12px;">
                <div  >
                    <img id="qr-image" src="/uploads/scanner.jpeg" alt="QR Payment"
                        style=" border-radius:8px; border:1px solid #333;">

                    <p style="margin-top:8px; font-size:13px; color:var(--muted);">
                        Scan this QR code and make payment. Further fill this form and submit it.
                    </p>
                </div>
            </div>
            <!--  -->
            
        </div>
       

        
    </form>
</div>

<!-- Recent Requests -->
<div class="card">
    <h2>Recent Fund Requests</h2>
<div class="table-res">
        <table class="table">
        <thead>
            <tr>
                <th>#</th>
                <th>Amount</th>
                <th>Deposit Date</th>
                <th>Mode</th>
                <th>Status</th>
                <th>Bank Name</th>
                <th>Account #</th>
                <th>Slip</th>
            </tr>
        </thead>
        <tbody>
            <?php $__empty_1 = true; $__currentLoopData = $requests; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $req): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <tr>
                <td><?php echo e($i+1); ?></td>
                <td><?php echo e($req->amount); ?></td>
                <td><?php echo e($req->deposit_date); ?></td>
                <td><?php echo e($req->payment_mode); ?></td>
                <td><?php echo e(ucfirst($req->status)); ?></td>
                <td><?php echo e($req->bank_name); ?></td>
                <td><?php echo e($req->account_number); ?></td>
                <td>
                    <?php if($req->attachment): ?>
                    <a href="<?php echo e(asset($req->attachment)); ?>" target="_blank" style="color:var(--accent)">View</a>
                    <?php else: ?> — <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <tr>
                <td colspan="8">No fund requests found.</td>
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
// logout
document.getElementById('logout-link').addEventListener('click', function(e) {
    e.preventDefault();
    document.getElementById('logout-form').submit();
});


</script>


<script>
document.addEventListener("DOMContentLoaded", function() {

    // ===== Saved Bank Details (Read-only section) =====
    const savedBankName = document.querySelector(
        'input[value="<?php echo e($user->bank_name); ?>"]'
    );
    const savedAccountNumber = document.querySelector(
        'input[value="<?php echo e($user->account_number); ?>"]'
    );

    // ===== Submit Fund Request Fields =====
    const formBankName = document.querySelector('input[name="bank_name"]');
    const formAccountNumber = document.querySelector('input[name="account_number"]');

    // ===== Auto-fill logic =====
    if (savedBankName && savedBankName.value.trim() !== "") {
        if (formBankName && formBankName.value.trim() === "") {
            formBankName.value = savedBankName.value.trim();
        }
    }

    if (savedAccountNumber && savedAccountNumber.value.trim() !== "") {
        if (formAccountNumber && formAccountNumber.value.trim() === "") {
            formAccountNumber.value = savedAccountNumber.value.trim();
        }
    }

});
</script>

<script>
document.addEventListener("DOMContentLoaded", function() {

    const paymentSelect = document.querySelector('select[name="payment_mode"]');
    const qrBox = document.getElementById('qr-payment-box');

    if (!paymentSelect || !qrBox) return;

    // ===== Set Online Transaction as default =====
    paymentSelect.value = "QR Transaction";
    qrBox.style.display = "block";

    // ===== Change handler =====
    paymentSelect.addEventListener("change", function() {
        if (this.value === "QR Transaction") {
            qrBox.style.display = "block";
        } else {
            qrBox.style.display = "none";
        }
    });

});
</script>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('common.layout', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/kuldeepsharma/Projects/hgnlplay/resources/views/wallet-fund-request.blade.php ENDPATH**/ ?>