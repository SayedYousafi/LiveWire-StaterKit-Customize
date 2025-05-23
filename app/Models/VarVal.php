<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VarVal extends Model
{
    protected $table ='variation_values';
    protected $guarded = [];

    public function values()
    {
        return $this->belongsTo(Item::class, 'item_id');
    }
}
