<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class po extends Model
{
    protected $guarded = [];

    public function purchaseOrders()
    {
        return $this->hasMany(PurchaseOrder::class, 'po_id');
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'supplier_id');
    }
}
