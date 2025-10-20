<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ItemQuality extends Model
{
    protected $guarded=[];

    public function item()
    {
        return $this->belongsTo(Item::class);
    }
}
