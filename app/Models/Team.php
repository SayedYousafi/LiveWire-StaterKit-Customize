<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Team extends Model
{
    protected $guarded = [];

    public static function search($term)
    {
        return static::where('first_name', 'like', '%' . $term . '%')
            ->orWhere('middle_name', 'like', '%' . $term . '%')
             ->orWhere('last_name', 'like', '%' . $term . '%')
            ->orWhere('city', 'like', '%' . $term . '%')
            ->orWhere('designation', 'like', '%' . $term . '%')

            ->orWhere('country', 'like', '%' . $term . '%');
    }

    protected $casts = [
        'is_fully_prepared'   => 'boolean',
        'is_tax_included'     => 'boolean',
        'is_freight_included' => 'boolean',
    ];
}
