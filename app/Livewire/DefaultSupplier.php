<?php
namespace App\Livewire;

use App\Models\Item;
use App\Models\Supplier;
use App\Models\Supplier_item;
use Flux\Flux;
use Livewire\Component;
use Livewire\WithPagination;

class DefaultSupplier extends Component
{
    use WithPagination;

    public $id;
    public bool $enableEdit = false;
    public bool $isUpdate   = true;
    public $itemId, $item_id, $supplier_id, $supplier_name, $item_name;
    public $is_default, $moq, $oi, $is_po, $price_rmb, $note_cn, $url, $lead_time;

    public function mount($id = null): void
    {
        $this->id = $id;
        //dd($this->id);
    }

    public function render()
    {
        return view('livewire.default-supplier', [
            'supplierItems' => Supplier_item::with(['item', 'supplier'])
                ->where('item_id', $this->id)
                ->paginate(50),
            'suppliers'     => Supplier::all(),
        ]);
    }

    public function cancel(): void
    {
        Flux::modal('defaultModal')->close();
    }

    public function getSuppItem($id): void
    {
        $this->supplier_id=$id;
        $this->loadSupplierItem($id);
        Flux::modal('defaultModal')->show();
    }

    public function insert($id): void
    {
        $this->loadSupplierItem($id);
        Flux::modal('defaultModal')->show();
    }

    protected function loadSupplierItem($id): void
    {
        $supplierItem = Supplier_item::with(['item', 'supplier'])->findOrFail($id);

        $this->supplier_name = $supplierItem->supplier->name;
        $this->item_name     = "{$supplierItem->item->item_name} / {$supplierItem->item->item_name_cn}";
        $this->is_default    = $supplierItem->is_default;
        $this->moq           = $supplierItem->moq;
        $this->is_po         = $supplierItem->is_po;
        $this->price_rmb     = $supplierItem->price_rmb;
        $this->note_cn       = $supplierItem->note_cn;
        $this->url           = $supplierItem->url;
        $this->oi           = $supplierItem->oi;
        $this->lead_time     = $supplierItem->lead_time;
        $this->itemId        = $supplierItem->suppItemId ?? $supplierItem->id;
        $this->item_id       = $supplierItem->item_id;
    }

    public function editSuppItem(): void
    {
        Supplier_item::where('id', $this->supplier_id)->update([
            'moq'       => $this->moq,
            'price_rmb' => $this->price_rmb,
            'url'       => $this->url,
            'note_cn'   => $this->note_cn,
            'is_po'     => $this->is_po,
            'oi'     => $this->oi,
            'lead_time' => $this->lead_time,
        ]);

        Item::where('id', $this->id)->update([
            'RMB_Price' => $this->price_rmb,
        ]);
        Flux::modal('defaultModal')->close();
        session()->flash('success', 'Supplier item updated successfully!');
    }

    public function store($item_id): void
    {
        Supplier_item::create([
            'item_id'     => $item_id,
            'supplier_id' => $this->supplier_id,
            'is_default'  => 'N',
            'moq'         => $this->moq,
            'oi'          => $this->oi,
            'price_rmb'   => $this->price_rmb,
            'url'         => $this->url,
            'note_cn'     => $this->note_cn,
            'is_po'       => $this->is_po,
            'lead_time'   => $this->lead_time,
        ]);
        Flux::modal('defaultModal')->close();
        session()->flash('success', 'New supplier inserted successfully!');
    }

    public function makeDefault($id, $itemId): void
    {
        Supplier_item::where('item_id', $itemId)->update(['is_default' => 'N']);
        Supplier_item::where('id', $id)->update(['is_default' => 'Y']);
        Item::where('id', $this->id)->update(['note' => 'Default supplier changed']);
        session()->flash('success', 'Default supplier changed successfully!');
    }

    public function deleteSupp($id): void
    {
        try {
            $supplier = Supplier_item::findOrFail($id);
            $supplier->delete();
            session()->flash('success', 'Non-default supplier deleted successfully');
        } catch (\Exception $e) {
            session()->flash('error', 'Something went wrong!');
        }
    }
}
