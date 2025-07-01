<?php

namespace App\Models;

use Illuminate\Contracts\Auth\Guard;
use Illuminate\Database\Eloquent\Model;

class PackingList extends Model
{
    protected $guarded = [];

    public function customers()
    {
        return $this->belongsTo(Cci_customer::class, 'customer_id', 'customer_id');
    }
}
