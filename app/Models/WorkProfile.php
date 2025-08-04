<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WorkProfile extends Model
{
    protected $guarded = [];
    protected $casts   = ['working_days' => 'array'];

    public function users()
    {
        return $this->hasMany(User::class);
    }
}
