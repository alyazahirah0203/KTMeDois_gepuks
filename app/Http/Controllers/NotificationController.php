<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use App\Models\VendorNotification;
use App\Services\NotificationService;
use App\Services\VendorNotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    protected $notificationService;
    protected $vendorNotificationService;

    public function __construct(NotificationService $notificationService, VendorNotificationService $vendorNotificationService)
    {
        $this->middleware('auth');
        $this->notificationService = $notificationService;
        $this->vendorNotificationService = $vendorNotificationService;
    }

    public function markAsRead($id)
    {
        // Check if vendor is authenticated
        if (Auth::guard('vendor')->check()) {
            $result = $this->vendorNotificationService->markAsRead($id);
            return response()->json(['success' => $result]);
        }
        
        // Check if web user is authenticated
        if (Auth::check()) {
            $this->notificationService->markAsRead($id, Auth::id());
            return response()->json(['success' => true]);
        }
        
        return response()->json(['error' => 'Unauthorized'], 401);
    }

    public function markAllAsRead()
    {
        // Check if vendor is authenticated
        if (Auth::guard('vendor')->check()) {
            $this->vendorNotificationService->markAllAsRead(Auth::guard('vendor')->id());
            return response()->json(['success' => true]);
        }
        
        // Check if web user is authenticated
        if (Auth::check()) {
            $this->notificationService->markAllAsRead(Auth::id());
            return response()->json(['success' => true]);
        }
        
        return response()->json(['error' => 'Unauthorized'], 401);
    }
}