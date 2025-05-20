<?php

namespace App\Livewire;

use App\Models\Item;
use App\Models\Order;
use Livewire\Component;
use App\Models\Supplier;

class DashboardStats extends Component
{
    public $ordersCount;
    public $itemsCount;
    public $suppliersCount;

    public function mount()
    {
        $this->ordersCount = Order::count();
        $this->itemsCount = Item::count();
        $this->suppliersCount = Supplier::count();
    }
    public function render()
    {
        return view('livewire.dashboard-stats');
    }
}
