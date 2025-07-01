<?php

namespace App\Livewire;

use App\Models\Stockvalue;
use App\Models\Warehouse;
use Illuminate\Support\Facades\File;
use Livewire\Component;

class ImportWarehouse extends Component
{
    // use exportable;
    public $wItems;

    public $valueDate;

    public function render()
    {
        // get the lsat date of warehouse synch from wawi.
        // $filePath = '/var/www/html/public/wareHouseItems.csv';
        $filePath = public_path('wareHouseItems.csv');

        if (File::exists($filePath)) {
            $this->valueDate = date('Y-m-d H:i:s', File::lastModified($filePath)); // Get last modified time
            // dd($valueDate);
        } else {
            dd('no file found');
        }

        $this->wItems = Warehouse::join('supplier_items', 'warehouse_items.item_id', '=', 'supplier_items.item_id')
            ->join('categories', 'warehouse_items.category_id', '=', 'categories.id') // Join with categories
            ->selectRaw('
            warehouse_items.category_id,
            categories.cat_name AS category_name,
            COUNT(warehouse_items.item_id) AS Count,
            SUM(supplier_items.price_rmb * warehouse_items.stock_qty) AS RMB_Value,
            SUM(COALESCE(EK_net(supplier_items.price_rmb, warehouse_items.category_id) * warehouse_items.stock_qty , 0)) AS EUR_Value
        ')
            ->where('categories.is_ignored_value', '!=', 'Y')               // Filter out ignored categories
            ->groupBy('warehouse_items.category_id', 'categories.cat_name') // Group by category_id and category_name
            ->orderBy('warehouse_items.category_id')
            ->get()
            ->toArray();

        return view('livewire.import-warehouse')->with(
            [

                'wItems' => $this->wItems,
                'date' => $this->valueDate,
            ]
        );
    }

    public function importData()
    {
        // dd('import is blocked temporary');
        if (empty($this->wItems)) {
            session()->flash('error', 'No data to import.');

            return;
        }

        $now = now();
        $insertData = [];

        foreach ($this->wItems as $data) {
            $insertData[] = [
                'date' => $now,
                'category' => $data['category_name'],
                'rmb' => round($data['RMB_Value'], 2), // Ensure two decimal places
                'eur' => round($data['EUR_Value'], 2),
                'count' => $data['Count'],
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        if (! empty($insertData)) {
            Stockvalue::insert($insertData); // Batch insert for better performance
            session()->flash('success', 'Stock value for this month created successfully!');
        } else {
            session()->flash('error', 'No valid data to import.');
        }
    }
}
