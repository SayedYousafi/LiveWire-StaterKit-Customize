<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Supplier_order extends Model
{
    protected $guarded = [];

    public function orderTypes()
    {
        return $this->belongsTo(Supplier_type::class,'order_type_id','id');
    }
    public function supplier()
    {
        return $this->belongsTo(Supplier::class,'supplier_id','id');
    }

    public function status()
    {
        return $this->belongsTo(Order_status::class,'id', 'supplier_order_id');
    }
}
