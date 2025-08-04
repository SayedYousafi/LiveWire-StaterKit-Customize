<?php

namespace App\Livewire;

use App\Models\Cargo;
use App\Models\Cci_customer;
use App\Models\Cci_invoice;
use App\Models\Cci_item;
use App\Models\Customer;
use App\Models\Order_item;
use App\Models\Order_status;
use App\Models\Taric;
use App\Services\InvoiceService;
use Flux\Flux;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Open invoices')]
class Invoices extends Component
{
    protected InvoiceService $invoiceItemService;

    public function boot(InvoiceService $invoiceItemService)
    {
        $this->invoiceItemService = $invoiceItemService;
    }

    public string $title = 'invoices';

    public $tariffNo;

    public $itemNo;

    public $reAssignId;

    public $remarks_cn;

    public $cargoId;

    public $changId;

    public $qty_no;

    public $currentQty;

    public $itemToSetId;

    public $pricedTaricId;

    public $special_code;

    public $invSerialNo;

    public $customerId;

    public $ean;

    public $eur;

    public $rmb;

    public $qty;

    public $taricCode;

    public $item_name;

    public $selectedMasterIds;

    public $eur_special_price;

    public $changIdnew_cust_inv_id;

    public $new_cust_inv_id;

    public function render()
    {
        $items = $this->invoiceItemService->baseInvoiceQuery()
            ->where('cargo_status', '!=', 'Shipped')
            ->groupBy('cargos.id')
            ->orderBy('cargos.id', 'DESC')
            ->get();

        // dd($items);
        return view('livewire.invoices')->with([
            'invoices' => $items,
            'invoiceItems' => $this->groupByItem($this->itemNo, $term = 'listByItem'),
            'invoiceTarics' => $this->groupByTaric($this->tariffNo, $term = 'listByTarics'),
            'cargos' => Cargo::where('cargo_status', 'Open')->pluck('cargo_no', 'id'),
        ]);
    }

    public function groupByTaric($id, $term = null)
    {
        $this->tariffNo = $id;
        $tarics = $this->invoiceItemService->getInvoices($this->tariffNo, $term)->get();

        // dd($tarics);
        return $tarics;
    }

    public function groupByItem($id, $term = null)
    {
        $this->itemNo = $id;
        $items = $this->invoiceItemService->getInvoices($this->itemNo, $term)->get();
        //dd($items);
        return $items;
    }

    public function cancel()
    {
        $this->tariffNo = null;
        $this->itemNo = null;
        $this->changId = null;
        $this->reAssignId = null;
        $this->special_code = '';
        $this->qty_no = null;
        $this->itemToSetId = null;
        session()->forget('verifiedRow');
        Flux::modal('edit-price')->close();
    }

    public function selectTotalPrice($taricId, $itemId)
    {
        // dd($taricId, $itemId);
        $this->pricedTaricId = $itemId;
        $this->selectedMasterIds = $this->invoiceItemService->getInvoices($this->tariffNo)

            ->where('order_statuses.taric_id', $taricId) // Filtering by taric_id
            ->pluck('master_id')
            ->unique()
            ->toArray();
        // dd($this->selectedMasterIds);
    }

    public function setTarifCode()
    {
        // dd($this->selectedMasterIds);
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
        $this->qty_no = $id;
        $this->reAssignId = null;
        $this->changId = null;
        $qty = Order_status::where('master_id', "$this->qty_no")->first();
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
        $this->qty_no = $id;
        Flux::modal('edit-qty')->show();
        $order = Order_item::where('master_id', "$this->qty_no")->first();
        $newOrder = $order->toArray();
        unset($newOrder['id']);
        // unset($newOrder['master_id']);
        $newOrder['master_id'] = $newOrder['master_id'].'-1';
        $duplicateOrder = Order_item::create($newOrder);

        $item = Order_status::where('master_id', "$this->qty_no")->first();
        $stat = $item->toArray();
        unset($stat['id']);
        // unset($stat['master_id']);
        $stat['master_id'] = $stat['master_id'].'-1';
        $duplicateStat = Order_status::create($stat);
        if ($duplicateStat) {
            // Flux::modal('edit-qty')->close();
        }
    }

    public function selectCode($id)
    {
        $this->changId = $id;
        $itemToChange = Order_status::where('master_id', "$this->changId")->first();
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
        // dd($id);
    }

    public function eurSpecialPrice()
    {
        $this->validate(['eur_special_price' => 'required']);
        // dd($this->eur_special_price);
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
            session()->flash('error', 'This invoice cannot be closed, it has Special Taric Code');
            session()->forget('verifiedRow');

            return;
        }

        if ($hasUnPrintedLabels) {
            session()->flash('error', "This invoice cannot be closed, still have ($countItemStatus) unprinted labels");
            session()->forget('verifiedRow');
        } else {
            session()->flash('success', 'All good, Good to go! click blue button to close this invoice!');
            session()->put('verifiedRow', $id);
        }
    }

    public function checkStatus($id, $cust_id)
    {
        $this->customerId = $cust_id;
        // dd($customerId);
        $this->invSerialNo = substr(str_replace('.', '', microtime(true)), -7);
        // 1. create customer
        $this->createCustomer($this->customerId);
        // // 2. create item invoices
        $this->createItems($id);
        // // 3. create Invoices
        $this->createInvoice($id);
        // //4. set status to invoiced.
        $this->statusInvoiced($id);
        // //change the cargo status from open to close;
        // $this->closeCargo($id);

        session()->flash('success', 'Invoice closed successfully !!! ');
    }

    // 1. create customer
    public function createCustomer($id)
    {
        $customer = Customer::findOrFail($id);
        $this->customerId = $customer->id;
        // dd($customer);
        $cData = $customer->toArray();
        unset($cData['id']);
        unset($cData['custom_no']); // ignore the primary key for column consisitency
        $cData['customer_id'] = $customer->id;
        $cData['invSerialNo'] = $this->invSerialNo;

        $new_cust_inv = Cci_customer::create($cData);
        if ($new_cust_inv) {
            session()->flash('success', 'Customer invoice created successfully !!! ');
            $this->new_cust_inv_id = $new_cust_inv->id;
        } else {
            session()->flash('error', 'something went wrong !!! ');
        }
    }

    // 2. create item invoices
    public function createItems($id)
    {
        // retrieve and create cci_items table
        $invoicedItems = $this->invoiceItemService->getInvoices($id)->get();
        // dd($invoicedItems);
        foreach ($invoicedItems as $item) {

            $this->ean = $item->ean;

            // Handle the Tariff logic for itemTaricId === 48
            if ($item->itemTaricId === 48) {
                $itemCode = Taric::where('id', $item->srqTaricID)
                    ->select('name_en', 'code')
                    ->first();

                if ($itemCode) {
                    $this->taricCode = $itemCode->code;
                    // Optionally assign the name if needed

                } else {
                    // Fallback values if no tariff found
                    $this->taricCode = 'n/a';
                    $this->item_name = 'Unknown Item';
                }
            } else {
                // Handle other items
                if (! empty($item->set_taric_code) && $item->set_taric_code !== $item->code) {
                    // Use set_taric_code if it's present and different from code
                    $this->taricCode = $item->set_taric_code;
                } else {
                    // Use code as the default if set_taric_code is missing or equal to code
                    $this->taricCode = $item->code;
                }

                // Set item_name from item
                $this->item_name = $item->item_name;
            }

            // Set RMB and EUR prices based on the conditions
            $this->rmb = ($item->is_rmb_special == 'Y') ? $item->rmb_special_price : $item->price_rmb;

            if ($item->is_eur_special == 'Y') {
                $this->eur = $item->eur_special_price;
            }

            // Handle qty_split logic
            $this->qty = ($item->qty == $item->qty_split) ? $item->qty : $item->qty_split;

            $cci_item = Cci_item::create([
                'invSerialNo' => $this->invSerialNo,
                'cci_customer_id' => $this->customerId,
                'cargo_id' => $id,
                'ean' => $this->ean,
                'item_name' => $this->item_name,
                'tariff_code' => $this->taricCode,
                'qty' => $this->qty,
                'rmb' => $this->rmb,
                'eur' => $this->eur,
            ]);
        }
    }

    public function createInvoice($cargoId)
    {
        // $invoiceItems = $this->invoiceItemService->getInvoices($cargoId)->get();
        $invoiceItems = $this->invoiceItemService->getInvoices($cargoId, 'listByTarics')->get();
        // dd($invoiceItems);
        $totalPrice = 0;
        $totalQty = 0;

        // Caches to prevent duplicate queries
        $tariffCacheById = [];
        $tariffCacheByCode = [];

        foreach ($invoiceItems as $item) {
            $itemCount = $item->total_count;
            $cargoNo = $item->cargo_no;
            $totalQty += $item->totalQty;
            $totalPrice += $item->totalValue;

            // Determine tariff code and name based on itemTaricId
            if ($item->itemTaricId === 48) {
                if (isset($tariffCacheById[$item->srqTaricID])) {
                    $tariff = $tariffCacheById[$item->srqTaricID];
                } else {
                    $tariff = Taric::find($item->srqTaricID, ['name_en', 'code']);
                    $tariffCacheById[$item->srqTaricID] = $tariff;
                }

                if ($tariff) {
                    $taricCode = $tariff->code;
                    $taricNameEN = $tariff->name_en;
                    $taric_name_cn = $tariff->name_cn;
                } else {
                    $taricCode = 'n/a';
                    $taricNameEN = 'Unknown Tariff';
                }
            } else {
                if (! empty($item->set_taric_code) && $item->set_taric_code !== $item->code) {
                    if (isset($tariffCacheByCode[$item->set_taric_code])) {
                        $tariff = $tariffCacheByCode[$item->set_taric_code];
                    } else {
                        $tariff = Taric::where('code', $item->set_taric_code)
                            ->select('name_en')
                            ->first();
                        $tariffCacheByCode[$item->set_taric_code] = $tariff;
                    }
                    $taricCode = $item->set_taric_code;
                    $taricNameEN = $tariff ? $tariff->name_en : 'Unknown Tariff';
                } else {
                    $taricCode = $item->code;
                    $taricNameEN = $item->name_en;
                    $taric_name_cn = $item->name_cn;
                }
            }

            // Create a ClosedInvoice record for the current item
            Cci_invoice::create([
                'invSerialNo' => $this->invSerialNo,
                'cci_customer_id' => $this->new_cust_inv_id,
                'cargo_id' => $cargoId,
                'cargo_no' => $cargoNo,
                'cargo_type' => $item->cargo_type,
                'taric_code' => $taricCode,
                'taric_nameEN' => $taricNameEN,
                'taric_name_cn' =>$taric_name_cn,
                'item_count' => $itemCount,
                'total_qty' => $item->totalQty,
                'total_price' => $item->totalValue,
            ]);
        }

        // Create a final record for the freight cost
        Cci_invoice::create([
            'invSerialNo' => $this->invSerialNo,
            'cci_customer_id' => $this->new_cust_inv_id,
            'cargo_id' => $cargoId,
            'taric_code' => 'n/a',
            'taric_nameEN' => 'Freight cost',
        ]);

        session()->flash('success', 'Closed invoice created successfully!');
    }
    // 4. setting status to invoice after successfull creation of invoices

    public function statusInvoiced($id)
    {
        Order_status::where('cargo_id', $id)
            ->update(
                [
                    'status' => 'Invoiced',
                ]);
    }
}
