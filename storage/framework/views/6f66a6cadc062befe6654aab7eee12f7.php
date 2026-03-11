<!-- Sidebar -->
<div class="mobile-header">
    <div class="logo"><img src="/storage/logo.jpeg" alt="Himalaya Trading">
        <!-- <h1>Himalaya <span class="brand-green">Trading</span></h1> -->
    </div>
    <div class="toggle-btn" id="toggleBtn">☰</div>
</div>
<aside class="sidebar" id="sidebar">
    <div class="logo"><img src="/storage/logo.jpeg" alt="Himalaya Trading">
        <!-- <h1>Himalaya <span class="brand-green">Trading</span></h1> -->
    </div>
    <div class="toggle-btn" id="toggleBtnd">☰</div>
    <ul>
        <?php if(Auth::user()->hasRole('customer')): ?>
        <a href="<?php echo e(url('/dashboard')); ?>" style="text-decoration:none;color:inherit;display:block;">
            <li class="<?php echo e(Request::is('dashboard') ? 'active' : ''); ?>">🏠 <span>Dashboard</span></li>
        </a>

        <li class="<?php echo e(request()->routeIs('profile') || request()->routeIs('profile.edit') || request()->routeIs('profile.kyc') ? 'active' : ''); ?>"
            style="display: block;">
            <span><a href="<?php echo e(route('profile')); ?>" style="text-decoration:none; color:inherit;display:block;">👤
                    Profile</a></span>
            <ul
                style="list-style:none; padding-left:10px; margin-top:10px; display:<?php echo e(request()->routeIs('profile') || request()->routeIs('profile.edit') || request()->routeIs('profile.kyc') ? 'block' : 'none'); ?>;">
                <li class="<?php echo e(request()->routeIs('profile') ? 'active' : ''); ?>">
                    <a href="<?php echo e(route('profile')); ?>" style="text-decoration:none; color:inherit; display:block;">View
                        Profile</a>
                </li>
                <li class="<?php echo e(request()->routeIs('profile.edit') ? 'active' : ''); ?>">
                    <a href="<?php echo e(route('profile.edit')); ?>"
                        style="text-decoration:none; color:inherit; display:block;">Update Profile</a>
                </li>
                <li class="<?php echo e(request()->routeIs('profile.kyc') ? 'active' : ''); ?>">
                    <a href="<?php echo e(route('profile.kyc')); ?>"
                        style="text-decoration:none; color:inherit; display:block;">Upload KYC</a>
                </li>
            </ul>
        </li>


        <a href="<?php echo e(route('member.register')); ?>" style="text-decoration: none; color: inherit; display: block;">
            <li class="<?php echo e(Request::routeIs('member.register') ? 'active' : ''); ?>">📝 <span>Registration</span></li>
        </a>
        <li
            class="team-item <?php echo e(request()->routeIs('tree','team.list','team.direct','team.total','team.level') ? 'active open' : ''); ?>" style="display:block;padding:0px;">
            <div class="team-menu">
                <span class="team-title">
                    👥 <span>Team Detail</span>
                </span>
                <span class="arrow">▾</span>
            </div>

            <ul class="submenu">
                <li>
                    <a href="<?php echo e(route('tree')); ?>">🌳 Tree View</a>
                </li>
                <li>
                    <a href="<?php echo e(route('team.list')); ?>">📋 List View</a>
                </li>
                <li>
                    <a href="<?php echo e(route('team.direct')); ?>">📋 Direct Referral</a>
                </li>
                <li>
                    <a href="<?php echo e(route('team.total')); ?>">📋 Total Downline</a>
                </li>
                <li>
                    <a href="<?php echo e(route('team.level')); ?>">📋 Total Level Downline</a>
                </li>
            </ul>
        </li>


        <a href="<?php echo e(route('wallet.fund')); ?>" style="text-decoration:none;color:inherit;display:block;">
            <li class="<?php echo e(Request::routeIs('wallet.fund') ? 'active' : ''); ?>">💰 <span>Wallet Fund Request</span></li>
        </a>

        <a href="<?php echo e(route('member.topup')); ?>" style="text-decoration:none;color:inherit;display:block;">
            <li class="<?php echo e(Request::routeIs('member.topup') ? 'active' : ''); ?>">🔄 <span>Member Topup</span></li>
        </a>

        <a href="<?php echo e(route('withdraw.index')); ?>" style="text-decoration:none;color:inherit;display:block;">
            <li class="<?php echo e(Request::routeIs('withdraw.index') ? 'active' : ''); ?>">💸 <span>Withdraw</span></li>
        </a>

        <a href="<?php echo e(route('reports.index')); ?>" style="text-decoration:none;color:inherit;display:block;">
            <li class="<?php echo e(Request::routeIs('reports.index') ? 'active' : ''); ?>">📊 <span>Report</span></li>
        </a>

        <!-- <li>🎁 <span>Reward Bonus</span></li> -->

        <a href="<?php echo e(route('mailbox')); ?>" style="text-decoration:none;color:inherit;display:block;">
            <li class="<?php echo e(Request::routeIs('mailbox') ? 'active' : ''); ?>">📬 <span>Mail Box</span></li>
        </a>
        <?php endif; ?>

        <?php if(Auth::user()->hasRole('admin')): ?>
        <a href="<?php echo e(url('/dashboard')); ?>" style="text-decoration:none;color:inherit;display:block;">
            <li class="<?php echo e(Request::is('dashboard') ? 'active' : ''); ?>">🧭 <span>Admin Console</span></li>
        </a>

        <a href="<?php echo e(route('admin.users')); ?>" style="text-decoration:none;color:inherit;display:block;">
            <li class="<?php echo e(Request::routeIs('admin.users') ? 'active' : ''); ?>">👥 <span>Manage Users</span></li>
        </a>

        <a href="<?php echo e(route('admin.payments')); ?>" style="text-decoration:none;color:inherit;display:block;">
            <li class="<?php echo e(Request::routeIs('admin.payments') ? 'active' : ''); ?>">💳 <span>Manage Payments</span></li>
        </a>

        <a href="<?php echo e(route('admin.payouts')); ?>" style="text-decoration:none;color:inherit;display:block;">
            <li class="<?php echo e(Request::routeIs('admin.payouts') ? 'active' : ''); ?>">🏦 <span>Manage Payouts</span></li>
        </a>

        <a href="<?php echo e(route('admin.support')); ?>" style="text-decoration:none;color:inherit;display:block;">
            <li class="<?php echo e(Request::routeIs('admin.support') ? 'active' : ''); ?>">🆘 <span>Support Request</span></li>
        </a>

        <a href="<?php echo e(route('admin.lucky.index')); ?>" style="text-decoration:none;color:inherit;display:block;">
            <li class="<?php echo e(Request::routeIs('admin.lucky.index') ? 'active' : ''); ?>">🎁 <span>Lucky Draw</span></li>
        </a>
        <?php endif; ?>



        
        
        <a href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" style="text-decoration: none; color: inherit; display: block;">
           <li>🚪 <span>Logout</span></li>
        </a>
        
        <form id="logout-form" action="<?php echo e(route('logout')); ?>" method="POST" style="display: none;">
            <?php echo csrf_field(); ?>
        </form>

    </ul>


</aside><?php /**PATH /Users/kuldeepsharma/Projects/hgnlplay/resources/views/common/header.blade.php ENDPATH**/ ?>