<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Attachment extends Model
{
    protected $guarded = [];

    public function items()
    {
        return $this->belongsToMany(Item::class, 'attachment_item');
    }
}
