<?php


use App\Console\Commands\DbBackup;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use App\Console\Commands\ExportWarehouseItemValue;
use App\Console\Commands\ExportStockValueDifference;
use App\Console\Commands\ImportWarehouseValueSummary;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command(DbBackup::class)->daily()
         ->appendOutputTo(storage_path('logs/db_backup.log'));

Schedule::command(ExportStockValueDifference::class)->monthly();
Schedule::command(ExportWarehouseItemValue::class)->monthly();
Schedule::command(ImportWarehouseValueSummary::class)->monthly();

