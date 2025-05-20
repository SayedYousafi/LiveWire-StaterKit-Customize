<?php

namespace App\Livewire;

use Livewire\Component;
use App\Services\ItemDetail;

class ItemDetails extends Component
{
    public $itemId;
    
    protected ItemDetail $ItemDetail;

    public function boot(ItemDetail $ItemDetail)
    {
        $this->ItemDetail = $ItemDetail;
    }

    public function render()
    {
         $item = $this->ItemDetail->getItemDetial($this->itemId);
         //dd($item);
        return view('livewire.item-details')->with([
            'itemDetail' => $item,
        ]);
    }
}
