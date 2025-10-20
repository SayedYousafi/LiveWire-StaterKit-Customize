<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Item extends Model
{
    protected $guarded = [];

    public function warehouse(): HasOne
    {
        return $this->hasOne(Warehouse::class);
    }

    public function supplierItem(): HasOne
    {
        return $this->hasOne(Supplier_item::class);
    }

    public function categories(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'cat_id', 'id');
    }

    public function tarics(): BelongsTo
    {
        return $this->belongsTo(Taric::class);
    }

    public function parents()
    {
        return $this->belongsTo(Parents::class, 'parent_id', 'id');
    }

    public function values()
    {
        return $this->hasMany(VarVal::class, 'item_id', 'id');
    }

    public function qualities()
    {
        return $this->hasMany(ItemQuality::class, 'item_id', 'id');
    }

    public function purchaseOrders()
    {
        return $this->hasMany(PurchaseOrder::class, 'item_id');
    }

    public function attachments()
    {
        return $this->belongsToMany(Attachment::class, 'attachment_item');
    }

    public function itemQualities()
    {
        return $this->hasMany(ItemQuality::class, 'item_id');
    }
}