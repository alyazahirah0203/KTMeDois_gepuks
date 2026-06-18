<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasFactory;

    protected $primaryKey = 'invoice_id';
    protected $table = 'invoices';
    
    protected $fillable = [
        'invoice_no',
        'uuid',
        'do_id',
        'vendor_id',
        'invoice_date',
        'customer_no',
        'line_total',
        'service_tax',
        'shipping',
        'discount',
        'penalty',
        'total',
        'payments',
        'credits',
        'financial_charges',
        'balance_due',
        'payment_terms',
        'due_date',
        'proof_of_delivery',
        'pdf_path',
        'status',
        'reason',
        'rejected_at',
        'rejected_by'
    ];

    protected $casts = [
        'invoice_date' => 'date',
        'due_date' => 'date',
        'rejected_at' => 'datetime'
    ];

    public static function calculateTotal($lineTotal, $discount = 0, $penalty = 0)
    {
        $afterPenalty = $lineTotal - $penalty;
        $afterDiscount = $afterPenalty - $discount;
        $tax = $afterDiscount * 0.06;
        $total = $afterDiscount + $tax;
        
        return [
            'line_total' => round($lineTotal, 2),
            'discount' => round($discount, 2),
            'penalty' => round($penalty, 2),
            'tax' => round($tax, 2),
            'total' => round($total, 2),
            'balance_due' => round($total, 2)
        ];
    }

    public function deliveryOrder()
    {
        return $this->belongsTo(DeliveryOrder::class, 'do_id', 'do_id');
    }

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
        return $this->hasMany(InvoiceItem::class, 'invoice_id', 'invoice_id');
    }

    public function payments()
    {
        return $this->hasMany(Payment::class, 'invoice_id', 'invoice_id');
    }

    public function payment()
    {
        return $this->hasOne(Payment::class, 'invoice_id', 'invoice_id');
    }

    public function rejector()
    {
        return $this->belongsTo(User::class, 'rejected_by');
    }

    public function isRejected()
    {
        return $this->status === 'Rejected';
    }

    public function isSubmitted()
    {
        return $this->status === 'Submitted';
    }

    public function isFinanceReview()
    {
        return $this->status === 'Finance Review';
    }

    public function isPaymentProcessing()
    {
        return $this->status === 'Payment Processing';
    }

    public function isPaid()
    {
        return $this->status === 'Paid';
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