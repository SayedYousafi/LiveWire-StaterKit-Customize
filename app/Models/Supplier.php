<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    protected $guarded = [];

    public static function scopeSearch($query, $value)
    {
        $query->where('name', 'like', '%' . $value . '%')
            ->orWhere('name_cn', 'like', '%' . $value . '%')
             ->orWhere('company_name', 'like', '%' . $value . '%')
            ->orWhere('city', 'like', '%' . $value . '%')
            ->orWhere('province', 'like', '%' . $value . '%')
         
            ->orWhere('contact_person', 'like', '%' . $value . '%');
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

    public function items()
    {
        return $this->belongsToMany(Item::class, 'supplier_items', 'supplier_id', 'item_id');
    }
}
