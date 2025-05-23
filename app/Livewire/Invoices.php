<?php
namespace App\Livewire;

use Flux\Flux;
use App\Models\Cargo;
use App\Models\Cci_customer;
use Livewire\Component;
use App\Models\Customer;
use App\Models\Order_item;
use App\Models\Order_status;
use App\Services\InvoiceService;

class Invoices extends Component
{
    protected InvoiceService $invoiceItemService;

    public function boot(InvoiceService $invoiceItemService)
    {
        $this->invoiceItemService = $invoiceItemService;
    }

    public $tariffNo;
    public $itemNo;
    public $reAssignId, $remarks_cn, $cargoId, $changId, $qty_no, $currentQty, $itemToSetId, $pricedTaricId, $special_code, $invSerialNo, 
    $customerId, $selectedMasterIds, $eur_special_price, $changIdnew_cust_inv_id, $new_cust_inv_id;

    public function render()
    {
        $items = $this->invoiceItemService->baseInvoiceQuery()
            ->groupBy('cargos.id')
            ->orderBy('cargos.id', 'DESC')
            ->get();
        //dd($items);
        return view('livewire.invoices')->with([
            'invoices'      => $items,
            'invoiceItems'  => $this->groupByItem($this->itemNo, $term = 'listByItem'),
            'invoiceTarics' => $this->groupByTaric($this->tariffNo, $term = 'listByTarics'),
            'cargos'        => Cargo::where('cargo_status', 'Open')->pluck('cargo_no', 'id'),
        ]);
    }
    public function groupByTaric($id, $term = null)
    {
        $this->tariffNo = $id;
        $tarics         = $this->invoiceItemService->getInvoices($this->tariffNo, $term)->get();
        //dd($tarics);
        return $tarics;
    }
    public function groupByItem($id, $term = null)
    {
        $this->itemNo = $id;
        $items        = $this->invoiceItemService->getInvoices($this->itemNo, $term)->get();
        return $items;
    }

    public function cancel()
    {
        $this->tariffNo       = null;
        $this->itemNo         = null;
        $this->changId        = null;
        $this->reAssignId     = null;
        $this->special_code   = '';
        $this->qty_no         = null;
        $this->itemToSetId    = null;
        session()->forget('verifiedRow');
        Flux::modal('edit-price')->close();
    }

    public function selectTotalPrice($taricId, $itemId)
    {
        //dd($taricId, $itemId);
        $this->pricedTaricId     = $itemId;
        $this->selectedMasterIds = $this->invoiceItemService->getInvoices($this->tariffNo)

            ->where('order_statuses.taric_id', $taricId) // Filtering by taric_id
            ->pluck('master_id')
            ->unique()
            ->toArray();
        //dd($this->selectedMasterIds);
    }

    public function setTarifCode()
    {
        //dd($this->selectedMasterIds);
        $set = Order_status::whereIn('master_id', $this->selectedMasterIds)
            ->update(
                [
                    'set_taric_code' => $this->special_code,
                ]);
        if ($set) {
            session()->flash('success', 'Selected taric code set  successfully !');
            $this->special_code = '';
        } else {
            session()->flash('error', 'something went wrong');
        }
        $this->pricedTaricId = null;
    }

    public function reAssign($id)
    {
        $this->reAssignId = $id;
        Flux::modal('myModal')->show();
    }
    public function changeCargo()
    {
        Order_status::where('master_id', "$this->reAssignId")
            ->update(
                [
                    'cargo_id' => $this->cargoId,
                ]);
        Flux::modal('myModal')->close();
        session()->flash('success', 'Cargo reassigned/changed successfully !');
    }

    public function selectQtyDelivery($id)
    {
        $this->qty_no     = $id;
        $this->reAssignId = null;
        $this->changId    = null;
        $qty              = Order_status::where('master_id', "$this->qty_no")->first();
        $this->currentQty = $qty->qty_split;
        Flux::modal('edit-qty')->show();
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
    public function splitDelivery($id)
    {
        $this->changId = $id;
        $this->qty_no  = $id;
        Flux::modal('edit-qty')->show();
        $order    = Order_item::where('master_id', "$this->qty_no")->first();
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
public function selectCode($id)
    {
        $this->changId      = $id;
        $itemToChange       = Order_status::where('master_id', "$this->changId")->first();
        $this->special_code = $itemToChange->special_code;
    }

    public function changeCode()
    {
        $set = Order_status::where('master_id', "$this->changId")->first()
            ->update(
                [
                    'taric_id' => $this->special_code,
                ]);
        if ($set) {
            session()->flash('success', 'Special code set with correct Taric_ID successfully, it will appear in the bill');
            $this->special_code = '';
        } else {
            session()->flash('error', 'something went wrong');
        }
    }

    public function itemToSet($id)
    {
        $this->itemToSetId = $id;
        //dd($id);
    }

    public function eurSpecialPrice()
    {
        $this->validate(['eur_special_price' => 'required']);
        //dd($this->eur_special_price);
        $done = Order_status::where('master_id', "$this->itemToSetId")
            ->update(
                [
                    'eur_special_price' => $this->eur_special_price,
                ]);
        if ($done) {
            session()->flash('success', 'EUR price set successfully !');
            $this->reset(['itemToSetId', 'eur_special_price']);
        }
    }

public function verifyAll($id)
{
    $chkTaricId = $this->invoiceItemService->getInvoices($id)->get();

    $hasSpecialTaricCode = false;
    $hasUnPrintedLabels = false;
    $countItemStatus = 0;

    foreach ($chkTaricId as $chkId) {
        if ($chkId->srqTaricID === 48) {
            $hasSpecialTaricCode = true;
            break;
        }

        if ($chkId->status !== 'Printed') {
            $hasUnPrintedLabels = true;
            $countItemStatus++;
        }
    }

    if ($hasSpecialTaricCode) {
        session()->flash('error', "This invoice cannot be closed, it has Special Taric Code");
        session()->forget('verifiedRow');
        return;
    }

    if ($hasUnPrintedLabels) {
        session()->flash('error', "This invoice cannot be closed, still have ($countItemStatus) unprinted labels");
        session()->forget('verifiedRow');
    } else {
        session()->flash('success', "All good, Good to go! click blue button to close this invoice!");
        session()->put('verifiedRow', $id);
    }
}

    public function checkStatus($id, $cust_id)
    {
        $this->customerId = $cust_id;
        //dd($customerId);
        $this->invSerialNo = substr(str_replace('.', '', microtime(true)), -7);
        // 1. create customer
        $this->createCustomer($this->customerId);
        // // 2. create item invoices
        // $this->createItems($id);
        // // 3. create Invoices
        // $this->createInvoice($id);
        // //4. set status to invoiced.
        // $this->statusInvoiced($id);
        // //change the cargo status from open to close;
        // $this->closeCargo($id);

        session()->flash('success', 'Invoice closed successfully !!! ');
    }

    // 1. create customer
    public function createCustomer($id)
    {
        $customer      = Customer::findOrFail($id);
        $this->customerId = $customer->id;
        //dd($customer);
            $cData = $customer->toArray();
            unset($cData['id']);
            unset($cData['custom_no']); // ignore the primary key for column consisitency
            $cData['customer_id']  = $customer->id;
            $cData['invSerialNo']  = $this->invSerialNo;
        
            $new_cust_inv          = Cci_customer::create($cData);
            if ($new_cust_inv) {
                session()->flash('success', 'Customer invoice created successfully !!! ');
                $this->new_cust_inv_id = $new_cust_inv->id;
            }
            else{
                session()->flash('error', 'something went wrong !!! ');
            }
        
    }

}
