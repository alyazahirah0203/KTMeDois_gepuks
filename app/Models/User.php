<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'vendor_id'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function isVendor()
    {
        return $this->role === 'vendor';
    }

    public function isOfficer()
    {
        return $this->role === 'officer';
    }

    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    public function vendor()
    {
        return $this->belongsTo(Vendor::class, 'vendor_id', 'supplierid');
    }
}