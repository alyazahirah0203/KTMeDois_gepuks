<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeliveryOrder extends Model
{
    use HasFactory;

    protected $primaryKey = 'do_id';
    protected $table = 'delivery_orders';
    
    protected $fillable = [
        'do_number',
        'po_number',
        'vendor_id',
        'order_date',
        'shipping_address',
        'invoice_address',
        'delivery_date',
        'delivery_time',
        'remarks',
        'receiver_signature',
        'status',
        'reason'
    ];

    protected $casts = [
        'order_date' => 'date',
        'delivery_date' => 'date'
    ];

    public function vendor()
    {
        return $this->belongsTo(Vendor::class, 'vendor_id');
    }

    public function items()
    {
        return $this->hasMany(DOItem::class, 'do_id', 'do_id');
    }

    public function invoice()
    {
        return $this->hasOne(Invoice::class, 'do_id', 'do_id');
    }

    public function isApproved()
    {
        return $this->status === 'Approved';
    }
}