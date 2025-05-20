<?php
namespace App\Livewire;

use App\Models\Confirm;
use App\Models\Item;
use App\Models\Order_status;
use App\Models\Supplier;
use App\Models\Supplier_order;
use App\Models\Supplier_type;
use App\Services\OrderItemService;
use Flux\Flux;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class SupplierOrder extends Component
{
    public $itemOrders = [];

    public $cargoId, $orderNo, $masterIds, $supplierId, $tableId, $terms;
    public $order_type_id, $npr_remark, $selectedSupplierName, $supplier_id;
    public $probNo, $checkNo, $chkDetails, $purchaseDetails = false, $purchaseDetailsNo, $editDetails, $chkDetailsNo, $m_id, $count_item, $count_purchased;
    public $ref_no, $supplierOrderId, $item_ID, $txtProblem, $problemId, $problemType, $checkId;
    public $currentQty, $qtyIdForChange, $remarks_cn, $remark, $newStatus, $qty, $SOID;
    public string $search = '';
    public string $title  = 'Suppler Orders';
    public $param, $status;

    protected OrderItemService $orderItemService;

    public function boot(OrderItemService $orderItemService)
    {
        $this->orderItemService = $orderItemService;
    }

    public function render()
    {
        $sos = Supplier_order::with(['status', 'supplier', 'orderTypes'])
            ->whereHas('status', function ($query) {
                $query->whereNotNull('order_statuses.supplier_order_id');
            })->latest()->get();

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
        //dd($items);
        $allPurchased = true;
        foreach ($items as $item) {
            if ($item->price_rmb == '0' || $item->price_rmb == '') {
                session()->flash('error', 'there are items with zero RMB Price !!!');
                break;
            } elseif ($item->status !== "Purchased") {
                $allPurchased = false;
                //dd($allPurchased);
                session()->flash('error', 'Some ordered item(s) are not yet Purchased or are in diffrent status !!!');
                break; // Exit the loop early if any item is not purchases
            } elseif ($item->status === "Paid") {
                session()->flash('success', 'Already Paid !!!');
            }

            if ($allPurchased) {
                // make it all paid
                //dd('good to go for payment');
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
        //session()->flash('success', 'Supplier order set back to NSO successfully !');
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
        //\Log::info('updateQty called with qty: ' . $this->qty . ' and remarks: ' . $this->remarks_cn); // Debugging line
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
        //dd($id);
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
                'supp_items_id' => $suppItemId,
                'confirm_by'    => Auth::user()->name,
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
        //dd($itemID);
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
    public function openCheck($id)
    {
        //dd('Hi', $id);
        $this->chkDetailsNo = $id;
        $this->chkDetails   = true;
        $this->probNo       = null;

    }
    public function checkDetails($id)
    {
        //dd($id, 'checking strats here');
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
        $this->checkId    = $id;
        $status           = Order_status::where('master_id', $id)->select('problems')->first();
        $this->txtProblem = $status->problems ?? '';
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
        //dd($edit);
        $this->newStatus   = $edit->status;
        $this->problemType = $edit->status;
        $this->remark      = $edit->problems;
        $this->SOID        = $edit->supplier_order_id;
        $this->qty         = $edit->qty_label;
        $this->remarks_cn  = $edit->remarks_cn;
        Flux::modal('adjust-problem')->show();

        //dd($this->m_id , 'waiting for Joschua Busniss logic');
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

}
