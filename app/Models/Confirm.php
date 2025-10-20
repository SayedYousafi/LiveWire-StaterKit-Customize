<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Confirm extends Model
{
    protected $guarded = [];

    public function quality()
    {
        return $this->belongsTo(ItemQuality::class, 'quality_id', 'id');
    }
}
