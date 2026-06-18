<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class VendorUser extends Authenticatable
{
    use Notifiable;
    
    protected $connection = 'vendor_db';
    protected $table = 'vendor_users';
    protected $primaryKey = 'id';
    
    protected $fillable = [
        'vendor_id',
        'name',
        'email',
        'password',
        'role',
        'status',
        'last_login'
    ];
    
    protected $hidden = [
        'password',
        'remember_token',
    ];
    
    protected $casts = [
        'last_login' => 'datetime',
    ];
    
    public function vendor()
    {
        return $this->belongsTo(VendorDB::class, 'vendor_id', 'id');
    }
    
    public function notifications()
    {
        return $this->hasMany(VendorNotification::class, 'vendor_user_id', 'id');
    }
    
    public function unreadNotifications()
    {
        return $this->notifications()->where('is_read', false);
    }
    
    public function isActive()
    {
        return $this->status === 'active';
    }
    
    public function isAdmin()
    {
        return $this->role === 'admin';
    }
}