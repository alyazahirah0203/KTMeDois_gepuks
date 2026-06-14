<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\InvoiceController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\DOItemController;
use App\Http\Controllers\NotificationController;

use App\Models\Invoice;
use App\Services\InvoiceSubmissionService;

Route::get('/create-test-notification', function() {
    \App\Models\Notification::create([
        'user_id' => auth()->id(),
        'title' => 'Test Notification',
        'message' => 'This is a test notification to verify the bell icon is working.',
        'type' => 'success',
        'link' => '/dashboard',
        'is_read' => false
    ]);
    
    return redirect()->back()->with('success', 'Test notification created!');
});

Route::middleware(['auth'])->group(function () {
    Route::post('/notifications/{id}/read', [NotificationController::class, 'markAsRead'])->name('notifications.mark-read');
    Route::post('/notifications/mark-all-read', [NotificationController::class, 'markAllAsRead'])->name('notifications.mark-all-read');
});

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/api/do-items/{doId}', [DOItemController::class, 'getItems']);

Auth::routes();

Route::get('/home', [HomeController::class, 'index'])->name('home');
Route::get('/dashboard', [HomeController::class, 'index'])->name('dashboard');

Route::middleware(['auth'])->group(function () {
    Route::prefix('invoices')->name('invoices.')->group(function () {
        Route::get('/create', [InvoiceController::class, 'create'])->name('create');
        Route::post('/store', [InvoiceController::class, 'store'])->name('store');
        Route::get('/track', [InvoiceController::class, 'track'])->name('track');
        Route::get('/{id}', [InvoiceController::class, 'show'])->name('show');
        Route::get('/{id}/download', [InvoiceController::class, 'download'])->name('download');
        Route::get('/{id}/status', [InvoiceController::class, 'getStatus'])->name('status');
    });
});