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
        return $this->belongsTo(Vendor::class, 'vendor_id', 'supplierid');
    }

    public function vendorExternal()
    {
        return $this->belongsTo(VendorDB::class, 'vendor_id', 'SUPPLIERID');
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

    public function getVendorNameAttribute()
    {
        // First try the external vendor (for vendors from external DB)
        if ($this->vendorExternal) {
            return $this->vendorExternal->SUPPLIER_COMP_NAME;
        }
        
        // Then try the main vendor (for legacy vendors)
        if ($this->vendor) {
            return $this->vendor->supplier_comp_name;
        }
        
        // If still not found, try to find by vendor_id directly in external DB
        $vendor = VendorDB::where('SUPPLIERID', $this->vendor_id)->first();
        if ($vendor) {
            return $vendor->SUPPLIER_COMP_NAME;
        }
        
        // Last resort - check main vendors table
        $vendorMain = Vendor::where('supplierid', $this->vendor_id)->first();
        if ($vendorMain) {
            return $vendorMain->supplier_comp_name;
        }
        
        return 'Unknown Vendor (' . $this->vendor_id . ')';
    }
}