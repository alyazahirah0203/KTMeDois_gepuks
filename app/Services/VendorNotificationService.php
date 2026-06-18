<?php

namespace App\Services;

use App\Models\VendorNotification;
use App\Models\VendorUser;
use Illuminate\Support\Facades\Auth;

class VendorNotificationService
{
    public function send($vendorUserId, $title, $message, $type = 'info', $link = null)
    {
        return VendorNotification::create([
            'vendor_user_id' => $vendorUserId,
            'title' => $title,
            'message' => $message,
            'type' => $type,
            'link' => $link
        ]);
    }
    
    public function sendToVendorBySupplierId($supplierId, $title, $message, $type = 'info', $link = null)
    {
        // Find vendor user by supplier ID (SUPPLIERID)
        $vendorUser = VendorUser::whereHas('vendor', function($query) use ($supplierId) {
            $query->where('SUPPLIERID', $supplierId);
        })->first();
        
        if ($vendorUser) {
            return $this->send($vendorUser->id, $title, $message, $type, $link);
        }
        
        return null;
    }
    
    public function markAsRead($notificationId)
    {
        $notification = VendorNotification::where('id', $notificationId)
            ->where('vendor_user_id', Auth::guard('vendor')->id())
            ->first();
            
        if ($notification) {
            $notification->markAsRead();
            return true;
        }
        return false;
    }
    
    public function markAllAsRead($vendorUserId)
    {
        return VendorNotification::where('vendor_user_id', $vendorUserId)
            ->where('is_read', false)
            ->update(['is_read' => true, 'read_at' => now()]);
    }
    
    public function getUnreadCount($vendorUserId)
    {
        return VendorNotification::where('vendor_user_id', $vendorUserId)
            ->where('is_read', false)
            ->count();
    }
    
    public function getNotifications($vendorUserId, $limit = 10)
    {
        return VendorNotification::where('vendor_user_id', $vendorUserId)
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }
    
    public function getAllNotifications($vendorUserId)
    {
        return VendorNotification::where('vendor_user_id', $vendorUserId)
            ->orderBy('created_at', 'desc')
            ->get();
    }
}