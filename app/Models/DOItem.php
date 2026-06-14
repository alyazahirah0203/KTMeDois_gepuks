<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DOItem extends Model
{
    use HasFactory;

    protected $primaryKey = 'item_id';
    protected $table = 'do_items';
    
    protected $fillable = [
        'do_id',
        'item_no',
        'description',
        'quantity'
    ];

    public function deliveryOrder()
    {
        return $this->belongsTo(DeliveryOrder::class, 'do_id', 'do_id');
    }
}