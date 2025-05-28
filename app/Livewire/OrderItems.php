<?php
namespace App\Livewire;

use App\Models\Cargo;
use App\Models\Order_item;
use App\Models\Order_status;
use Flux\Flux;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Title('Order Items')]
class OrderItems extends Component
{
    use WithPagination;

    public $catName, $cargoId, $orderNo, $masterIds, $qty_no, $currentQty;
    public string $search = '';
    public string $title  = 'Order items';
    public $param, $status;

    public function render()
    {
        $query = DB::table('order_items')
            ->join('order_statuses', 'order_statuses.master_id', '=', 'order_items.master_id')
            ->join('items', 'items.ItemID_DE', '=', 'order_items.ItemID_DE')
            ->join('warehouse_items', 'warehouse_items.item_id', '=', 'items.id')
            ->join('supplier_items', 'supplier_items.item_id', '=', 'items.id')
            ->join('suppliers', 'suppliers.id', '=', 'supplier_items.supplier_id')
            ->where('supplier_items.is_default', 'Y');

        // Apply search (Livewire input)
        if (! empty($this->search)) {
            $query->where(function ($q) {
                $q->where('order_items.order_no', 'like', "%{$this->search}%")
                    ->orWhere('order_items.remark_de', 'like', "%{$this->search}%")
                    ->orWhere('items.item_name', 'like', "%{$this->search}%")
                    ->orWhere('items.item_name_cn', 'like', "%{$this->search}%")
                    ->orWhere('items.remark', 'like', "%{$this->search}%")
                    ->orWhere('warehouse_items.item_no_de', 'like', "%{$this->search}%")
                    ->orWhere('items.ean', 'like', "%{$this->search}%")
                    ->orWhere('supplier_items.note_cn', 'like', "%{$this->search}%")
                    ->orWhere('suppliers.name', 'like', "%{$this->search}%");
            });
        }

        // Filter by param (e.g., order number, item name, etc.)
        // Special case first
        if ($this->param === 'noCargo') {
            $query->whereNull('order_statuses.cargo_id');
        } elseif (! empty($this->param)) {
            $query->where(function ($q) {
                $q->where('order_items.order_no', '=', "$this->param");
            });
        }

        // Filter by status
        if (! empty($this->status)) {
            $query->where('order_statuses.status', $this->status);
        }

        $orderItems = $query->select('order_items.id AS ID', 'order_items.order_no', 'order_items.qty', 'order_statuses.remarks_cn',
            'order_statuses.cargo_id', 'order_statuses.status', 'order_items.remark_de', 'items.remark','order_statuses.master_id',
            'order_statuses.qty_split', 'order_statuses.qty_label',
            'items.ean', 'supplier_items.note_cn', 'items.item_name', 'items.item_name_cn', 'supplier_items.price_rmb', 'suppliers.id AS supplierId', 'suppliers.name')
            ->orderBy('order_items.order_no', 'DESC')
            ->orderBy('items.item_name', 'ASC')
            ->paginate(100);

        return view('livewire.order-items')->with([
            'orderItems' => $orderItems,
            'cargos'     => Cargo::where('cargo_status', 'Open')->pluck('cargo_no', 'id'),
        ]);
    }

    public function selectCargo($oNo)
    {
        //dd($oNo);
        $this->orderNo   = $oNo;
        $this->masterIds = Order_item::where('id', $this->orderNo)->pluck('master_id')->toArray();

        Flux::modal('myModal')->show();
    }

    public function changeCargo()
    {
        date_default_timezone_set('Europe/Berlin');

        Order_status::whereIn('master_id', $this->masterIds)
            ->update([
                'cargo_id'   => $this->cargoId,
                'cargo_date' => now(),
            ]);

        session()->flash('success', 'Cargo assigned successfully.');
        Flux::modal('myModal')->close();
    }

    public function splitDelivery($id)
    {
        //$this->changId = $id;
        $this->qty_no  = $id;
        Flux::modal('edit-qty')->show();
        $order    = Order_item::where('master_id', "$this->qty_no")->first();
        //dd($order);
        $newOrder = $order->toArray();
        unset($newOrder['id']);
        //unset($newOrder['master_id']);
        $newOrder['master_id'] = $newOrder['master_id'] . '-1';
        $duplicateOrder        = Order_item::create($newOrder);

        $item = Order_status::where('master_id', "$this->qty_no")->first();
        $stat = $item->toArray();
        unset($stat['id']);
        //unset($stat['master_id']);
        $stat['master_id'] = $stat['master_id'] . '-1';
        $duplicateStat     = Order_status::create($stat);
        if ($duplicateStat) {
            //Flux::modal('edit-qty')->close();
        }
    }
    public function updateQty()
    {
        $done = Order_status::where('master_id', "$this->qty_no")->first()
            ->update(
                [
                    'qty_split' => $this->currentQty,
                ]);
        if ($done) {
            session()->flash('success', 'QTY delivery set successfully !');
            $this->reset('currentQty');
            Flux::modal('edit-qty')->close();
        }
    }
}
