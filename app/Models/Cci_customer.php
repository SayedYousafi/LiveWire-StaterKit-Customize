<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cci_customer extends Model
{
    protected $guarded = [];

    public function lists()
    {
       return $this->hasMany(PackingList::class, 'customer_id', 'customer_id');
    }
}

 
