<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LeaveRequest extends Model
{
    protected $guarded = [];

    public function users()
    {
        return $this->hasMany(User::class, 'id', 'user_id');
    }
}
