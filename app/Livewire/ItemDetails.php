<?php

namespace App\Livewire;

use App\Services\ItemDetail;
use Livewire\Component;

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

        // dd($item);
        return view('livewire.item-details')->with([
            'itemDetail' => $item,
        ]);
    }
}
