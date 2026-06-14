<?php

namespace App\Services;

use App\Models\Notification;

class NotificationService
{
    public function send($userId, $title, $message, $type = 'info', $link = null)
    {
        return Notification::create([
            'user_id' => $userId,
            'title' => $title,
            'message' => $message,
            'type' => $type,
            'link' => $link,
            'is_read' => false
        ]);
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
    
    public function markAsRead($notificationId, $userId)
    {
        return Notification::where('notification_id', $notificationId)
            ->where('user_id', $userId)
            ->update(['is_read' => true]);
    }
    
    public function markAllAsRead($userId)
    {
        return Notification::where('user_id', $userId)
            ->where('is_read', false)
            ->update(['is_read' => true]);
    }
}