<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order_item extends Model
{
    protected $guarded = [];

    public function supplierOrder()
    {
        return $this->belongsTo(Supplier_order::class, 'supplier_order_id', 'id');
    }

    public function item()
    {
        return $this->belongsTo(Item::class, 'item_id', 'id');
    }

    public function status()
    {
        return $this->hasOne(Order_status::class, 'master_id', 'master_id');
    }
}