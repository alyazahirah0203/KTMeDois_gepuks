<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $primaryKey = 'payment_id';
    protected $table = 'payments';
    
    protected $fillable = [
        'invoice_id',
        'payment_date',
        'payment_amount',
        'payment_status',
        'payment_method',
        'transaction_ref'
    ];

    protected $casts = [
        'payment_date' => 'date'
    ];

    public function invoice()
    {
        return $this->belongsTo(Invoice::class, 'invoice_id', 'invoice_id');
    }
}