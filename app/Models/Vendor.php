<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vendor extends Model
{
    use HasFactory;

    protected $table = 'vendors';

    protected $fillable = [
        'supplierid',
        'supplier_comp_reg_no',
        'supplier_comp_name',
        'supplier_ctc_no',
        'supplier_ctc_person',
        'supplier_email_add',
        'supplier_expired_date',
        'supplier_ctc_status'
    ];

    protected $casts = [
        'supplier_expired_date' => 'date'
    ];

    public function isActive()
    {
        return $this->supplier_ctc_status === 'active' &&
               ($this->supplier_expired_date === null || $this->supplier_expired_date > now());
    }

    public function user()
    {
        return $this->hasOne(User::class, 'vendor_id', 'supplierid');
    }

    public function deliveryOrders()
    {
        return $this->hasMany(DeliveryOrder::class, 'vendor_id');
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class, 'vendor_id');
    }
}