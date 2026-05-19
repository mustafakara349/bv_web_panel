<?php

use App\Http\Controllers\Web\Auth\LoginController;
use App\Http\Controllers\Web\DashboardController;
use App\Http\Controllers\Web\AppointmentController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => redirect()->route('login'));

Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
});

Route::middleware(['auth'])->group(function () {
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/appointment-stats', [DashboardController::class, 'appointmentStats'])->name('dashboard.appointment-stats');

    Route::get('/appointments/events', [AppointmentController::class, 'events'])->name('appointments.events');
    Route::get('/appointments/available-slots', [AppointmentController::class, 'availableSlots'])->name('appointments.available-slots');
    Route::resource('appointments', AppointmentController::class)->except(['destroy']);
    Route::patch('/appointments/{appointment}/status', [AppointmentController::class, 'updateStatus'])->name('appointments.update-status');
    Route::post('/appointments/{appointment}/payments', [AppointmentController::class, 'storePayment'])->name('appointments.payments.store');
    Route::delete('/appointments/{appointment}/payments/{payment}', [AppointmentController::class, 'destroyPayment'])->name('appointments.payments.destroy');
    Route::post('/appointments/{appointment}/complete-payment', [AppointmentController::class, 'completeWithPayment'])->name('appointments.complete-payment');

    Route::resource('employees', App\Http\Controllers\Web\EmployeeController::class);
    Route::resource('customers', App\Http\Controllers\Web\CustomerController::class);
    Route::resource('services', App\Http\Controllers\Web\ServiceController::class);
    Route::patch('/services/{service}/toggle-status', [App\Http\Controllers\Web\ServiceController::class, 'toggleStatus'])->name('services.toggle-status');
    Route::get('/finance/transactions', [App\Http\Controllers\Web\TransactionController::class, 'index'])->name('finance.transactions');
    Route::post('/finance/transactions', [App\Http\Controllers\Web\TransactionController::class, 'store'])->name('finance.transactions.store');
    Route::delete('/finance/transactions/{transaction}', [App\Http\Controllers\Web\TransactionController::class, 'destroy'])->name('finance.transactions.destroy');

    Route::get('/finance/expenses', [App\Http\Controllers\Web\ExpenseController::class, 'index'])->name('finance.expenses');
    Route::post('/finance/expenses', [App\Http\Controllers\Web\ExpenseController::class, 'store'])->name('finance.expenses.store');
    Route::delete('/finance/expenses/{expense}', [App\Http\Controllers\Web\ExpenseController::class, 'destroy'])->name('finance.expenses.destroy');
    Route::post('/finance/expenses/categories', [App\Http\Controllers\Web\ExpenseController::class, 'storeCategory'])->name('finance.expenses.categories.store');
    // Campaigns Routes
    Route::get('/campaigns', [App\Http\Controllers\Web\CampaignController::class, 'index'])->name('campaigns.index');
    Route::post('/campaigns', [App\Http\Controllers\Web\CampaignController::class, 'store'])->name('campaigns.store');
    Route::patch('/campaigns/{campaign}/toggle', [App\Http\Controllers\Web\CampaignController::class, 'toggleStatus'])->name('campaigns.toggle');
    Route::delete('/campaigns/{campaign}', [App\Http\Controllers\Web\CampaignController::class, 'destroy'])->name('campaigns.destroy');
    Route::post('/campaigns/coupons', [App\Http\Controllers\Web\CampaignController::class, 'storeCoupon'])->name('campaigns.coupons.store');
    Route::delete('/campaigns/coupons/{coupon}', [App\Http\Controllers\Web\CampaignController::class, 'destroyCoupon'])->name('campaigns.coupons.destroy');

    // Reviews Routes
    Route::get('/reviews', [App\Http\Controllers\Web\ReviewController::class, 'index'])->name('reviews.index');
    Route::delete('/reviews/{review}', [App\Http\Controllers\Web\ReviewController::class, 'destroy'])->name('reviews.destroy');

    // Notifications Routes
    Route::get('/notifications', [App\Http\Controllers\Web\NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications', [App\Http\Controllers\Web\NotificationController::class, 'store'])->name('notifications.store');
    Route::post('/notifications/mark-all-read', [App\Http\Controllers\Web\NotificationController::class, 'markAllRead'])->name('notifications.mark-all-read');
    Route::patch('/notifications/{notification}/toggle-read', [App\Http\Controllers\Web\NotificationController::class, 'toggleRead'])->name('notifications.toggle-read');
    Route::delete('/notifications/{notification}', [App\Http\Controllers\Web\NotificationController::class, 'destroy'])->name('notifications.destroy');

    // Reports Routes
    Route::get('/reports', [App\Http\Controllers\Web\ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/{type}', [App\Http\Controllers\Web\ReportController::class, 'show'])->name('reports.show');

    // Settings Routes
    Route::get('/settings', [App\Http\Controllers\Web\SettingController::class, 'index'])->name('settings.index');
    Route::post('/settings/global', [App\Http\Controllers\Web\SettingController::class, 'updateGlobal'])->name('settings.global.update');
    Route::post('/settings/branch', [App\Http\Controllers\Web\SettingController::class, 'updateBranch'])->name('settings.branch.update');
});
