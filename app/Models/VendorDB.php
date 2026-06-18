<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VendorDB extends Model
{
    protected $connection = 'vendor_db';
    protected $table = 'vendors';
    protected $primaryKey = 'id';
    
    protected $fillable = [
        'SUPPLIERID',
        'SUPPLIER_COMP_REG_NO',
        'SUPPLIER_COMP_NAME',
        'SUPPLIER_CTC_NO',
        'SUPPLIER_CTC_PERSON',
        'SUPPLIER_EMAIL_ADD',
        'SUPPLIER_EXPIRED_DATE',
        'SUPPLIER_CTC_STATUS'
    ];
    
    protected $casts = [
        'SUPPLIER_EXPIRED_DATE' => 'date'
    ];
    
    public function users()
    {
        return $this->hasMany(VendorUser::class, 'vendor_id', 'id');
    }
    
    public function isActive()
    {
        return $this->SUPPLIER_CTC_STATUS === 'active' &&
               ($this->SUPPLIER_EXPIRED_DATE === null || $this->SUPPLIER_EXPIRED_DATE > now());
    }
    
    // Fix: Proper accessor for SUPPLIERID
    public function getSupplierIdAttribute()
    {
        return $this->attributes['SUPPLIERID'] ?? null;
    }
    
    // Fix: Proper accessor for company name
    public function getCompanyNameAttribute()
    {
        return $this->attributes['SUPPLIER_COMP_NAME'] ?? null;
    }
    
    // Fix: Proper accessor for SUPPLIER_COMP_NAME
    public function getSupplierCompNameAttribute()
    {
        return $this->attributes['SUPPLIER_COMP_NAME'] ?? null;
    }
    
    // Relationship to main Vendor model if needed for cross-database queries
    public function mainVendor()
    {
        return $this->hasOne(Vendor::class, 'supplierid', 'SUPPLIERID');
    }
    
    // Magic method to handle property access
    public function __get($key)
    {
        // If the key exists in attributes, return it
        if (array_key_exists($key, $this->attributes)) {
            return $this->attributes[$key];
        }
        
        // If there's an accessor method, use it
        $method = 'get' . ucfirst($key) . 'Attribute';
        if (method_exists($this, $method)) {
            return $this->$method();
        }
        
        // Check if it's a relationship
        if (method_exists($this, $key)) {
            return $this->getRelationValue($key);
        }
        
        return parent::__get($key);
    }
}