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
        'status',
        'reason'
    ];

    protected $casts = [
        'invoice_date' => 'date',
        'due_date' => 'date'
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
        return $this->belongsTo(Vendor::class, 'vendor_id');
    }

    public function items()
    {
        return $this->hasMany(InvoiceItem::class, 'invoice_id', 'invoice_id');
    }

    public function payments()
    {
        return $this->hasMany(Payment::class, 'invoice_id', 'invoice_id');
    }
}