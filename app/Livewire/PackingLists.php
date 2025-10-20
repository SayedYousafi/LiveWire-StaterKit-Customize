<?php
namespace App\Livewire;

use App\Models\PackingList;
use App\Models\Cargo;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class PackingLists extends Component
{
    public $packToEdit;
    public $packingId;
    public array $packingLists = [];
    public $title ='PackingList';

    protected $listeners = ['listUpdated' => '$refresh'];

    public function mount()
    {
        DB::statement('SET SESSION sql_mode = ""');

        $lists = DB::table('cci_invoices')
            ->join('cci_customers', 'cci_invoices.cci_customer_id', '=', 'cci_customers.id')
            ->where('cci_invoices.cargo_id', $this->packingId)
            ->where('cci_invoices.taric_code', '!=', 'n/a')
            ->select(
                'cci_invoices.id',
                'cci_invoices.cargo_id',
                'cci_invoices.cargo_no',
                'cci_customers.customer_id',
                'cci_invoices.taric_nameEN',
                'cci_invoices.taric_name_cn',
                DB::raw('SUM(cci_invoices.total_qty) AS total_qty')
            )
            ->groupBy('cci_invoices.taric_code', 'cci_customers.customer_id', 'cci_invoices.taric_nameEN')
            ->orderBy('cci_invoices.taric_nameEN', 'ASC')
            ->get();

        // Get the first ID to generate invoice_no
        $firstInvoiceNo = optional($lists->first())->id ? 'CI2500' . $lists->first()->id : null;

        $this->packingLists = $lists->map(function ($list) use ($firstInvoiceNo) {
            return [
                'customer_id'     => $list->customer_id,
                'cargo_id'        => $list->cargo_id,
                'invoice_no'      => $firstInvoiceNo,
                'cargo_no'        => $list->cargo_no,
                'itemDescription' => $list->taric_nameEN,
                'cn_description'  => $list->taric_name_cn,
                'itemQty'         => $list->total_qty,
                'client1'         => $list->client1 ?? 'GTECH-GT', // 👈 DEFAULT 
                'pallet'          => $list->pallet ?? 'P1', // 👈 DEFAULT
                'ptype'           => $list->ptype ?? 'Tray', // 👈 DEFAULT
                'weight'          => null,
                'length'          => null,
                'width'           => null,
                'height'          => null,
            ];
        })->toArray();
    }

    public function render()
    {
        DB::statement('SET SESSION sql_mode = ""');
        $packs = PackingList::select(['cargo_id','cargo_no', 'invoice_no', DB::raw('count(item_qty) AS CountList'), 'customer_id', 'created_at'])
            ->groupBy('cargo_id')
            ->orderBy('id', 'desc')
            ->get();

        return view('livewire.packing-list')->with(
            [
                'packs' => $packs,
            ]);
    }

    public function duplicateRow($index)
    {
        $original = $this->packingLists[$index];

        $copy = $original;
        // Optional: reset some fields
        $copy['itemQty'] = null;
        $copy['weight']  = null;
        $copy['length']  = null;
        $copy['width']   = null;
        $copy['height']  = null;

        array_splice($this->packingLists, $index + 1, 0, [$copy]);
    }

    public function save()
    {
        // Step 1: Basic field validation
        $this->validate([
            'packingLists.*.itemDescription' => 'required|string',
            //'packingLists.*.itemQty'         => 'required|numeric|min:1',
            'packingLists.*.client1'         => 'nullable|string',
            'packingLists.*.pallet'          => 'nullable|string|regex:/^P\d+$/i',
            'packingLists.*.weight'          => 'nullable|numeric|min:0',
            'packingLists.*.length'          => 'nullable|numeric|min:0',
            'packingLists.*.width'           => 'nullable|numeric|min:0',
            'packingLists.*.height'          => 'nullable|numeric|min:0',
        ]);

        // Step 2: Pallet sequence validation
        $pallets = collect($this->packingLists)
            ->pluck('pallet')
            ->filter()
            ->unique()
            ->values();

        $palletNumbers = $pallets->map(function ($pallet) {
            return (int) str_replace('P', '', strtoupper($pallet));
        })->sort()->values();

        foreach ($palletNumbers as $index => $palletNumber) {
            if ($palletNumber !== $index + 1) {
                $missing = 'P' . ($index + 1);
                $this->addError('pallet_sequence', "Pallet $missing is missing. Pallet sequence must be continuous.");
                return;
            }
        }
        foreach ($this->packingLists as $entry) {
            $saved = PackingList::create([
                'customer_id'      => $entry['customer_id'] ?? null,
                'cargo_id'         => $entry['cargo_id'] ?? null,
                'invoice_no'       => $entry['invoice_no'] ?? null,
                'cargo_no'         => $entry['cargo_no'] ?? null,
                'item_description' => $entry['itemDescription'] ?? null,
                'cn_description'   => $entry['cn_description'] ?? null,
                'item_qty'         => $entry['itemQty'] ?? 0,
                'client1'          => $entry['client1'] ?? null,
                'pallet'           => $entry['pallet'] ?? null,
                'ptype'            => $entry['ptype'] ?? null,
                'weight'           => $entry['weight'] ?? 0,
                'length'           => $entry['length'] ?? 0,
                'width'            => $entry['width'] ?? 0,
                'height'           => $entry['height'] ?? 0,
            ]);
        }
        if ($saved) {
            session()->flash('success', 'Packing list saved successfully!');
            //call cargo table to update the status of cargo from shipped to Packed.
            $this->setCargoStatusPacked();
            $this->reset();
        }

    }
    public function setCargoStatusPacked()
    {
        //dd($id);
        Cargo::where('id', $this->packingId)->update(['cargo_status' => 'Packed']);
        return redirect('packingList');
    }
    public function getPack($id)
    {
        $this->packToEdit = $id;
    }
}
