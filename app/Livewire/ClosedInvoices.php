<?php
namespace App\Livewire;

use App\Models\Cargo;
use App\Models\Cci_invoice;
use App\Models\Holiday;
use App\Models\Order_status;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

// use DB;

class ClosedInvoices extends Component
{
    public string $title = 'invoicesClosed';

    public $cargo_id;

    public $ciNo;

    public $cust_id;

    public $taricNo;

    public $sn;

    public $invNo;

    public $invSerialNo;

    public $myDate;

    public $description = 'Freight Cost';

    public $total_price;

    public $remark;

    public $myId;

    public function render()
    {
        DB::statement('SET SESSION sql_mode = ""'); // Disable strict mode for this session

        $results = DB::table('cci_customers')
            ->join('cci_invoices', 'cci_customers.id', '=', 'cci_invoices.cci_customer_id')
            ->join('cargos', 'cci_invoices.cargo_id', '=', 'cargos.id')
        // ->join('order_statuses', 'order_statuses.cargo_id', '=', '')
            ->select(
                'cci_customers.id',
                'cci_customers.customer_id',
                'cci_invoices.cargo_id',
                'cci_invoices.id as myId',
                'cci_invoices.created_at AS InvoiceDate',
                'cargos.cargo_no',
                'cargos.cargo_status',
                'cargos.shipped_at',
                'cci_customers.customer_company_name',
                DB::raw('COUNT(cci_invoices.taric_code) AS taric_code'),
                DB::raw('MAX(cci_invoices.invSerialNo) AS invSerialNo'),
                DB::raw('SUM(cci_invoices.total_qty) AS total_qty'),
                DB::raw('MAX(cci_customers.created_at) AS created_at'),
                DB::raw('SUM(cci_invoices.total_price) AS total_price'),
                DB::raw('SUM(cci_invoices.item_count) AS item_count'),
            )
            ->whereColumn('cci_invoices.invSerialNo', 'cci_customers.invSerialNo')
            ->groupBy('cci_customers.id')
            ->orderBy('cci_customers.id', 'DESC')->get();

        $data = $results;
        //dd($data);

        $items   = $this->showCiItem($this->ciNo, $this->sn);
        $results = $this->showItems($this->taricNo, $this->sn);

        return view('livewire.closed-invoices',
            compact('data', 'items', 'results')
        );
    }

    public function showCiItem($id, $sn)
    {
        DB::statement('SET SESSION sql_mode = ""');
        $this->ciNo = $id;
        $this->sn   = $sn;
        $taricItems = DB::table('cci_invoices')
            ->join('cci_customers', 'cci_customers.id', '=', 'cci_invoices.cci_customer_id')
            ->where('cci_invoices.invSerialNo', $sn)
            ->select('cci_invoices.cci_customer_id',
                'cci_invoices.taric_code AS code',
                'cci_invoices.taric_nameEN AS TarifName',
                DB::raw('SUM(cci_invoices.total_qty) AS total_qty'),
                DB::raw('SUM(cci_invoices.total_price) AS total_price')
            )
            ->groupBy('cci_invoices.cci_customer_id', 'cci_invoices.taric_code',

            )
            ->orderBy('cci_invoices.taric_nameEN')
            ->get();

        // dd($taricItems);
        return $taricItems;
    }

    public function showItems($id, $sn)
    {
        // dd($id);
        $this->taricNo = $id;
        $this->sn      = $sn;
        $result        = DB::table('cci_customers')
            ->join('cci_items', 'cci_items.cci_customer_id', '=', 'cci_customers.id')
            ->where('cci_items.invSerialNo', $sn)
            ->select('cci_customers.customer_id',
                'cci_items.ean', 'cci_items.id', 'cci_items.item_name', 'cci_items.tariff_code',
                'cci_items.qty', 'cci_items.rmb', 'cci_items.eur',
            )
            ->groupBy('cci_items.cci_customer_id', 'cci_customers.customer_id',
                'cci_items.ean', 'cci_items.id', 'cci_items.item_name', 'cci_items.tariff_code',
                'cci_items.qty', 'cci_items.rmb', 'cci_items.eur', )
            ->orderBy('cci_items.item_name')
            ->get();

        // dd($result);
        return $result;

    }

    public function shipCI($id)
    {
        $itemsToShip = DB::table('order_statuses')
            ->where('order_statuses.status', 'Invoiced')
            ->where('order_statuses.cargo_id', $id)
            ->get();
        // dd($itemsToShip);

        if (! empty($itemsToShip)) {
            foreach ($itemsToShip as $item) {
                Order_status::where('id', $item->id)
                    ->update(
                        [
                            'status' => 'Shipped',
                        ]);
            }
        } else {
            dd('no item to ship');
        }

        $cargo    = Cargo::with('cargoType')->findOrFail($id);
        $duration = $cargo->cargoType->duration;
        $eta      = Carbon::now()->addDays($duration);

        // Get public holidays for Germany as an array of date strings (Y-m-d)
        $publicHolidays = Holiday::where('country', 'Germany')->pluck('date')->map(function ($date) {
            return Carbon::parse($date)->toDateString();
        })->toArray();

        // Adjust $eta forward if it's a weekend or a public holiday
        while ($eta->isWeekend() || in_array($eta->toDateString(), $publicHolidays)) {
            if ($eta->isSaturday()) {
                $eta->addDays(2);
            } elseif ($eta->isSunday() || in_array($eta->toDateString(), $publicHolidays)) {
                $eta->addDay();
            }
        }

        // set the status of cargos to shipped, ETA as duration + shipped_at.
        Cargo::where('id', $id)
            ->update(
                [
                    'cargo_status' => 'Shipped',
                    'shipped_at'   => now(),
                    'eta'          => $eta,
                ]);
        session()->flash('success', 'Items shipped successfully !!!');
    }

    public function checkPrice($id, $sn)
    {
        $this->invNo = $id;
        $this->sn    = $sn;
        // dd($this->invNo, $sn);
        $results = DB::table('cci_customers')
            ->join('cci_invoices', 'cci_invoices.cci_customer_id', '=', 'cci_customers.id')
            ->where('cci_customers.id', $this->invNo)
            ->where('cci_invoices.invSerialNo', $this->sn)
            ->get();
        // dd($results);
        $this->myId = $results->last()->id;
        // dd(($results->last()->id));
        if ($results->last()->total_price === null && $results->last()->id === $this->myId) {
            session()->flash('error', 'Freight cost is zero, fill the required field below!');

        } else {
            //return redirect('/invoice/'.$this->invNo.'/'.$this->sn);
            return '/invoice/' . $this->invNo . '/' . $this->sn;
        }
    }

    public function getData($id, $sn)
    {
        $this->invNo = $id;
        $this->sn    = $sn;
        // dd($this->invNo,$this->sn=$sn );
        $results = DB::table('cci_customers')
            ->join('cci_invoices', 'cci_invoices.cci_customer_id', '=', 'cci_customers.id')
            ->where('cci_customers.id', $this->invNo)
            ->where('cci_invoices.invSerialNo', $this->sn)
            ->get();
        // dd($results);
        $this->myId        = $results->last()->id;
        $this->description = $results->last()->taric_nameEN;
        $this->total_price = $results->last()->total_price;
        $this->remark      = $results->last()->remark;

        return $results;
    }

    public function editData()
    {
        $this->validate([
            'description' => 'required|string',
            'total_price' => 'required|numeric',
            'remark'      => 'required',
        ]);
        Cci_invoice::where('id', $this->myId)
        // ->whereColumn('cargo_date',$tci_created_at)
            ->update([
                // 'invSerialNo'=>$this->invSerialNo,
                'cci_customer_id' => $this->invNo,
                'taric_nameEN'    => $this->description,
                'total_price'     => $this->total_price,
                'created_at'      => $this->myDate,
                'taric_code'      => 'n/a',
                'item_count'      => 0,
                'total_qty'       => 0,
                'remark'          => $this->remark,
            ]);
        session()->flash('success', 'Extra data saved successfully in this invoice !!!');
        $this->cancel();
    }

    // protected $rules =
    public function cancel()
    {
        $this->ciNo        = null;
        $this->taricNo     = null;
        $this->invNo       = null;
        $this->description = '';
        $this->total_price = '';
        $this->remark      = '';
    }
}
