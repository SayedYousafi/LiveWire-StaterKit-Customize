<?php
namespace App\Livewire;

use App\Models\po;
use App\Models\PurchaseOrder;
use App\Services\OrderItemService;
use Livewire\Component;

class PurchaseOrders extends Component
{
    public $supplierOrderId;
    public $orderedItems;
    public $model = [];
    public $supplier_id, $item_id, $qty, $price;
    public $desc, $comment1, $comment2, $comment3, $comment4, $comment5, $comment6;
    public $editor    = null;
    public $viewingPo = null;
    public $pos;

    protected OrderItemService $orderItemService;

    public function boot(OrderItemService $orderItemService)
    {
        $this->orderItemService = $orderItemService;
    }

    public function render()
    {
        if (! $this->editor) {
            $this->orderedItems = $this->orderItemService->getItemOrders($this->supplierOrderId)
                ->get();
        }

        $this->supplier_id = $this->orderedItems->first()->SUPPID ?? null;
        $this->pos = $this->supplier_id ? po::with('supplier', 'purchaseOrders')->where('supplier_id', $this->supplier_id)->latest()->get() : collect();

        $getPOS = $this->pos->first();
        $this->desc     = $getPOS->desc ?? "";
        $this->comment1 = $getPOS->comment1 ?? "";
        $this->comment2 = $getPOS->comment2 ?? "";
        $this->comment3 = $getPOS->comment3 ?? "";
        $this->comment4 = $getPOS->comment4 ?? "";
        $this->comment5 = $getPOS->comment5 ?? "";
        $this->comment6 = $getPOS->comment6 ?? "";

        return view('livewire.purchase-orders')->with([
            'orderedItems' => $this->orderedItems,
            'pos'          => $this->pos,
        ]);
    }

    public function save()
    {
        $this->validate([
            'desc'     => 'nullable|string',
            'comment1' => 'nullable|string',
            'comment2' => 'nullable|string',
            'comment3' => 'nullable|string',
            'comment4' => 'nullable|string',
            'comment5' => 'nullable|string',
            'comment6' => 'nullable|string',
            'model.*'  => 'nullable|string',
        ]);

        $po = po::updateOrCreate(['id' => $this->editor], [
            'supplier_id' => $this->supplier_id,
            'desc'        => $this->desc,
            'comment1'    => $this->comment1,
            'comment2'    => $this->comment2,
            'comment3'    => $this->comment3,
            'comment4'    => $this->comment4,
            'comment5'    => $this->comment5,
            'comment6'    => $this->comment6,
        ]);

        // Delete existing PurchaseOrders for this PO to avoid duplicates
        PurchaseOrder::where('po_id', $po->id)->delete();

        // Create new PurchaseOrder records for each item
        foreach ($this->orderedItems as $index => $item) {
            PurchaseOrder::create([
                'po_id'   => $po->id,
                'item_id' => $item->item_id,
                'qty'     => $item->qty,
                'price'   => $item->price_rmb,
                'model'   => $this->model[$index] ?? null,
            ]);
        }

        $this->model    = [];
        $this->desc     = '';
        $this->comment1 = '';
        $this->comment2 = '';
        $this->comment3 = '';
        $this->comment4 = '';
        $this->comment5 = '';
        $this->comment6 = '';

        $isUpdate     = $this->editor ? true : false;
        $this->editor = null;

        $this->orderedItems = $this->orderItemService->getItemOrders($this->supplierOrderId)
            ->get();

        session()->flash('success', $isUpdate ? 'Purchase order updated successfully!' : 'Purchase orders created successfully!');
    }

    public function edit($id)
    {
        $this->editor = $id;
        $po           = po::with(['purchaseOrders.item', 'supplier'])->findOrFail($id);

        $this->supplier_id = $po->supplier_id;
        $this->desc     = $po->desc;
        $this->comment1 = $po->comment1;
        $this->comment2 = $po->comment2;
        $this->comment3 = $po->comment3;
        $this->comment4 = $po->comment4;
        $this->comment5 = $po->comment5;
        $this->comment6 = $po->comment6;

        $this->model = $po->purchaseOrders->pluck('model')->toArray();

        // Load orderedItems from the po's PurchaseOrders
        $this->orderedItems = $po->purchaseOrders->map(function ($purchaseOrder) use ($po) {
            return (object) [
                'SUPPID'       => $po->supplier_id,
                'supplierName' => $po->supplier ? ($po->supplier->name ?? 'N/A') : 'N/A',
                'item_id'      => $purchaseOrder->item_id,
                'item_name'    => $purchaseOrder->item ? ($purchaseOrder->item->item_name ?? 'N/A') : 'N/A',
                'qty'          => $purchaseOrder->qty,
                'price_rmb'    => $purchaseOrder->price,
            ];
        });
    }

    public function view($id)
    {
        $this->viewingPo = po::with([
            'purchaseOrders.item.attachments',
            'purchaseOrders.item.itemQualities',
            'supplier',
        ])->findOrFail($id);
    }

    public function closeView()
    {
        $this->viewingPo = null;
    }

    public function close($id)
    {
        $close = po::findOrFail($id);
        $close->update([
            'status' => 1,
        ]);

        session()->flash('success', 'PO closed successfully');
    }
}