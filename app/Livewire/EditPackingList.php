<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\PackingList;
use Livewire\Attributes\Reactive;
use Illuminate\Support\Facades\DB;

class EditPackingList extends Component
{
    //#[Reactive]
    public $packingId ;
    public array $packingLists = [];

    public function render()
{
    $lists = PackingList::where('cargo_id', $this->packingId)->get();

    $this->packingLists = $lists->map(function ($list) {
        return [
            'id'              => $list->id,
            'itemDescription' => $list->item_description,
            'cn_description'  => $list->cn_description,
            'itemQty'         => $list->item_qty,
            'client1'         => $list->client1,
            'pallet'          => $list->pallet,
            'ptype'           => $list->ptype,
            'weight'          => (float) $list->weight,   // keep decimals here if needed
            'length'          => (int) round($list->length),
            'width'           => (int) round($list->width),
            'height'          => (int) round($list->height),
        ];
    })->toArray();

    return view('livewire.edit-packing-list')->with([
        'packingLists' => $this->packingLists,
    ]);
}


    public function updateList()
    {
        foreach ($this->packingLists as $entry) {
            $saved = PackingList::where('id', $entry['id'])->update([
                'cn_description'   => $entry['cn_description'],  
                'client1'          => $entry['client1'] ,
                'pallet'           => $entry['pallet'] ,
                'ptype'            => $entry['ptype'] ,
                'weight'           => $entry['weight'] ,
                'length'           => $entry['length'] ,
                'width'            => $entry['width'] ,
                'height'           => $entry['height'],
            ]);
        }
        if ($saved) {
            
            $this->dispatch('listUpdated');
            session()->flash('success', 'Packing list updated successfully!');
        }
    }
}
