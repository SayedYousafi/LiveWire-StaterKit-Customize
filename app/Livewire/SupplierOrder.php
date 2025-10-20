<?php
namespace App\Livewire;

use App\Models\Confirm;
use App\Models\Dimension;
use App\Models\Item;
use App\Models\Order_item;
use App\Models\Order_status;
use App\Models\Supplier;
use App\Models\Supplier_order;
use App\Models\Supplier_type;
use App\Services\OrderItemService;
use Flux\Flux;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Title('Supplier Order')]
class SupplierOrder extends Component
{
    use WithPagination;

    public $itemOrders = [];

    public $cargoId;

    public $orderNo;

    public $masterIds;

    public $supplierId;

    public $tableId;

    public $terms;

    public $order_type_id;

    public $npr_remark;

    public $selectedSupplierName;

    public $supplier_id;

    public $probNo;

    public $checkNo;

    public $chkDetails;

    public $purchaseDetails = false;

    public $purchaseDetailsNo;

    public $editDetails;

    public $chkDetailsNo;

    public $m_id;

    public $count_item;

    public $count_purchased;

    public $ref_no;

    public $supplierOrderId;

    public $item_ID;

    public $txtProblem;

    public $problemId;

    public $problemType;

    public $checkId;

    public $currentQty;

    public $qtyIdForChange;

    public $remarks_cn;

    public $remark;

    public $newStatus;

    public $qty;

    public $SOID;

    public string $search = '';

    public string $title = 'Supplier Orders';

    public $param;

    public $status;

    public $statusId, $ean, $item_id, $weight, $length, $width, $height;
    public $confirmIssue = null;

    protected OrderItemService $orderItemService;

    public function boot(OrderItemService $orderItemService)
    {
        $this->orderItemService = $orderItemService;
    }

    public function render()
    {
        $sos = Supplier_order::with(['statuses', 'supplier', 'orderTypes', 'supplierItem'])
            ->whereHas('statuses', function ($query) {
                $query->whereNotIn('status', ['Shipped', 'Invoiced']);
            })
            ->latest() ->get();
            //->paginate(50);
            //dd($sos);
        return view('livewire.supplier-order')->with(
            [
                'sos'         => $sos,
                'itemOrders'  => $this->itemOrders,
                'title'       => $this->title,
                'order_types' => Supplier_type::all(),
                'suppliers'   => Supplier::all(),
                
            ]);
    }

    public function refreshItemOrders()
    {
        $items = $this->orderItemService->getItemOrders($this->supplierId)->get();
        //dd($items);
        $this->itemOrders        = $items;
        $this->count_item        = $items->count();
        $this->count_purchased   = $items->where('status', 'Purchased')->count();
        $this->purchaseDetailsNo = '';
        $this->purchaseDetails   = false;
        $this->dispatch('$refresh');
    }

    public function showTable($soid)
    {
        // dd($soid);
        $this->tableId    = $soid;
        $this->supplierId = $soid;
        $this->refreshItemOrders();
    }

    public function editSO($id)
    {
        $this->supplierOrderId = $id;

        $so                  = Supplier_order::findOrFail($id);
        $this->supplierId    = $so->supplier_id;
        $this->order_type_id = $so->order_type_id;
        $this->ref_no        = $so->ref_no;
        $this->remark        = $so->remark;

        Flux::modal('edit-so')->show();

    }

    public function updateSO()
    {
        Supplier_order::where('id', $this->supplierOrderId)
            ->update([
                'supplier_id'   => $this->supplierId,
                'order_type_id' => $this->order_type_id,
                'ref_no'        => $this->ref_no,
                'remark'        => $this->remark,
            ]);

        Order_status::where('supplier_order_id', "$this->supplierOrderId")->update(
            [
                'ref_no' => $this->ref_no,
            ]);
        $this->supplierId    = '';
        $this->order_type_id = '';
        $this->ref_no        = '';
        $this->remarks_cn    = '';
        $this->refreshItemOrders();
        Flux::modal('edit-so')->close();
        session()->flash('success', 'Supplier order updated successfully.');
    }

    public function payOrder($id)
    {
        $items = $this->orderItemService->getItemOrders($id)->get();
        // dd($items);
        $allPurchased = true;
        foreach ($items as $item) {
            if ($item->price_rmb == '0' || $item->price_rmb == '') {
                session()->flash('error', 'there are items with zero RMB Price !!!');
                break;
            } elseif ($item->status !== 'Purchased') {
                $allPurchased = false;
                // dd($allPurchased);
                session()->flash('error', 'Some ordered item(s) are not yet Purchased or are in diffrent status !!!');
                break; // Exit the loop early if any item is not purchases
            } elseif ($item->status === 'Paid') {
                session()->flash('success', 'Already Paid !!!');
            }

            if ($allPurchased) {
                // make it all paid
                // dd('good to go for payment');
                Order_status::where('supplier_order_id', $id)->update(
                    [
                        'status' => 'Paid',
                    ]);
                Supplier_order::where('id', $id)->update(
                    [
                        'paid' => 'Y',
                    ]);
                session()->flash('success', 'Paid added successfully !!!');
            }
        }
        $this->refreshItemOrders();
    }

    public function cancel()
    {
        $this->tableId           = '';
        $this->purchaseDetails   = false;
        $this->purchaseDetailsNo = '';
        Flux::modal('itemDimentions')->close();
        $this->checkId = null;
    }

    public function setBackSO($id)
    {
        Order_status::where('master_id', "$id")->update(
            [
                'status'            => 'NSO',
                'supplier_order_id' => null,
                'taric_id'          => null,
            ]);

        session()->flash('success', 'Supplier order set back to NSO successfully !!!');
        // if there is no more IDs in Status table delete the records from supplier order talbe as well.
        DB::table('supplier_orders')
            ->whereNotExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('order_statuses')
                    ->whereColumn('order_statuses.supplier_order_id', 'supplier_orders.id');
            })
            ->delete();
        // session()->flash('success', 'Supplier order set back to NSO successfully !');
        $this->refreshItemOrders();
    }

    public function changeQty($id)
    {
        $qtyToChange          = Order_status::where('id', $id)->first();
        $this->qtyIdForChange = $id;
        $this->currentQty     = $qtyToChange->qty_label;
        $this->remarks_cn     = $qtyToChange->remarks_cn;
        Flux::modal('edit-qty')->show();
    }

    public function updateQty()
    {
        $this->validate([
            'currentQty' => 'required',
            'remarks_cn' => 'required',
        ]);
        // \Log::info('updateQty called with qty: ' . $this->qty . ' and remarks: ' . $this->remarks_cn); // Debugging line
        Order_status::where('id', $this->qtyIdForChange)->update(
            [
                'qty_label'  => $this->currentQty,
                'remarks_cn' => $this->remarks_cn,
            ]);

        Flux::modal('edit-qty')->close();
        session()->flash('success', 'QTY and CN remarks updated successfully !!!');
        $this->refreshItemOrders();
    }

    public function openDetails($id)
    {
        // dd($id);
        $this->purchaseDetailsNo = $id;
        $this->purchaseDetails   = true;
        $this->probNo            = null;
        $this->editDetails       = false;
    }

    public function purchase($id, $suppItemId, $name)
    {
        Order_status::where('master_id', "$id")->update(
            [
                'status' => 'Purchased',
            ]);

        Confirm::create(
            [
                'item_id'    => $suppItemId,
                'confirm_by' => Auth::user()->name,
            ]);

        session()->flash('success', $name . ' Purchased & confirmed successfully !!!');

        $this->dispatch('scroll-to-top');
        $this->refreshItemOrders();
        $this->purchaseDetailsNo = '';
        $this->purchaseDetails   = false;
        $this->dispatch('$refresh');

    }

    public function setNprRemark($itemID)
    {
        date_default_timezone_set('Europe/Berlin');
        // dd($itemID);
        Item::where('id', "$itemID")->update(
            [
                'is_npr'     => 'Y',
                'npr_remark' => $this->npr_remark,

            ]);
        session()->flash('success', 'Newe Picture Remark set successfully !!!');
        $this->npr_remark = '';
    }

    public function getRefNo($id)
    {
        $this->supplierOrderId = $id;
        $so                    = Supplier_order::findOrFail($id);
        $this->ref_no          = $so->ref_no;
        Flux::modal('edit-refNo')->show();
    }

    public function updateRefNo()
    {
        Order_status::where('supplier_order_id', '=', $this->supplierOrderId)->update(
            [
                'ref_no' => $this->ref_no,
            ]);

        Supplier_order::where('id', '=', $this->supplierOrderId)->update(
            [
                'ref_no' => $this->ref_no,
            ]);

        session()->flash('success', 'Refrence # added successfully !!!');
        $this->supplierOrderId = false;
        Flux::modal('edit-refNo')->close();
        $this->refreshItemOrders(); // Fetch items again for next operation
    }

    // Define event listeners
    protected $listeners = [
        'confirmationCreated' => 'handleConfirmationCreated',
    ];

    public function handleConfirmationCreated($masterId)
    {
        // Call checkDetails with the masterId from the event
        $this->openCheck($masterId);
    }

    public function openCheck($id)
    {
        $this->chkDetailsNo = $id;
        $this->chkDetails   = true;
        $this->probNo       = null;
        // show or hide C_Problem button in check-order-details.blade.php
        $chkConfirm = Confirm::where('m_id', $id)->get('issues')->toArray();

// Extract only the "issues" values
        $issues = array_column($chkConfirm, 'issues');

        $this->confirmIssue = array_filter($issues, fn($v) => ! is_null($v)) ? 1 : null;

    }

    public function checkDetails($id)
    {

        // dd($id, 'checking strats here');
        Order_status::where('master_id', $id)->update(
            [
                'status' => 'Checked',
            ]);
        session()->flash('success', 'Checked successfully !!!');
        $this->chkDetailsNo = null;

        $this->refreshItemOrders();
    }

    public function pProblem($id)
    {
        $this->problemId  = $id;
        $status           = Order_status::where('master_id', $id)->select('problems')->first();
        $this->txtProblem = $status->problems ?? '';
        Flux::modal('edit-problem')->show();

    }

    public function updateProblem()
    {
        Order_status::where('master_id', $this->problemId)->update(
            [
                'problems' => $this->txtProblem,
                'status'   => 'P_Problem',
            ]);

        session()->flash('success', 'Purchase problem registered successfully !!!');
        $this->txtProblem = '';
        Flux::modal('edit-problem')->close();
        $this->refreshItemOrders();

    }

    public function cProblem($id)
    {
        $this->checkId = $id;

        // Fetch confirms with quality relationship
        $confirms = Confirm::with('quality')->where('m_id', $id)->get();

        // Fetch ordered quantity with null check
        $orderedItem = Order_item::where('master_id', $id)->first();
        if (! $orderedItem) {
            logger()->warning("No Order_item found for master_id: {$id}");
            $this->txtProblem = "Error: No order found for the given ID.";
            Flux::modal('edit-check-problem')->show();
            return;
        }
        $orderedQty = $orderedItem->qty ?? 0;

        $poorRemarks = [];
        $poorQty     = 0;

        // Process confirms with validation
        foreach ($confirms as $confirm) {
            // Check if quality relationship and name exist
            $qualityName = $confirm->quality && isset($confirm->quality->name)
            ? $confirm->quality->name
            : 'Unknown Quality';

            // Ensure poorQty is numeric
            $currentPoorQty = is_numeric($confirm->poorQty) ? (int) $confirm->poorQty : 0;

            $poorRemarks[] = "{$currentPoorQty}pcs {$qualityName}\n";
            $poorQty += $currentPoorQty;
        }

                                                 // Calculate OK quantity with validation
        $okQty = max(0, $orderedQty - $poorQty); // Ensure no negative quantity

        // Build problem text, handle empty poorRemarks
        $this->txtProblem = ! empty($poorRemarks)
        ? implode('', $poorRemarks) . "And OK Qty is: {$okQty}"
        : "No issues found. OK Qty is: {$okQty}";

        Flux::modal('edit-check-problem')->show();
    }

    public function updateStatus()
    {
        Order_status::where('master_id', $this->checkId)->update(
            [
                'problems' => $this->txtProblem,
                'status'   => 'C_Problem',
            ]);
        session()->flash('success', 'Check problem registered successfully !!!');
        $this->txtProblem = '';
        Flux::modal('edit-check-problem')->close();
        $this->refreshItemOrders();
    }

    public function adjustProblem($m_id)
    {
        $this->m_id = $m_id;
        $edit       = Order_status::where('id', $m_id)->first();
        // dd($edit);
        $this->newStatus   = $edit->status;
        $this->problemType = $edit->status;
        $this->remark      = $edit->problems;
        $this->SOID        = $edit->supplier_order_id;
        $this->qty         = $edit->qty_label;
        $this->remarks_cn  = $edit->remarks_cn;
        Flux::modal('adjust-problem')->show();

        // dd($this->m_id , 'waiting for Joschua Busniss logic');
    }

    public function editProblem()
    {
        // Check if the m_id is empty and show an error message
        if (empty($this->m_id)) {
            session()->flash('error', 'You need to refresh your page!');

            return; // Stop further execution if there's no m_id
        }

        // Prepare the base data for update
        $updateData = [
            'status'     => $this->newStatus,
            'problems'   => $this->remark,
            'qty_label'  => $this->qty,
            'remarks_cn' => $this->remarks_cn,
        ];

        // Check for a specific remark and adjust the data accordingly
        if ($this->remark !== 'C_Problem') {
            if ($this->newStatus === 'NSO') {
                $updateData['supplier_order_id'] = null;
            } else {
                $updateData['supplier_order_id'] = $this->SOID;
            }
        } else {

            $updateData['supplier_order_id'] = $this->SOID;
        }

        // Update the status with the prepared data
        Order_status::where('id', $this->m_id)->update($updateData);

        // Flash success message
        session()->flash('success', 'Problem updated successfully!');
        Flux::modal('adjust-problem')->close();
        $this->refreshItemOrders();
    }

    public $priceId;

    public $rmb_special_price;

    public function specialPriceSelected($id)
    {
        $this->priceId           = $id;
        $rmb                     = Order_status::findOrFail($this->priceId);
        $this->rmb_special_price = $rmb->rmb_special_price;
        Flux::modal('edit-price')->show();
    }

    public function setSpecialPrice()
    {
        $this->validate(['rmb_special_price' => 'required|gte:0|lte:9999']);
        Order_status::where('id', $this->priceId)->update([
            'rmb_special_price' => "$this->rmb_special_price",
        ]);
        session()->flash('success', 'Special price set successfully !');
        Flux::modal('edit-price')->close();
        $this->rmb_special_price = '';
    }

    public function dimensions($id, $ean, $item_id)
    {
        $this->statusId = $id;
        $this->ean      = $ean;
        $this->item_id  = $item_id;
        $dim            = Dimension::where('status_id', $id)->first();

        $this->weight = $dim->weight ?? '';
        $this->length = $dim->length ?? '';
        $this->width  = $dim->width ?? '';
        $this->height = $dim->height ?? '';
        $this->qty    = $dim->dimqty ?? '';

        Flux::modal('itemDimentions')->show();
    }

    public function update()
    {
        Dimension::updateOrCreate(
            ['status_id' => $this->statusId],
            [
                'item_id' => $this->item_id,
                'weight'  => $this->weight,
                'length'  => $this->length,
                'width'   => $this->width,
                'height'  => $this->height,
                'dimqty'  => $this->qty,
            ]
        );

        session()->flash('success', 'Item dimensions updated successfully!');
        Flux::modal('itemDimentions')->close();
    }

}


