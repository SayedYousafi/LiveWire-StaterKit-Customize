<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Team extends Model
{
    protected $guarded = [];

    public function scopeSearch($query, $term)
    {
        return $query->when($term, function ($q) use ($term) {
            $q->where(function ($q) use ($term) {
                $q->where('first_name', 'like', '%' . $term . '%')
                    ->orWhere('middle_name', 'like', '%' . $term . '%')
                    ->orWhere('last_name', 'like', '%' . $term . '%')
                    ->orWhere('city', 'like', '%' . $term . '%')
                    ->orWhere('designation', 'like', '%' . $term . '%')
                    ->orWhere('country', 'like', '%' . $term . '%');
            });
        });
    }

    protected $casts = [
        'status' => 'boolean',

    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
