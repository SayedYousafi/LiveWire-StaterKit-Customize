<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Parents extends Model
{
    protected $guarded = [];

    public function items()
    {
        return $this->hasMany(Item::class, 'parent_id');
    }

    public function scopeSearch($query, $term)
    {
        return $query->when($term, function ($q) use ($term) {
            $q->where(function ($query) use ($term) {
                $query->where('name_en', 'like', '%' . $term . '%')
                    ->orWhere('name_de', 'like', '%' . $term . '%')
                    ->orWhere('name_cn', 'like', '%' . $term . '%')
                    ->orWhere('de_no', 'like', '%' . $term . '%')
                    ->orWhere('var_en_1', 'like', '%' . $term . '%')
                    ->orWhere('var_de_1', 'like', '%' . $term . '%');
            });
        });
    }

    protected $casts = [
        'is_active'         => 'boolean',
        'is_var_unilingual' => 'boolean',
        'is_nwv'            => 'boolean',
    ];
}
