<?php
namespace App\Livewire;

use App\Models\Customer;
use App\Models\Item;
use App\Models\LeaveRequest;
use App\Models\Order;
use App\Models\Supplier;
use Livewire\Component;

class DashboardStats extends Component
{
    public $ordersCount;

    public $itemsCount;

    public $suppliersCount;

    public $customersCount;

    public function mount()
    {
        $this->ordersCount    = Order::count();
        $this->itemsCount     = Item::count();
        $this->suppliersCount = Supplier::count();
        $this->customersCount = Customer::count();
    }

    public function render()
    {
        $today = now()->toDateString();

        $usersOnLeaveToday = LeaveRequest::whereDate('dateFrom', '<=', $today)
            ->whereDate('dateTo', '>=', $today)
            ->where('status', 'approved')
            ->with('users') // eager load user
            ->get();
        $leaves = $usersOnLeaveToday;
        //dd($leaves);
        return view('livewire.dashboard-stats', compact('leaves'));
    }
}
