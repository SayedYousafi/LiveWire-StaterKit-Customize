<?php
namespace App\Livewire;

use App\Models\PackingList;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class PackingLists extends Component
{
    public $btnEnable =false;
    public $packingId;
    public array $packingLists = [];

    public function mount()
    {
        DB::statement('SET SESSION sql_mode = ""');

        $lists = DB::table('cci_invoices')
            ->join('cci_customers', 'cci_invoices.cci_customer_id', '=', 'cci_customers.id')
            ->where('cci_invoices.cargo_id', $this->packingId)
            ->where('cci_invoices.taric_code', '!=', 'n/a')
            ->select(
                'cci_customers.customer_id',
                'cci_invoices.taric_nameEN',
                DB::raw('SUM(cci_invoices.total_qty) AS total_qty')
            )
            ->groupBy('cci_invoices.taric_code', 'cci_customers.customer_id', 'cci_invoices.taric_nameEN')
            ->orderBy('cci_invoices.taric_nameEN', 'ASC')
            ->get();

        $this->packingLists = $lists->map(function ($list) {
            return [
                'customer_id'     => $list->customer_id,
                'itemDescription' => $list->taric_nameEN,
                'itemQty'         => $list->total_qty,
                'client1'         => null,
                'pallet'          => null,
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
        $packs = PackingList::select([DB::raw('count(item_qty) AS CountList'), 'customer_id', 'created_at'])
        ->groupBy('created_at')
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
        $copy['weight'] = null;
        $copy['length'] = null;
        $copy['width'] = null;
        $copy['height'] = null;

        array_splice($this->packingLists, $index + 1, 0, [$copy]);
    }

    public function save()
    {
        foreach ($this->packingLists as $entry) {
            PackingList::create([
                'customer_id'      => $entry['customer_id'] ?? null,
                'item_description' => $entry['itemDescription'] ?? null,
                'item_qty'         => $entry['itemQty'] ?? 0,
                'client1'          => $entry['client1'] ?? null,
              
                'pallet'           => $entry['pallet'] ?? null,
                'weight'           => $entry['weight'] ?? 0,
                'length'           => $entry['length'] ?? 0,
                'width'            => $entry['width'] ?? 0,
                'height'           => $entry['height'] ?? 0,
            ]);
        }

        session()->flash('success', 'Packing list saved successfully!');
        $this->reset();
    }
}