<?php

namespace App\Livewire;

use Flux\Flux;
use Livewire\Component;
use App\Models\Item;
use Illuminate\Validation\Rules;

class Items extends Component
{
    public $name, $price, $itemId, $update;
    public function render()
    {
        return view('livewire.items')->with([
            'items' => Item::all(),
        ]);
    }
    public function addItem()
    {
        $this->validate([
            'name' => 'required',
            'price' => 'required',
        ]);

        Item::create([
            'name' => $this->name,
            'price' => $this->price
        ]);
        session()->flash('success','Item added successfully !.');
        Flux::modal('itemModal')->close();
        $this->reset();
    }
    public function edit($id)
    {
        $this->itemId = $id;
        $this->update = true;
        $item = Item::findOrFail($id);
        $this->name = $item->name;
        $this->price = $item->price;
        Flux::modal('itemModal')->show();
    }

    public function updateItem()
    {
        Item::where('id', $this->itemId )->update([
            'name' => $this->name,
            'price' => $this->price
        ]);
        session()->flash('success','Item updated successfully !.');
        Flux::modal('itemModal')->close();
        $this->reset();
    }

    public function delete($id)
    {
        $item = Item::findOrFail($id);
        $item->delete();
        session()->flash('success','Item deleted successfully !.');
    }
}
