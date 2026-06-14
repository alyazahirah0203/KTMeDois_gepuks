<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvoiceItem extends Model
{
    use HasFactory;

    protected $primaryKey = 'item_id';
    protected $table = 'invoice_items';
    
    protected $fillable = [
        'invoice_id',
        'product_code',
        'description',
        'uom',
        'quantity',
        'unit_price',
        'amount'
    ];

    protected static function booted()
    {
        static::creating(function ($item) {
            $item->amount = $item->quantity * $item->unit_price;
        });
    }

    public function invoice()
    {
        return $this->belongsTo(Invoice::class, 'invoice_id', 'invoice_id');
    }
}