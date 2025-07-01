<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cci_invoice extends Model
{
    public $table = 'cci_invoices';

    protected $guarded = [];

    public function customer()
    {
        return $this->hasMany(Cci_customer::class, 'customer_id', 'cci_customer_id');
    }
}
