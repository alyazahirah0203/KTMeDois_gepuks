<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AuditLog extends Model
{
    use HasFactory;

    protected $primaryKey = 'log_id';
    protected $table = 'audit_logs';
    
    protected $fillable = [
        'timestamp',
        'username',
        'module',
        'action',
        'description',
        'status',
        'ip_address'
    ];

    protected $casts = [
        'timestamp' => 'datetime', 
    ];

    public static function log($module, $action, $description, $status = 'Success')
    {
        $username = 'system';
        
        // Check if vendor is authenticated (from external database)
        if (auth()->guard('vendor')->check()) {
            $vendorUser = auth()->guard('vendor')->user();
            
            // Try to get company name from vendor
            if ($vendorUser->vendor) {
                $username = $vendorUser->vendor->SUPPLIER_COMP_NAME . ' (Vendor)';
            } else {
                $username = $vendorUser->name . ' (Vendor)';
            }
        } 
        // Check if web user is authenticated (officer/admin from main database)
        elseif (auth()->check()) {
            $user = auth()->user();
            $username = $user->name ?? 'system';
            
            // Add role to username for clarity
            if ($user->isITOfficer()) {
                $username .= ' (IT Admin)';
            } elseif ($user->isReviewOfficer()) {
                $username .= ' (Review Officer)';
            } elseif ($user->isFinanceOfficer()) {
                $username .= ' (Finance Officer)';
            } elseif ($user->isVendor()) {
                $username .= ' (Vendor)';
            }
        }

        return self::create([
            'timestamp' => now(),
            'username' => $username,
            'module' => $module,
            'action' => $action,
            'description' => $description,
            'status' => $status,
            'ip_address' => request()->ip()
        ]);
    }
}