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

    Route::get('/appointments/available-slots', [AppointmentController::class, 'availableSlots'])->name('appointments.available-slots');
    Route::resource('appointments', AppointmentController::class)->except(['destroy']);
    Route::patch('/appointments/{appointment}/status', [AppointmentController::class, 'updateStatus'])->name('appointments.update-status');

    Route::resource('employees', App\Http\Controllers\Web\EmployeeController::class);
    Route::resource('customers', App\Http\Controllers\Web\CustomerController::class);
    Route::resource('services', App\Http\Controllers\Web\ServiceController::class);
    Route::patch('/services/{service}/toggle-status', [App\Http\Controllers\Web\ServiceController::class, 'toggleStatus'])->name('services.toggle-status');
    Route::view('/finance/transactions', 'finance.transactions')->name('finance.transactions');
    Route::view('/finance/expenses', 'finance.expenses')->name('finance.expenses');
    Route::view('/campaigns', 'campaigns.index')->name('campaigns.index');
    Route::view('/reviews', 'reviews.index')->name('reviews.index');
    Route::view('/notifications', 'notifications.index')->name('notifications.index');
    Route::get('/reports', [App\Http\Controllers\Web\ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/{type}', [App\Http\Controllers\Web\ReportController::class, 'show'])->name('reports.show');
    Route::view('/settings', 'settings.index')->name('settings.index');
});
