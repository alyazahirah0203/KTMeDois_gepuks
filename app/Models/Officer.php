<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Officer extends Model
{
    use HasFactory;

    protected $primaryKey = 'staff_id';
    protected $table = 'officers';
    
    protected $fillable = [
        'user_id',
        'staff_name',
        'department',
        'position'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}