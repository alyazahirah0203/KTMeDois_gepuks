<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VendorNotification extends Model
{
    protected $connection = 'vendor_db';
    protected $table = 'vendor_notifications';
    protected $primaryKey = 'id';
    
    protected $fillable = [
        'vendor_user_id',
        'title',
        'message',
        'type',
        'link',
        'is_read',
        'read_at'
    ];
    
    protected $casts = [
        'is_read' => 'boolean',
        'read_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];
    
    public function vendorUser()
    {
        return $this->belongsTo(VendorUser::class, 'vendor_user_id', 'id');
    }
    
    public function markAsRead()
    {
        $this->is_read = true;
        $this->read_at = now();
        $this->save();
    }
    
    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }
    
    public function scopeForVendor($query, $vendorUserId)
    {
        return $query->where('vendor_user_id', $vendorUserId);
    }
}