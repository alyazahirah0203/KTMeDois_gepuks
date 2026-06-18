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

    public function isReviewOfficer()
    {
        return $this->role === 'review_officer';
    }

    public function isFinanceOfficer()
    {
        return $this->role === 'finance_officer';
    }

    public function isITOfficer()
    {
        return $this->role === 'it_officer';
    }

    public function isOfficer()
    {
        return $this->role === 'review_officer' || $this->role === 'finance_officer' || $this->role === 'it_officer';
    }

    public function vendor()
    {
        return $this->belongsTo(Vendor::class, 'vendor_id', 'supplierid');
    }

    public function officer()
    {
        return $this->hasOne(Officer::class, 'user_id');
    }

    // Helper to get vendor ID from either guard
    public static function getVendorId()
    {
        if (auth()->guard('vendor')->check()) {
            $vendorUser = auth()->guard('vendor')->user();
            return $vendorUser->vendor->SUPPLIERID ?? null;
        }
        
        if (auth()->check() && auth()->user()->isVendor()) {
            return auth()->user()->vendor_id;
        }
        
        return null;
    }

    // Helper to check if current user is vendor (from either guard)
    public static function isVendorUser()
    {
        if (auth()->guard('vendor')->check()) {
            return true;
        }
        
        if (auth()->check() && auth()->user()->isVendor()) {
            return true;
        }
        
        return false;
    }
}