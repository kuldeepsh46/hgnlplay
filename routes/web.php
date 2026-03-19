<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\TreeController;
use App\Http\Controllers\TopupController;
use App\Http\Controllers\MailboxController;
use App\Http\Controllers\EMIController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\WithdrawController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Admin\PaymentController;
use App\Http\Controllers\Admin\PayoutController;
use App\Http\Controllers\Admin\SupportController;
use App\Http\Controllers\Admin\SettingsController;
use App\Mail\WelcomeMemberMail;
use App\Models\User;
Route::get('/', function () {
    return view('welcome');
});
// Route::get('/login', function () {
//     return view('login');
// });

Auth::routes();

Route::get('/dashboard', [App\Http\Controllers\DashboardController::class, 'index'])
    ->middleware(['auth'])
    ->name('dashboard');

Route::middleware(['auth'])->group(function () {
    // Route::view('/dashboard','dashboard')->name('dashboard');

    // Route::get('/tree', [\App\Http\Controllers\Mlm\TreeController::class,'index'])->name('tree');

    Route::post('/order', [\App\Http\Controllers\Mlm\OrderController::class, 'store'])->name('order.store');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/admin/users', [DashboardController::class, 'manageUsers'])->name('admin.users');
    Route::delete('/admin/users/{id}', [DashboardController::class, 'deleteUser'])->name('admin.users.delete');
    Route::get('/admin/users/{id}/edit', [DashboardController::class, 'editUser'])->name('admin.users.edit');
    Route::get('/admin/user-tree/{id}', [DashboardController::class, 'showUserTree'])->name('tree.view');
    Route::put('/admin/users/{id}', [DashboardController::class, 'updateUser'])->name('admin.users.update');
    Route::post('/admin/user/update-password', [DashboardController::class, 'updateUserPassword'])->name('admin.user.password.update');

    Route::get('/payments', [PaymentController::class, 'index'])->name('admin.payments');
    Route::post('/payments/approve/{id}', [PaymentController::class, 'approve'])->name('admin.payments.approve');
    Route::post('/payments/reject/{id}', [PaymentController::class, 'reject'])->name('admin.payments.reject');

    Route::get('/payouts', [PayoutController::class, 'index'])->name('admin.payouts');
    Route::post('/payouts/approve/{id}', [PayoutController::class, 'approve'])->name('admin.payouts.approve');
    Route::post('/payouts/reject/{id}', [PayoutController::class, 'reject'])->name('admin.payouts.reject');

    Route::get('/support', [SupportController::class, 'index'])->name('admin.support');
    Route::post('/support/reply/{id}', [SupportController::class, 'reply'])->name('admin.support.reply');
    Route::get('/support/message/{id}', [SupportController::class, 'getMessage'])->name('admin.support.message');

    // Route::get('/settings/qr', [SettingsController::class, 'index'])->name('admin.settings.qr');
    // Route::get('/settings/usdt', [SettingsController::class, 'usdt'])->name('admin.settings.usdt');
    // Route::post('/admin/settings/update-qr/{id}', [App\Http\Controllers\AdminController::class, 'updateScanner'])
    // ->name('admin.settings.updateQR');
    // Route::post('/admin/update-scanner', [SettingsController::class, 'updateScanner'])->name('updateQrScanner');
    // Route to show the settings page (General view)
    Route::get('/admin/settings/qr/{id}', [SettingsController::class, 'index'])->name('admin.settings.viewQR');

    // Route to handle the upload
    Route::post('/admin/settings/update-qr/{id}', [SettingsController::class, 'updateScanner'])->name('admin.settings.updateQR');
});

Route::get('/register-mlm', [\App\Http\Controllers\Mlm\RegisterController::class, 'show'])->name('mlm.register');
Route::post('/register-mlm', [\App\Http\Controllers\Mlm\RegisterController::class, 'store'])->name('mlm.register.store');

Route::middleware(['auth'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile');
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::post('/profile/update', [ProfileController::class, 'update'])->name('profile.update');
    Route::get('/profile/kyc', [ProfileController::class, 'kyc'])->name('profile.kyc');
    // Route::post('/profile/kyc/upload', [ProfileController::class, 'uploadKyc'])->name('profile.kyc.upload');
    Route::post('/profile/kyc/upload', [ProfileController::class, 'kycUpload'])->name('profile.kyc.upload');

    Route::post('/changep/update', [ProfileController::class, 'updatePassword'])->name('changep.update');
});

// Route::middleware(['auth', 'role:admin'])->group(function () {
//     Route::get('/admin/dashboard', [AdminController::class, 'index'])->name('admin.dashboard');
// });

Route::middleware(['auth'])->group(function () {
    Route::get('/member/register', [MemberController::class, 'create'])->name('member.register');
    Route::post('/member/register', [MemberController::class, 'store'])->name('member.store');
    // User Routes
    // Route::get('/wallet-fund-request', [MemberController::class, 'fundRequest'])->name('user.fundRequest');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/team/tree', [TreeController::class, 'index'])->name('tree');
    Route::get('/team/list', [TreeController::class, 'list'])->name('team.list');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/wallet-fund-request', [ProfileController::class, 'walletFundRequest'])->name('wallet.fund');
    Route::post('/wallet-fund-request/store', [ProfileController::class, 'storeWalletFundRequest'])->name('wallet.fund.store');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/member-topup', [TopupController::class, 'index'])->name('member.topup');
    Route::post('/member-topup/store', [TopupController::class, 'store'])->name('member.topup.store');
    Route::get('/member/check-id', [MemberController::class, 'checkId'])->name('member.check-id');
    Route::get('/mailbox', [MailboxController::class, 'index'])->name('mailbox');
    Route::post('/mailbox/send', [MailboxController::class, 'store'])->name('mailbox.send');
    Route::get('/mailbox/reply/{id}', [MailboxController::class, 'getReply'])->name('mailbox.reply');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/emi', [EMIController::class, 'index'])->name('emi.index');

    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/export/{type}', [ReportController::class, 'export'])->name('reports.export');

    Route::get('/withdraw', [WithdrawController::class, 'index'])->name('withdraw.index');
    Route::post('/withdraw/store', [WithdrawController::class, 'store'])->name('withdraw.store');

    Route::get('/team/direct-referral', [TreeController::class, 'directReferral'])->name('team.direct');

    Route::get('/team/total-downline', [TreeController::class, 'totalDownline'])->name('team.total');

    Route::get('/team/team-level', [TreeController::class, 'teamLevelDownline'])->name('team.level');
});

Route::get('/privacy-policy', function () {
    return view('privacy-policy');
});

Route::get('/terms-conditions', function () {
    return view('terms-conditions');
});

Route::get('/team-tree', [TreeController::class, 'index'])->name('team.tree');

use App\Http\Controllers\Admin\LuckyController;

Route::middleware(['auth'])->group(function () {
    Route::post('/lucky/release', [LuckyController::class, 'release']);
    Route::post('/lucky/winner', [LuckyController::class, 'declareWinner']);
});

use App\Http\Controllers\Admin\LuckyAdminController;

Route::middleware(['auth'])->group(function () {
    Route::get('/lucky', [LuckyAdminController::class, 'index'])->name('admin.lucky.index');
    Route::get('/lucky/{cycle}/vouchers', [LuckyAdminController::class, 'vouchers'])->name('admin.lucky.vouchers');
    Route::post('/lucky/declare-winner', [LuckyAdminController::class, 'declareWinner'])->name('admin.lucky.declare');
});
Route::get('/mail-preview', function () {
    $user = User::first(); // Grab a user from your database
    return new WelcomeMemberMail($user, 'Shimla@46');
});
