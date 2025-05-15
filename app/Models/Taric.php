<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Taric extends Model
{
    protected $guarded = [];

    public function items()
    {
        return $this->hasMany(Item::class, 'taric_id', 'id');       
    }

    public function parents()
    {
        return $this->hasMany(Parents::class, 'taric_id', 'id');       
    }

    public function scopeSearch($query, $value)
    {
       $query->where('name_en', 'like', '%'.$value.'%')
        ->orWhere('name_de', 'like', '%'.$value.'%')
        ->orWhere('code', 'like', '%'.$value.'%')
        ->orWhere('description_en', 'like', '%'.$value.'%')
        ->orWhere('description_de', 'like', '%'.$value.'%')
        ->orWhere('duty_rate', 'like', '%'.$value.'%');
    }
}
