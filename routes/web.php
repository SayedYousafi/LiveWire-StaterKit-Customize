<?php

use App\Livewire\Nso;
use App\Livewire\Admin;
use App\Livewire\Items;
use App\Livewire\Teams;
use App\Livewire\Cargos;
use App\Livewire\Charts;
use App\Livewire\Orders;
use App\Livewire\Tarics;
use App\Livewire\AddItem;
use App\Livewire\Parents;
use App\Livewire\Controls;
use App\Livewire\Invoices;
use App\Livewire\Problems;
use App\Livewire\Customers;
use App\Livewire\ItemEdits;
use App\Livewire\Suppliers;
use App\Livewire\Warehouse;
use App\Livewire\Auth\Login;
use App\Livewire\CargoTypes;
use App\Livewire\Categories;
use App\Livewire\OrderItems;
use App\Livewire\ReportList;
use App\Livewire\ItemDetails;
use App\Livewire\SupplierOrder;
use App\Livewire\ClosedInvoices;
use App\Livewire\ImportWarehouse;
use App\Livewire\ItemDescription;
use App\Livewire\Settings\Profile;
use App\Livewire\Settings\Password;
use App\Livewire\Settings\Appearance;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EtlController;
use App\Http\Controllers\ExportFullList;
use App\Http\Controllers\PrintController;
//use App\Models\User;
use Livewire\Volt\Volt;

// Route::get('/', function () { return view('welcome'); })->name('home');

Volt::route('/users', 'users');

//directly calling the login page.
Route::get('/', Login::class)->name('home');
Volt::route('/users', 'users');
Route::get('categories', Categories::class )->name('categories');
Route::get('items/{param?}', Items::class )->name('items');
Route::get('itemAdd', AddItem::class)->name('itemAdd');
Route::get('itemDetail/{itemId}', ItemDetails::class )->name('itemDetail');
Route::get('itemEdit/{id}', ItemEdits::class )->name('itemEdit');
Route::get('suppliers', Suppliers::class )->name('suppliers');
Route::get('tarics', Tarics::class)->name('tarics');
Route::get('parents', Parents::class)->name('parents');
Route::get('teams', Teams::class)->name('teams');
Route::get('orders', Orders::class)->name('orders');
Route::get('orderItems/{param?}/{status?}', OrderItems::class)->name('orderItems');
Route::get('nso', Nso::class)->name('nso');
Route::get('so', SupplierOrder::class)->name('so');
Route::get('cargos', Cargos::class)->name('cargos');
Route::get('cargotypes',CargoTypes::class)->name('cargotypes');
Route::get('invoices', Invoices::class)->name('invoices');
Route::get('invoicesClosed', ClosedInvoices::class)->name('invoicesClosed');
Route::get('controls', Controls::class)->name('controls');
Route::get('admin', Admin::class)->name('admin');
Route::get('problems', Problems::class)->name('problems');
Route::get('customers', Customers::class)->name('customers');
Route::get('shortdesc/{parent_id?}', ItemDescription::class)->name('shortdesc');

Route::get('print/{id}', [PrintController::class, 'print'])->name('print');
Route::get('invoice/{id}/{sn}', [PrintController::class, 'newInvoice'])->name('invoice');

Route::view('dashboard', 'dashboard')->middleware(['auth', 'verified'])->name('dashboard');

Route::get('/export', [ExportFullList::class, 'exportCSV']);
Route::get('/export/{isNew}', [ExportFullList::class, 'exportCSV']);
Route::get('/export/{updated}', [ExportFullList::class, 'exportCSV']);
Route::get('confirmed', [ExportFullList::class, 'confirmed'])->name('confirmed');

//Warehouse
Route::get('warehouse',Warehouse::class);
Route::get('downloadStockQty',ReportList::class);
// Chart Route;
Route::get('WarehouseValue',Charts::class);
Route::get('import', ImportWarehouse::class);

//ETL Processes
Route::get('etl', [EtlController::class, 'findTargetDate']); // ->middleware('auth');
Route::get('updateRecords', [EtlController::class, 'statusRemark']); // ->middleware('auth');
Route::get('synchIDs', [EtlController::class, 'MisIds']); // ->middleware('auth');
Route::get('importWareHouse', [EtlController::class, 'whareHouseSync']);

Route::middleware(['auth'])->group(function () {
    Route::redirect('settings', 'settings/profile');

    Route::get('settings/profile', Profile::class)->name('settings.profile');
    Route::get('settings/password', Password::class)->name('settings.password');
    Route::get('settings/appearance', Appearance::class)->name('settings.appearance');
});

require __DIR__.'/auth.php';