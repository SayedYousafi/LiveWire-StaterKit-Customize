<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cargo_type extends Model
{
    protected $guarded = [];

    public function cargos()
    {
        return $this->belongsTo(Cargo_type::class);
    }
}
