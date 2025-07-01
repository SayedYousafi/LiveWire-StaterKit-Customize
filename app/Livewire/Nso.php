<?php

namespace App\Livewire;

use App\Models\Order_status;
use App\Models\Supplier_order;
use App\Services\OrderItemService;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Title('NSO')]
class Nso extends Component
{
    use WithPagination;

    public $catName;

    public $cargoId;

    public $orderNo;

    public $masterIds;

    public $supplierId;

    public $tableId;

    public $terms;

    public string $search = '';

    public string $title = 'NSOs';

    public $param;

    public $status;

    protected OrderItemService $orderItemService;

    public function boot(OrderItemService $orderItemService)
    {
        $this->orderItemService = $orderItemService;
    }

    public function render()
    {
        $baseQuery = $this->orderItemService->baseOrderQuery();
        // dd($baseQuery);
        $nsoOrders = $this->orderItemService
            ->finalizeOrderQuery(clone $baseQuery)
            ->where('order_statuses.status', 'NSO')
            ->where('orders.comment', 'NOT LIKE', '%expres%')
            ->orderBy('suppliers.id', 'ASC')
            ->get();
        // dd($nsoOrders);
        $expressOrders = $this->orderItemService
            ->finalizeOrderQuery(clone $baseQuery)
            ->where('order_statuses.status', 'NSO')
            ->where('orders.comment', 'LIKE', '%expres%')
            ->orderBy('suppliers.id', 'ASC')
            ->get();

        $itemOrders = $this->orderItemService
            ->getItemOrdersQuery($this->supplierId, $this->terms)
            ->where('order_statuses.status', 'NSO')
            ->get();

        return view('livewire.nso', [
            'nsoOrders' => $nsoOrders,
            'expressOrders' => $expressOrders,
            'itemOrders' => $itemOrders,
        ]);
    }

    public function showTable($id, $term)
    {
        $this->tableId = $id;
        $this->supplierId = $id;
        $this->terms = $term;
    }

    public function createSupplierOrder($id)
    {
        $this->supplierId = $id;
        $items = $this->orderItemService
            ->getItemOrdersQuery($this->supplierId, $this->terms)
            ->whereNull('order_statuses.supplier_order_id')
            ->get();
        //dd($id,$items);
        $firstItem = $items->first();
        $order_type_id = $firstItem->order_type_id;

        $supplierOrder = Supplier_order::create([
            'supplier_id' => $this->supplierId,
            'order_type_id' => $order_type_id,
            'remark' => '.',
            'ref_no' => null,
            'send2cargo' => 'N',
        ]);

        foreach ($items as $item) {
            $masterId=$item->master_id;
            Order_status::where('master_id', $masterId)->update([
                'status' => 'SO',
                'supplier_order_id' => $supplierOrder->id,
                'taric_id' => $item->taric_id,
            ]);
        }

        session()->flash('success', 'Supplier order created successfully !!!');
    }

    public function cancel()
    {
        $this->tableId = null;
        $this->supplierId = null;
        $this->terms = null;
    }
}
