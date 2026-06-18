<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\User;

class NotificationService
{
    public function send($userId, $title, $message, $type = 'info', $link = null)
    {
        return Notification::create([
            'user_id' => $userId,
            'title' => $title,
            'message' => $message,
            'type' => $type,
            'link' => $link
        ]);
    }
    
    public function sendToVendor($vendorId, $title, $message, $type = 'info', $link = null)
    {
        // This is for legacy vendors in main database
        $user = User::where('vendor_id', $vendorId)->first();
        if ($user) {
            return $this->send($user->id, $title, $message, $type, $link);
        }
        return null;
    }
    
    public function markAsRead($notificationId, $userId)
    {
        $notification = Notification::where('notification_id', $notificationId)
            ->where('user_id', $userId)
            ->first();
            
        if ($notification) {
            $notification->is_read = true;
            $notification->save();
            return true;
        }
        return false;
    }
    
    public function markAllAsRead($userId)
    {
        return Notification::where('user_id', $userId)
            ->where('is_read', false)
            ->update(['is_read' => true]);
    }
    
    public function getUnreadCount($userId)
    {
        return Notification::where('user_id', $userId)
            ->where('is_read', false)
            ->count();
    }
    
    public function getNotifications($userId, $limit = 10)
    {
        return Notification::where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }
}