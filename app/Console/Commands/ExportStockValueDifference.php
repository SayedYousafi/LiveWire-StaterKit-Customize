<?php

namespace App\Console\Commands;

use App\Models\Stockvalue;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

class ExportStockValueDifference extends Command
{
    protected $signature = 'export:stock-difference';

    protected $description = 'Export monthly stock value difference to CSV';

    public function handle()
    {
        $records = Stockvalue::selectRaw("
            DATE_FORMAT(created_at, '%Y-%m-%d') AS month,
            SUM(EUR) - LAG(SUM(EUR)) OVER (ORDER BY DATE_FORMAT(created_at, '%Y-%m-%d')) AS difference")
            ->where('created_at', '>=', Carbon::now()->subMonths(13))
            ->groupBy('month')
            ->orderBy('month', 'desc')
            ->limit(1)
            ->get();

        $filePath = storage_path('app/exports/WarehouseValueDifference_'.now()->format('Y-m-d').'.csv');
        $file = fopen($filePath, 'w');
        fputcsv($file, ['Month', 'Difference']);

        foreach ($records as $record) {
            fputcsv($file, [$record->month, $record->difference]);
        }

        fclose($file);
    }
}
