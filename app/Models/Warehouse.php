<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Warehouse extends Model
{
    protected $guarded = [];
    protected $table ='warehouse_items';

    public function items()
    {
        return $this->hasOne(Item::class);
    }
    public function supplierItem()
    {
        return $this->hasOne(Supplier_item::class, 'item_id', 'item_id')->where('is_default', 'Y');
        //return $this->belongsTo(SupplierItem::class, 'item_id', 'item_id');
    }
    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}

