<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    protected $primaryKey = 'notification_id';
    protected $table = 'notifications';
    
    protected $fillable = [
        'user_id',
        'title',
        'message',
        'type',
        'link',
        'is_read'
    ];
    
    protected $casts = [
        'is_read' => 'boolean',
        'created_at' => 'datetime'
    ];
    
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
    public function markAsRead()
    {
        $this->update(['is_read' => true]);
    }
}