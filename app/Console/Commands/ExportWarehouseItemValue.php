<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Warehouse;

class ExportWarehouseItemValue extends Command
{
    protected $signature = 'export:warehouse-items';
    protected $description = 'Export detailed warehouse item values to CSV';

    public function handle()
    {
        $wareHouse = WareHouse::with('supplierItem', 'category')->get();
        $filePath = storage_path('app/exports/WarehouseItemValue_' . now()->format('Y-m-d') . '.csv');
        $file = fopen($filePath, 'w');

        fputcsv($file, ["ID", "ItemID", "ID_DE", "EAN", "Category", "Item Name", "Qty", "RMB", "Total RMB", "EUR", "Total EUR"]);

        foreach ($wareHouse as $record) {
            $categoryName = optional($record->category)->name;
            $priceRMB = optional($record->supplierItem)->price_rmb ?? 0;
            $eur = EK_net($priceRMB, $record->category_id);
            $totalRMB = $record->stock_qty * $priceRMB;
            $totalEUR = $record->stock_qty * $eur;

            fputcsv($file, [
                $record->id,
                $record->item_id,
                $record->ItemID_DE,
                $record->ean,
                $categoryName,
                $record->item_name_en,
                $record->stock_qty,
                $priceRMB,
                $totalRMB,
                $eur,
                $totalEUR,
            ]);
        }

        fclose($file);
    }
}
