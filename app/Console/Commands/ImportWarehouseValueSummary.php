<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Warehouse;
use App\Models\Stockvalue;

class ImportWarehouseValueSummary extends Command
{
    protected $signature = 'import:warehouse-summary';
    protected $description = 'Import warehouse category summary into the stockvalue table';

    public function handle()
    {
        $wItems = Warehouse::join('supplier_items', 'warehouse_items.item_id', '=', 'supplier_items.item_id')
            ->join('categories', 'warehouse_items.category_id', '=', 'categories.id')
            ->selectRaw('
                warehouse_items.category_id,
                categories.name AS category_name,
                COUNT(warehouse_items.item_id) AS Count,
                SUM(supplier_items.price_rmb * warehouse_items.stock_qty) AS RMB_Value,
                SUM(COALESCE(EK_net(supplier_items.price_rmb, warehouse_items.category_id) * warehouse_items.stock_qty , 0)) AS EUR_Value
            ')
            ->where('categories.is_ignored_value', '!=', 'Y')
            ->groupBy('warehouse_items.category_id', 'categories.name')
            ->orderBy('warehouse_items.category_id')
            ->get()
            ->toArray();

        $now = now();
        $insertData = [];

        foreach ($wItems as $data) {
            $insertData[] = [
                'date'       => $now,
                'category'   => $data['category_name'],
                'rmb'        => round($data['RMB_Value'], 2),
                'eur'        => round($data['EUR_Value'], 2),
                'count'      => $data['Count'],
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        if (!empty($insertData)) {
            Stockvalue::insert($insertData);
            \Log::info("Warehouse value summary imported successfully.");
        } else {
            \Log::info("No data available to import.");
        }
    }
}
