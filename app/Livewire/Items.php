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
    public $search = '', $param;
    public $title  = 'Items';
    public $selectedSupplier;
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
        $query = $this->ItemService->getItemsData($this->search);
        if ($this->param === 'zero') {
            // $query->whereHas('suppliers', fn($q) => $q->whereNull('price_rmb')->orWhere('price_rmb', 0))
            $query->where(function ($q) {
                $q->whereNull('supplier_items.price_rmb')->orWhere('supplier_items.price_rmb', 0);
            })
                ->where('items.is_rmb_special', 'N')
                ->where('items.isActive', 'Y');
        } elseif ($this->param === 'varval') {
            $query->where(function ($q) {
                $q->whereNull('variation_values.value_en')->orWhere('variation_values.value_en', '');
            })
                ->where('items.isActive', 'Y')
                ->where('parents.is_var_unilingual', 'N');
        } elseif ($this->param === 'noTarics') {
            $query->whereNull('items.taric_id');
        } elseif ($this->param === 'noCategory') {
            $query->whereNull('items.cat_id');
        } elseif ($this->param === 'npr') {
            $query->where('items.is_npr', 'Y')->where('items.isActive', 'Y');
        } elseif ($this->param === 'noPics') {
            $query->where('items.is_npr', 'Y')
                ->where('items.isActive', 'Y')
                ->where(function ($q) {
                    $q->whereNull('photo')
                        ->orWhereIn('photo', ['DummyPicture.jpg', '']);
                });

        } elseif ($this->param === 'naShipping') {
            $query->whereRaw("ShippingClass(items.weight, items.length, items.width, items.height) = 'Na'");
            //$query->whereNull('supplier_items.supplier_id');
        } elseif ($this->param === 'noSupplier') {
            // whereRaw("ShippingClass(titems.weight, titems.length, titems.width, titems.height) = 'Na'");
            $query->whereNull('supplier_items.supplier_id');
        } elseif (strpos($this->param, 'code') === 0) {
            $codeId = str_replace('code', '', $this->param);
            if (is_numeric($codeId)) {
                $query->where('taric_id', $codeId);
            } else {
                dd('Invalid code ID');
            }
        } elseif ($this->param === 'parentNONE') {
            $query->where('parent_no_de', 'NONE');
        } elseif (preg_match('/^parent(V\d{3}-\w{3})$/', $this->param, $matches)) {
            $parent_no_de = $matches[1];
            $query->where('parent_no_de', $parent_no_de);
        } elseif ($this->param) {
            $query->whereHas('suppliers', fn($q) => $q->where('supplier_id', $this->param));
        }
        //$query->where('parent_no_de','!=', 'NONE');
        $items = $query->orderBy('items.id', 'desc')->paginate(100);
        //dd($this->items);
        return view('livewire.items')->with([
            'items' => $items,
        ]);
    }

    public function suppliers($id)
    {
        $this->selectedSupplier = $id;
    }

    public function Save()
    {
        dd('Not yet developed');
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
