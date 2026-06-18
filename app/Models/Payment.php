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
        'transaction_ref',
        'proof_of_payment',
        'remarks',
        'processed_by'
    ];

    protected $casts = [
        'payment_date' => 'date',
        'payment_amount' => 'decimal:2'
    ];

    public function invoice()
    {
        return $this->belongsTo(Invoice::class, 'invoice_id', 'invoice_id');
    }

    public function processor()
    {
        return $this->belongsTo(User::class, 'processed_by');
    }
}