<?php

namespace App\Livewire;

use App\Models\Item;
use App\Models\Order;
use App\Models\Supplier;
use Livewire\Component;

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
