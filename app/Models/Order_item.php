<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order_item extends Model
{
    protected $guarded = [];
    
        public function status()
    {
        return $this->hasOne(Order_status::class, 'master_id', 'master_id');
    }
}
