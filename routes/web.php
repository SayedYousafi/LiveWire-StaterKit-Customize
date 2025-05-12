<?php

use App\Livewire\Settings\Appearance;
use App\Livewire\Settings\Password;
use App\Livewire\Settings\Profile;
use App\Livewire\Auth\Login;
use App\Livewire\Categories;
use App\Livewire\Items;
use App\Livewire\Post;
use App\Livewire\Suppliers;
use Illuminate\Support\Facades\Route;

// Route::get('/', function () {
//     return view('welcome');
// })->name('home');

//directly calling the login page.
Route::get('/', Login::class)->name('home');
Route::get('categories', Categories::class )->name('categories');
Route::get('items', Items::class )->name('items');
Route::get('suppliers', Suppliers::class )->name('suppliers');


Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::view('orders', 'orders')
    ->middleware(['auth', 'verified'])
    ->name('orders');

Route::middleware(['auth'])->group(function () {
    Route::redirect('settings', 'settings/profile');

    Route::get('settings/profile', Profile::class)->name('settings.profile');
    Route::get('settings/password', Password::class)->name('settings.password');
    Route::get('settings/appearance', Appearance::class)->name('settings.appearance');
});

require __DIR__.'/auth.php';
