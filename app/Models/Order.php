<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    /** @use HasFactory<\Database\Factories\OrderFactory> */
    use HasFactory;

    protected $guarded = [];

    public function scopeSearch($query, $term)
    {
        if (! $term) {
            return $query; // no filtering if no search term
        }

        return $query->where('order_no', 'like', '%'.$term.'%')
            ->orWhere('comment', 'like', '%'.$term.'%');
    }

    public function orderItems()
    {
        return $this->hasMany(Order_item::class, 'order_no', 'order_no');
    }

    public function categories()
    {
        return $this->belongsTo(Category::class, 'category_id', 'de_cat');
    }

    public function getStatusCountsAttribute(): array
    {
        return $this->orderItems
            ->filter(fn ($item) => $item->status) // Only items with a related status
            ->groupBy(fn ($item) => $item->status->status)
            ->map(fn ($group) => $group->count())
            ->toArray();
    }
}
