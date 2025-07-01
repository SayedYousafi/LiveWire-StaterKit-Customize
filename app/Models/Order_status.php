<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order_status extends Model
{
    protected $guarded = [];

    public function dimension()
    {
        return $this->hasmay(Dimension::class, 'status_id', 'id');
    }
 
}
