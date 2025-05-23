<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Supplier_item extends Model
{
    protected $guarded=[];
    

public function item()
{
    return $this->belongsTo(Item::class);
}

}
