<?php

namespace App\Livewire;

use App\Models\Stockvalue;
use App\Models\Warehouse as ModelsWareHouse;
use Livewire\Component;
use Livewire\WithPagination;

class Warehouse extends Component
{
    use WithPagination;

   

    public $table = false;

    public $chart = false;

    public $wItems = [];

    public $stocks = [];

    public $date;

    public $category;

    public $rmb;

    public $eur;

    public $count;

    public function render()
    {
        $this->wItems = ModelsWareHouse::join('supplier_items', 'warehouse_items.item_id', '=', 'supplier_items.item_id')
            ->join('categories', 'warehouse_items.category_id', '=', 'categories.id') // Join with categories
            ->selectRaw('
            warehouse_items.category_id,
            categories.name AS category_name,
            COUNT(warehouse_items.item_id) AS Count,
            SUM(supplier_items.price_rmb * warehouse_items.stock_qty) AS RMB_Value,
           
            SUM(COALESCE(EK_net(supplier_items.price_rmb, warehouse_items.category_id), 0) * warehouse_items.stock_qty)

        ')
            ->where('categories.is_ignored_value', '!=', 'Y')               // Filter out ignored categories
            ->groupBy('warehouse_items.category_id', 'categories.name') // Group by category_id and category_name
            ->orderBy('warehouse_items.category_id')
            ->get()
            ->toArray();

        $this->stocks = Stockvalue::selectRaw("
            DATE_FORMAT(created_at, '%Y-%m') as month, 
            category, 
            SUM(eur) as total_eur
        ")
            ->groupBy('month', 'category')
            ->orderBy('month')
            ->orderByRaw('SUM(eur) DESC')
            ->get()
            ->toArray();
        // dd($this->stocks);

        $wareHouses = ModelsWareHouse::with('supplierItem', 'category')->paginate(15);

        return view('livewire.warehouse')->with(
            [
                'wareHouses' => $wareHouses,
                'wItems' => $this->wItems,
                'stocks' => $this->stocks,
            ]
        );
    }

    public function displayTable()
    {
        $this->chart = false;
        $this->table = true;

        $this->wItems;

    }

    public function displayChart()
    {
        $this->wItems;
        $this->table = false;
        $this->chart = true;
        // $this->dispatch('renderChart');
    }
}
