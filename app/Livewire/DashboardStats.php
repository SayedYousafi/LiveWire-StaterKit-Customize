<?php
namespace App\Livewire;

use App\Models\Item;
use App\Models\Order;
use App\Models\Holiday;
use Livewire\Component;
use App\Models\Customer;
use App\Models\Supplier;
use App\Models\LeaveRequest;
use Livewire\WithPagination;

class DashboardStats extends Component
{
    use WithPagination;
    public $ordersCount;

    public $itemsCount;

    public $suppliersCount;

    public $customersCount;

    

    public bool $enableEdit = false;

    public bool $isUpdate = false;

    public $holidayId;

    public string $search = '';

    public string $filterByCountry = '';

    public string $title = 'Public Holidays';

    

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

        $holidays = Holiday::search($this->search);
        if ($this->filterByCountry) {
            $holidays->where('country', $this->filterByCountry);
        }
        $holidays = $holidays->paginate(100);

        return view('livewire.dashboard-stats', compact('leaves', 'holidays'));
    }
}
