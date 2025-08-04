<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Holiday extends Model
{
    protected $guarded =[];

    public static function scopeSearch($query, $value)
    {
        $query->where('name', 'like', '%'.$value.'%')
            ->orWhere('type', 'like', '%'.$value.'%')
            ->orWhere('comments', 'like', '%'.$value.'%')
            ->orWhere('country', 'like', '%'.$value.'%')
            ->orWhere('day', 'like', '%'.$value.'%');
    }
}
