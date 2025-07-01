<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShortDescription extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $table = 'short_descriptions';

    public $timestamps = true;
}
