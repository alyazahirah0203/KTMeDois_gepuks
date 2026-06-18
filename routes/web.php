<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\DOController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\Api\DOItemController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Admin\UserManagementController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

// Authentication Routes
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Auth routes - disable registration and login
Auth::routes(['register' => false, 'login' => false]);

// Block register route just in case
Route::get('/register', function() { 
    abort(404); 
})->name('register');

// Vendor Dashboard - uses vendor guard
Route::get('/vendor/dashboard', [HomeController::class, 'vendorDashboard'])
    ->name('vendor.dashboard')
    ->middleware('auth:vendor');

// Home/Dashboard - for web guard users
Route::get('/home', [HomeController::class, 'index'])->name('home');
Route::get('/dashboard', [HomeController::class, 'index'])->name('dashboard');

Route::get('/api/do-items/{doId}', [DOItemController::class, 'getItems']);

// =============================================
// VENDOR MODULE ROUTES
// =============================================
Route::middleware(['auth'])->group(function () {
    Route::prefix('invoices')->name('invoices.')->group(function () {
        Route::get('/create', [InvoiceController::class, 'create'])->name('create');
        Route::post('/store', [InvoiceController::class, 'store'])->name('store');
        Route::get('/track', [InvoiceController::class, 'track'])->name('track');
        Route::get('/{id}', [InvoiceController::class, 'show'])->name('show');
        Route::get('/{id}/edit', [InvoiceController::class, 'edit'])->name('edit');
        Route::put('/{id}', [InvoiceController::class, 'update'])->name('update');
        Route::delete('/{id}', [InvoiceController::class, 'destroy'])->name('destroy');
        Route::get('/{id}/download', [InvoiceController::class, 'download'])->name('download');
        Route::get('/{id}/status', [InvoiceController::class, 'getStatus'])->name('status');
    });
});

Route::middleware(['auth'])->group(function () {
    Route::prefix('do')->name('do.')->group(function () {
        Route::get('/', [DOController::class, 'index'])->name('index');
        Route::get('/create', [DOController::class, 'create'])->name('create');
        Route::post('/store', [DOController::class, 'store'])->name('store');
        Route::get('/{id}', [DOController::class, 'show'])->name('show');
        Route::get('/{id}/edit', [DOController::class, 'edit'])->name('edit');
        Route::put('/{id}', [DOController::class, 'update'])->name('update');
        Route::delete('/{id}', [DOController::class, 'destroy'])->name('destroy');
        Route::post('/{id}/submit', [DOController::class, 'submit'])->name('submit');
        Route::post('/{id}/approve', [DOController::class, 'approve'])->name('approve');
        Route::post('/{id}/reject', [DOController::class, 'reject'])->name('reject');
        Route::get('/{id}/items', [DOController::class, 'getItems'])->name('items');
    });
});

// =============================================
// REVIEW MODULE ROUTES
// =============================================
Route::middleware(['auth'])->group(function () {
    Route::prefix('review')->name('review.')->group(function () {
        Route::get('/', [ReviewController::class, 'index'])->name('index');
        Route::get('/invoices', [ReviewController::class, 'invoices'])->name('invoices');
        Route::get('/invoices/{id}', [ReviewController::class, 'showInvoice'])->name('invoice.show');
        Route::post('/invoices/{id}/approve', [ReviewController::class, 'approve'])->name('invoice.approve');
        Route::post('/invoices/{id}/reject', [ReviewController::class, 'reject'])->name('invoice.reject');
        
        Route::get('/payment/{id}', [ReviewController::class, 'paymentForm'])->name('payment.form');
        Route::post('/payment/{id}', [ReviewController::class, 'processPayment'])->name('payment.process');
        Route::get('/payment/{id}/confirm', [ReviewController::class, 'confirmPayment'])->name('payment.confirm');
        Route::post('/payment/{id}/paid', [ReviewController::class, 'markAsPaid'])->name('payment.paid');
        Route::get('/payment/{id}/details', [ReviewController::class, 'viewPayment'])->name('payment.details');
        Route::get('/payment/{id}/edit', [ReviewController::class, 'editPayment'])->name('payment.edit');
        Route::put('/payment/{id}', [ReviewController::class, 'updatePayment'])->name('payment.update');
        Route::delete('/payment/{id}', [ReviewController::class, 'deletePayment'])->name('payment.delete');
        
        Route::get('/dos', [ReviewController::class, 'dos'])->name('dos');
        Route::get('/do/{id}', [ReviewController::class, 'showDO'])->name('do.show');
        Route::post('/do/{id}/approve', [ReviewController::class, 'approveDO'])->name('do.approve');
        Route::post('/do/{id}/reject', [ReviewController::class, 'rejectDO'])->name('do.reject');
        
        Route::get('/export', [ReviewController::class, 'export'])->name('export');
    });
});

// =============================================
// ADMIN MODULE ROUTES - ALL UNDER /dashboard/admin
// =============================================

// Admin Dashboard
Route::middleware(['auth'])->get('/dashboard/admin', [AdminController::class, 'dashboard'])
    ->name('dashboard.admin');

// All admin routes under /dashboard/admin
Route::middleware(['auth', 'it_officer'])->prefix('dashboard/admin')->name('dashboard.admin.')->group(function () {
    
    // User Management
    Route::get('/users', [UserManagementController::class, 'index'])->name('users.index');
    Route::get('/users/create', [UserManagementController::class, 'create'])->name('users.create');
    Route::post('/users', [UserManagementController::class, 'store'])->name('users.store');
    Route::get('/users/{id}/edit', [UserManagementController::class, 'edit'])->name('users.edit');
    Route::put('/users/{id}', [UserManagementController::class, 'update'])->name('users.update');
    Route::delete('/users/{id}', [UserManagementController::class, 'destroy'])->name('users.destroy');
    Route::post('/users/import', [UserManagementController::class, 'import'])->name('users.import');
    Route::get('/users/template', [UserManagementController::class, 'downloadTemplate'])->name('users.template');
    
    // Audit Logs
    Route::get('/audit-logs', [AdminController::class, 'auditLogs'])->name('audit-logs');
    Route::get('/audit-logs/export-pdf', [AdminController::class, 'exportAuditLogsPDF'])->name('audit-logs.export-pdf');
    Route::get('/audit-logs/export-csv', [AdminController::class, 'exportAuditLogsCSV'])->name('audit-logs.export-csv');
    
    // Reports
    Route::get('/reports', [AdminController::class, 'reports'])->name('reports');
});

// Notifications
Route::middleware(['auth'])->group(function () {
    Route::post('/notifications/{id}/read', [NotificationController::class, 'markAsRead']);
    Route::post('/notifications/mark-all-read', [NotificationController::class, 'markAllAsRead']);
});