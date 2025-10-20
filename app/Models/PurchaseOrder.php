<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PurchaseOrder extends Model
{
    protected $guarded = [];

    public function item()
    {
        return $this->belongsTo(Item::class, 'item_id');
    }

    public function getTotalAttribute()
    {
        return $this->price * $this->qty;
    }
}
