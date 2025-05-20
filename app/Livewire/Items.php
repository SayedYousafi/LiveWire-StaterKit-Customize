<?php
namespace App\Livewire;

use App\Models\Item;
use App\Services\ItemService;
use Flux\Flux;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Title('Items')]
class Items extends Component
{
    use WithPagination;
    public $search = '';
    public $title  = 'Items';
    public $name, $price, $itemId, $update;

    protected ItemService $ItemService;

    public function boot(ItemService $ItemService)
    {
        $this->ItemService = $ItemService;
    }
    public function updatingSearch()
    {
        $this->resetPage(); // This resets pagination when search changes
    }
    public function render()
    {
        $items = $this->ItemService->getItemsData($this->search)->paginate(100);
        //dd($this->items);
        return view('livewire.items')->with([
            'items' => $items,
        ]);
    }
    public function Save()
    {
        $this->validate([
            'name'  => 'required',
            'price' => 'required',
        ]);

        Item::create([
            'name'  => $this->name,
            'price' => $this->price,
        ]);
        session()->flash('success', 'Item added successfully !.');
        Flux::modal('myModal')->close();
        $this->reset();
    }
    public function edit($id)
    {
        $this->itemId = $id;
        $this->update = true;
        $item         = Item::findOrFail($id);
        $this->name   = $item->name;
        $this->price  = $item->price;
        Flux::modal('myModal')->show();
    }

    public function Update()
    {
        Item::where('id', $this->itemId)->update([
            'name'  => $this->name,
            'price' => $this->price,
        ]);
        session()->flash('success', 'Item updated successfully !.');
        Flux::modal('myModal')->close();
        $this->reset();
    }

    public function delete($id)
    {
        $item = Item::findOrFail($id);
        $item->delete();
        session()->flash('success', 'Item deleted successfully !.');
    }

    public function cancel()
    {
        $this->update = false;
        $this->reset();
    }
}
