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

    public static function log($module, $action, $description, $status = 'Success')
    {
        return self::create([
            'timestamp' => now(),
            'username' => auth()->user()->name ?? 'system',
            'module' => $module,
            'action' => $action,
            'description' => $description,
            'status' => $status,
            'ip_address' => request()->ip()
        ]);
    }
}