<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    protected $guarded = [];

    public static function scopeSearch($query, $value)
    {
        $query->where('customer_company_name', 'like', '%'.$value.'%')
            ->orWhere('email', 'like', '%'.$value.'%')
            ->orWhere('contact_first_name', 'like', '%'.$value.'%')
            ->orWhere('city', 'like', '%'.$value.'%')
            ->orWhere('phone', 'like', '%'.$value.'%')
            ->orWhere('contact_email', 'like', '%'.$value.'%');
    }

    public function customerType()
    {
        return $this->belongsTo(CustomerType::class);
    }
}
