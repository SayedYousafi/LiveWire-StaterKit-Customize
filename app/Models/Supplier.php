<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    protected $guarded = [];

    public static function search($term)
    {
        return static::where('name', 'like', '%' . $term . '%')
            ->orWhere('name_cn', 'like', '%' . $term . '%')
             ->orWhere('company_name', 'like', '%' . $term . '%')
            ->orWhere('city', 'like', '%' . $term . '%')
            ->orWhere('province', 'like', '%' . $term . '%')
         
            ->orWhere('contact_person', 'like', '%' . $term . '%');
    }

    protected $casts = [
        'is_fully_prepared'   => 'boolean',
        'is_tax_included'     => 'boolean',
        'is_freight_included' => 'boolean',
    ];
    public function orderType()
    {
        return $this->belongsTo(Supplier_type::class, 'order_type_id', 'id');
    }
}
