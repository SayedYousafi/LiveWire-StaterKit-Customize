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

    public $param;

    public $title = 'Items';

    public $selectedSupplier;

    public $name, $ean;

    public $price;

    public $itemId;

    public $weight, $length, $width, $height;

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
        // Apply filters based on $param
        $this->applyParamFilters($query);
        $items = $query->orderBy('items.id', 'desc')->paginate(100);
        return view('livewire.items')->with([
            'items' => $items,
        ]);
    }

    private function applyParamFilters($query): void
    {
        $param = $this->param;

        // Handle regex-based dynamic patterns first
        if (preg_match('/^parentID-(\d+)$/', $param, $matches)) {
            $query->where('items.parent_id', $matches[1]);
            return;
        }

        if (preg_match('/^supplierID-(\d+)$/', $param, $matches)) {
            $query->where('suppliers.id', $matches[1]);
            return;
        }

        if (preg_match('/^taricID-(\d+)$/', $param, $matches)) {
            $query->where('items.taric_id', $matches[1]);
            return;
        }

        if (preg_match('/^categoryID-(\d+)$/', $param, $matches)) {
            $query->where('items.cat_id', $matches[1]);
            return;
        }

        // Handle all other static conditions with match()
        match ($param) {
            'zero' => $this->applyZeroFilter($query),
            'varval'     => $this->applyVariationValueFilter($query),
            'noTarics'   => $query->whereNull('items.taric_id'),
            'noCategory' => $query->whereNull('items.cat_id'),
            'npr'        => $query->where('items.is_npr', 'Y')->where('items.isActive', 'Y'),
            'is_new'     => $query->where('items.is_new', 'Y'),
            'noPics'     => $this->applyNoPicsFilter($query),
            'naShipping' => $query->where('items.isActive', 'Y')
                ->whereRaw("ShippingClass(items.weight, items.length, items.width, items.height) = 'Na'"),
            'edited'     => $query->whereColumn('items.synced_at', '<=', 'items.updated_at'),
            'noSupplier' => $query->whereNull('supplier_items.supplier_id'),
        //default => $this->handleDefaultCases($query, $param),
            default      => null,
        };
    }

    private function applyZeroFilter($query): void
    {
        $query->where(function ($q) {
            $q->whereNull('supplier_items.price_rmb')
                ->orWhere('supplier_items.price_rmb', 0);
        })
            ->where('items.is_rmb_special', 'N')
            ->where('items.isActive', 'Y');
    }

    private function applyVariationValueFilter($query): void
    {
        $query->where(function ($q) {
            $q->whereNull('variation_values.value_en')
                ->orWhere('variation_values.value_en', '');
        })
            ->where('items.isActive', 'Y')
            ->where('parents.is_var_unilingual', 'N');
    }

    private function applyNoPicsFilter($query): void
    {
        $query->where('items.is_npr', 'Y')
            ->where('items.isActive', 'Y')
            ->where(function ($q) {
                $q->whereNull('photo')
                    ->orWhereIn('photo', ['DummyPicture.jpg', '']);
            });
    }

    private function applyCodeFilter($query, string $param): void
    {
        $codeId = str_replace('code', '', $param);
        if (is_numeric($codeId)) {
            $query->where('taric_id', $codeId);
        } else {
            abort(400, 'Invalid code ID');
        }
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

    public function fixDimentions($id)
    {
        $this->itemId = $id;

        $item         = Item::findOrFail($id);
        $this->name   = $item->item_name;
        $this->weight = $item->weight;
        $this->length = $item->length;
        $this->width  = $item->width;
        $this->height = $item->height;
        $this->ean    = $item->ean;

        Flux::modal('itemDimentions')->show();
    }

    public function update()
    {
        Item::where('id', $this->itemId)->update([
            'weight' => $this->weight,
            'length' => $this->length,
            'width'  => $this->width,
            'height' => $this->height,
        ]);
        session()->flash('success', 'Item dimensions updated successfully !.');
        Flux::modal('itemDimentions')->close();

    }

    public function cancel()
    {
        $this->reset();
    }
}
