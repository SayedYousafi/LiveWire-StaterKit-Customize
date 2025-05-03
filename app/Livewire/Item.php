<?php

namespace App\Livewire;

use Flux\Flux;
use Livewire\Component;
use App\Models\Item as Items;
use Illuminate\Validation\Rules;

class Item extends Component
{
    public $name, $price, $itemId;
    public function render()
    {
        return view('livewire.item')->with([
            'items' => Items::all(),
        ]);
    }
    public function addItem()
    {
        $this->validate([
            'name' => 'required',
            'price' => 'required',
        ]);

        Items::create([
            'name' => $this->name,
            'price' => $this->price
        ]);
        session()->flash('success','Item added successfully !.');
        Flux::modal('add-item')->close();
        $this->reset();
    }
    public function editItem($id)
    {
        $this->itemId = $id;
        $item = Items::findOrFail($id);
        $this->name = $item->name;
        $this->price = $item->price;
        Flux::modal('edit-item')->show();
    }

    public function updateItem()
    {
        Items::where('id', $this->itemId )->update([
            'name' => $this->name,
            'price' => $this->price
        ]);
        session()->flash('success','Item updated successfully !.');
        Flux::modal('edit-item')->close();
        $this->reset();
    }

    public function deleteItem($id)
    {
        $item = Items::findOrFail($id);
        $item->delete();
        session()->flash('success','Item deleted successfully !.');
    }
}
