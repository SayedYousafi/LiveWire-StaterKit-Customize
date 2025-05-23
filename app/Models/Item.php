<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Item extends Model
{
    protected $guarded = [];

    public function warehouse():HasOne
    {
        return $this->hasOne(Warehouse::class);
    }

    public function supplierItem():HasOne
    {
        return $this->hasOne(Supplier_item::class);
    }

    public function categories():BelongsTo
    {
        return $this->belongsTo(Category::class, 'cat_id', 'id');
    }
    public function tarics():BelongsTo
    {
        return $this->belongsTo(Taric::class);
    }
    public function parents()
    {
        return $this->belongsTo(Parents::class, 'parent_id', 'id');
    }
}

