<?php $__env->startSection('title', 'Reports'); ?>
<?php $__env->startSection('main'); ?>
<style>


body {
    margin: 0;
    font-family: "Inter", sans-serif;
    background: var(--bg);
    color: var(--text);
    display: flex;
    min-height: 100vh;
}


.user-info {
    background: #141c22;
    padding: 8px 14px;
    border-radius: 999px;
    display: flex;
    align-items: center;
    gap: 8px;
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

.card h2 {
    font-size: 18px;
    margin-top: 0
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
    background: linear-gradient(90deg, var(--accent), var(--accent));
    color: #000;
    white-space: nowrap;
    text-decoration: none;
}

.btn-copy:hover {
    opacity: .9
}

.btn-whatsapp {
    background: var(--accent);
    color: #fff;   white-space: nowrap;
    text-decoration: none;
}

.btn-whatsapp:hover {
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
}

th {
    background: #161f29;
    color: #a9b9c7;
}

td {
    color: #d4dee8
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
    <h1>Reports</h1>
    <div class="user-info">👤 <?php echo e($user->username ?? $user->name); ?></div>
</div>

<div class="card">
    <h2>Income Reports</h2>

    <form method="GET" action="<?php echo e(route('reports.index')); ?>"
        style="margin-bottom:20px;display:flex;gap:10px;flex-wrap:wrap;">
        <div>
            <label>From:</label>
            <input type="date" name="from" value="<?php echo e($from); ?>"
                style="padding:8px;border-radius:6px;background:#141c22;border:1px solid #1f2832;color:#fff;">
        </div>
        <div>
            <label>To:</label>
            <input type="date" name="to" value="<?php echo e($to); ?>"
                style="padding:8px;border-radius:6px;background:#141c22;border:1px solid #1f2832;color:#fff;">
        </div>
        <button class="btn btn-copy" type="submit">Filter</button>
        <a href="<?php echo e(route('reports.index')); ?>" class="btn btn-whatsapp">Reset</a>
    </form>

    <!-- Tabs -->
    <div style="display:flex;gap:10px;margin-bottom:16px;">
        <button class="btn" id="matchingBtn" style="background:var(--accent);color:#000;">Matching Income</button>
        <button class="btn" id="directBtn" style="background:#333;color:#fff;">Direct Income</button>
    </div>

    <!-- Matching Income -->
    <div id="matchingTab">
        <div style="display:flex;justify-content:space-between;align-items:center;">
            <h3 style="color:var(--accent)">Matching Income</h3>
            <a href="<?php echo e(route('reports.export','matching')); ?>" class="btn btn-copy">⬇ Export CSV</a>
        </div>
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Date</th>
                        <th>Amount</th>
                        <th>Type</th>
                        <th>Remarks</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $matchingIncomes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $r): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr>
                        <td><?php echo e($i + 1); ?></td>
                        <td><?php echo e(date('d M Y, h:i A', strtotime($r->created_at))); ?></td>
                        <td>₹<?php echo e(number_format($r->amount,2)); ?></td>
                        <td><?php echo e($r->type); ?></td>
                        <td><?php echo e($r->remarks); ?></td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr>
                        <td colspan="5">No records found.</td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <?php echo e($matchingIncomes->appends(request()->query())->links()); ?>

    </div>

    <!-- Direct Income -->
    <div id="directTab" style="display:none;">
        <div style="display:flex;justify-content:space-between;align-items:center;">
            <h3 style="color:var(--accent)">Direct Income</h3>
            <a href="<?php echo e(route('reports.export','direct')); ?>" class="btn btn-copy">⬇ Export CSV</a>
        </div>
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Date</th>
                        <th>Amount</th>
                        <th>Type</th>
                        <th>Remarks</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $directIncomes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $r): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr>
                        <td><?php echo e($i + 1); ?></td>
                        <td><?php echo e(date('d M Y, h:i A', strtotime($r->created_at))); ?></td>
                        <td>₹<?php echo e(number_format($r->amount,2)); ?></td>
                        <td><?php echo e($r->type); ?></td>
                        <td><?php echo e($r->remarks); ?></td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr>
                        <td colspan="5">No records found.</td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <?php echo e($directIncomes->appends(request()->query())->links()); ?>

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
document.getElementById('logout-link').addEventListener('click', function(e) {
    e.preventDefault();
    document.getElementById('logout-form').submit();
});

const matchTab = document.getElementById('matchingTab');
const directTab = document.getElementById('directTab');
const matchBtn = document.getElementById('matchingBtn');
const directBtn = document.getElementById('directBtn');
matchBtn.onclick = () => {
    matchTab.style.display = 'block';
    directTab.style.display = 'none';
    matchBtn.style.background = 'var(--accent)';
    matchBtn.style.color = '#000';
    directBtn.style.background = '#333';
    directBtn.style.color = '#fff';
};
directBtn.onclick = () => {
    matchTab.style.display = 'none';
    directTab.style.display = 'block';
    directBtn.style.background = 'var(--accent)';
    directBtn.style.color = '#000';
    matchBtn.style.background = '#333';
    matchBtn.style.color = '#fff';
};
</script>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('common.layout', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/kuldeepsharma/Projects/hgnlplay/resources/views/reports/index.blade.php ENDPATH**/ ?>