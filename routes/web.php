<?php

use App\Http\Controllers\Admin\AdminPortalController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Auth\AdminLoginController;
use App\Http\Controllers\Auth\CustomerLoginController;
use App\Http\Controllers\Auth\PasswordResetCodeController;
use App\Http\Controllers\Auth\StaffLoginController;
use App\Http\Controllers\Customer\DashboardController as CustomerDashboardController;
use App\Http\Controllers\Staff\DashboardController as StaffDashboardController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Portal chooser
|--------------------------------------------------------------------------
*/
Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::get('/app/download', function () {
    $apkPath = storage_path('app/releases/quickwash-customer.apk');

    abort_unless(is_file($apkPath), 404, 'The Android app is not available for download yet.');

    return response()->download($apkPath, 'QuickWash-Customer.apk', [
        'Content-Type' => 'application/vnd.android.package-archive',
    ]);
})->name('app.download');

/*
|--------------------------------------------------------------------------
| Admin portal (guard: admin)
|--------------------------------------------------------------------------
*/
Route::prefix('admin')->name('admin.')->group(function () {
    Route::middleware('guest:admin')->group(function () {
        Route::get('/login', [AdminLoginController::class, 'showLoginForm'])->name('login');
        Route::post('/login', [AdminLoginController::class, 'login']);
        Route::get('/forgot-password', [PasswordResetCodeController::class, 'showRequestForm'])->defaults('portal', 'admin')->name('password.request');
        Route::post('/forgot-password', [PasswordResetCodeController::class, 'sendCode'])->defaults('portal', 'admin')->middleware('throttle:5,1')->name('password.email');
        Route::get('/verify-reset-code', [PasswordResetCodeController::class, 'showCodeForm'])->defaults('portal', 'admin')->name('password.code');
        Route::post('/verify-reset-code', [PasswordResetCodeController::class, 'verifyCode'])->defaults('portal', 'admin')->middleware('throttle:10,1')->name('password.verify');
        Route::get('/reset-password', [PasswordResetCodeController::class, 'showResetForm'])->defaults('portal', 'admin')->name('password.reset');
        Route::post('/reset-password', [PasswordResetCodeController::class, 'resetPassword'])->defaults('portal', 'admin')->name('password.update');
    });

    Route::middleware('auth.admin')->group(function () {
        Route::post('/logout', [AdminLoginController::class, 'logout'])->name('logout');
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
        Route::get('/bookings', [AdminPortalController::class, 'bookings'])->name('bookings');
        Route::delete('/bookings', [AdminPortalController::class, 'destroyBookings'])->name('bookings.destroy-selected');
        Route::get('/bookings/{booking}', [AdminPortalController::class, 'showBooking'])->name('bookings.show');
        Route::delete('/bookings/{booking}', [AdminPortalController::class, 'destroyBooking'])->name('bookings.destroy');
        Route::get('/customers', [AdminPortalController::class, 'customers'])->name('customers');
        Route::get('/customers/{customer}', [AdminPortalController::class, 'showCustomer'])->name('customers.show');
        Route::get('/staff', [AdminPortalController::class, 'staff'])->name('staff');
        Route::post('/staff/{staff}/toggle-active', [AdminPortalController::class, 'toggleStaffActive'])->name('staff.toggle-active');
        Route::get('/assign-staff', [AdminPortalController::class, 'assignStaff'])->name('assign-staff');
        Route::post('/assign-staff', [AdminPortalController::class, 'assignStaffStore'])->name('assign-staff.store');
        Route::get('/services', [AdminPortalController::class, 'services'])->name('services');
        Route::post('/services', [AdminPortalController::class, 'storeService'])->name('services.store');
        Route::put('/services/{service}', [AdminPortalController::class, 'updateService'])->name('services.update');
        Route::delete('/services/{service}', [AdminPortalController::class, 'destroyService'])->name('services.destroy');
        Route::get('/reports', [AdminPortalController::class, 'reports'])->name('reports');
        Route::get('/payments', [AdminPortalController::class, 'payments'])->name('payments');
        Route::delete('/payments', [AdminPortalController::class, 'destroyPayments'])->name('payments.destroy-selected');
        Route::get('/payments/{payment}', [AdminPortalController::class, 'showPayment'])->name('payments.show');
        Route::delete('/payments/{payment}', [AdminPortalController::class, 'destroyPayment'])->name('payments.destroy');
        Route::post('/payments/{payment}/confirm', [AdminPortalController::class, 'confirmPayment'])->name('payments.confirm');
        Route::get('/announcements', [AdminPortalController::class, 'announcements'])->name('announcements');
        Route::post('/announcements', [AdminPortalController::class, 'storeAnnouncement'])->name('announcements.store');
        Route::get('/notifications', [AdminPortalController::class, 'notifications'])->name('notifications');
        Route::post('/notifications/mark-all-read', [AdminPortalController::class, 'markAllNotificationsRead'])->name('notifications.mark-all-read');
        Route::get('/notifications/{id}/goto', [AdminPortalController::class, 'gotoNotification'])->name('notifications.goto');
        Route::delete('/announcements/{announcement}', [AdminPortalController::class, 'destroyAnnouncement'])->name('announcements.destroy');
        Route::get('/settings', [AdminPortalController::class, 'settings'])->name('settings');
        Route::post('/settings/password', [AdminPortalController::class, 'updatePassword'])->name('settings.password');
    });
});

/*
|--------------------------------------------------------------------------
| Staff portal (guard: staff)
|--------------------------------------------------------------------------
*/
Route::prefix('staff')->name('staff.')->group(function () {
    Route::middleware('guest:staff')->group(function () {
        Route::get('/login', [StaffLoginController::class, 'showLoginForm'])->name('login');
        Route::post('/login', [StaffLoginController::class, 'login']);
        Route::get('/forgot-password', [PasswordResetCodeController::class, 'showRequestForm'])->defaults('portal', 'staff')->name('password.request');
        Route::post('/forgot-password', [PasswordResetCodeController::class, 'sendCode'])->defaults('portal', 'staff')->middleware('throttle:5,1')->name('password.email');
        Route::get('/verify-reset-code', [PasswordResetCodeController::class, 'showCodeForm'])->defaults('portal', 'staff')->name('password.code');
        Route::post('/verify-reset-code', [PasswordResetCodeController::class, 'verifyCode'])->defaults('portal', 'staff')->middleware('throttle:10,1')->name('password.verify');
        Route::get('/reset-password', [PasswordResetCodeController::class, 'showResetForm'])->defaults('portal', 'staff')->name('password.reset');
        Route::post('/reset-password', [PasswordResetCodeController::class, 'resetPassword'])->defaults('portal', 'staff')->name('password.update');
    });

    Route::middleware('auth.staff')->group(function () {
        Route::post('/logout', [StaffLoginController::class, 'logout'])->name('logout');
        Route::get('/dashboard', [StaffDashboardController::class, 'index'])->name('dashboard');
        Route::get('/bookings', [StaffDashboardController::class, 'bookings'])->name('bookings');
        Route::get('/bookings/{booking}', [StaffDashboardController::class, 'showBooking'])->name('bookings.show');
        Route::delete('/bookings/{booking}', [StaffDashboardController::class, 'destroyBooking'])->name('bookings.destroy');
        Route::get('/bookings/{booking}/status', [StaffDashboardController::class, 'updateStatus'])->name('bookings.status');
        Route::post('/bookings/{booking}/status', [StaffDashboardController::class, 'storeStatus'])->name('bookings.status.store');
        Route::get('/pickups', [StaffDashboardController::class, 'pickups'])->name('pickups');
        Route::get('/deliveries', [StaffDashboardController::class, 'deliveries'])->name('deliveries');
        Route::get('/receipt/{booking}', [StaffDashboardController::class, 'receipt'])->name('receipt');
        Route::post('/payments/{payment}/confirm', [StaffDashboardController::class, 'confirmPayment'])->name('payments.confirm');
        Route::get('/notifications', [StaffDashboardController::class, 'notifications'])->name('notifications');
        Route::post('/notifications/mark-all-read', [StaffDashboardController::class, 'markAllNotificationsRead'])->name('notifications.mark-all-read');
        Route::get('/notifications/{id}/goto', [StaffDashboardController::class, 'gotoNotification'])->name('notifications.goto');
        Route::get('/profile', [StaffDashboardController::class, 'profile'])->name('profile');
    });
});

/*
|--------------------------------------------------------------------------
| Customer portal (guard: customer)
|--------------------------------------------------------------------------
*/
Route::prefix('customer')->name('customer.')->group(function () {
    Route::middleware('guest:customer')->group(function () {
        Route::get('/login', [CustomerLoginController::class, 'showLoginForm'])->name('login');
        Route::post('/login', [CustomerLoginController::class, 'login']);
        Route::get('/register', [CustomerLoginController::class, 'showRegistrationForm'])->name('register');
        Route::post('/register', [CustomerLoginController::class, 'register']);
        Route::get('/forgot-password', [PasswordResetCodeController::class, 'showRequestForm'])->defaults('portal', 'customer')->name('password.request');
        Route::post('/forgot-password', [PasswordResetCodeController::class, 'sendCode'])->defaults('portal', 'customer')->middleware('throttle:5,1')->name('password.email');
        Route::get('/verify-reset-code', [PasswordResetCodeController::class, 'showCodeForm'])->defaults('portal', 'customer')->name('password.code');
        Route::post('/verify-reset-code', [PasswordResetCodeController::class, 'verifyCode'])->defaults('portal', 'customer')->middleware('throttle:10,1')->name('password.verify');
        Route::get('/reset-password', [PasswordResetCodeController::class, 'showResetForm'])->defaults('portal', 'customer')->name('password.reset');
        Route::post('/reset-password', [PasswordResetCodeController::class, 'resetPassword'])->defaults('portal', 'customer')->name('password.update');
    });

    Route::middleware('auth.customer')->group(function () {
        Route::post('/logout', [CustomerLoginController::class, 'logout'])->name('logout');
        Route::get('/dashboard', [CustomerDashboardController::class, 'index'])->name('dashboard');
        Route::get('/bookings', [CustomerDashboardController::class, 'bookings'])->name('bookings');
        Route::get('/bookings/create', [CustomerDashboardController::class, 'createBooking'])->name('bookings.create');
        Route::post('/bookings', [CustomerDashboardController::class, 'storeBooking'])->name('bookings.store');
        Route::get('/bookings/{booking}', [CustomerDashboardController::class, 'showBooking'])->name('bookings.show');
        Route::post('/bookings/{booking}/location', [CustomerDashboardController::class, 'updateLocation'])->name('bookings.location.update');
        Route::get('/bookings/{booking}/payment/resume', [CustomerDashboardController::class, 'resumePayment'])->name('bookings.payment.resume');
        Route::post('/bookings/{booking}/pay', [CustomerDashboardController::class, 'processPayment'])->name('bookings.pay');
        Route::get('/bookings/{booking}/receipt', [CustomerDashboardController::class, 'downloadReceipt'])->name('bookings.receipt');
        Route::get('/payments/{payment}/cash-processing', [CustomerDashboardController::class, 'showCashPaymentProcessing'])->name('payments.cash-processing');
        Route::get('/payments/{payment}/gcash', [CustomerDashboardController::class, 'showGcashPayment'])->name('payments.gcash');
        Route::post('/payments/{payment}/gcash', [CustomerDashboardController::class, 'submitGcashPayment'])->name('payments.gcash.submit');
        Route::get('/tracking', [CustomerDashboardController::class, 'tracking'])->name('tracking');
        Route::get('/loyalty', [CustomerDashboardController::class, 'loyalty'])->name('loyalty');
        Route::get('/notifications', [CustomerDashboardController::class, 'notifications'])->name('notifications');
        Route::post('/notifications/mark-all-read', [CustomerDashboardController::class, 'markAllNotificationsRead'])->name('notifications.mark-all-read');
        Route::get('/notifications/{id}/goto', [CustomerDashboardController::class, 'gotoNotification'])->name('notifications.goto');
        Route::get('/profile', [CustomerDashboardController::class, 'profile'])->name('profile');
        Route::put('/profile', [CustomerDashboardController::class, 'updateProfile'])->name('profile.update');
    });
});
