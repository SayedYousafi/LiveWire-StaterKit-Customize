<?php

namespace App\Livewire;

use App\Models\Cargo;
use App\Models\Category;
use App\Models\Order;
use App\Models\Order_item;
use App\Models\Order_status;
use Flux\Flux;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Title('Orders')]
class Orders extends Component
{
    use WithPagination;

    public $catName;

    public $cargoId;

    public $orderNo;

    public $masterIds;

    public string $search = '';

    public string $title = 'Orders';

    public function render()
    {
        $statusesToCount = [
            'NSO', 'P_Problem', 'C_Problem', 'Checked', 'SO',
            'Printed', 'Purchased', 'Shipped', 'Paid', 'Invoiced',
        ];

        $statusCounts = Order_status::whereIn('status', $statusesToCount)
            ->select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->get()
            ->pluck('count', 'status')
            ->toArray();

        $counts = [
            'totalItemOrders' => Order_item::count(),
            'openOrders' => $statusCounts['NSO'] ?? 0,
            'problemOrders' => ($statusCounts['P_Problem'] ?? 0) + ($statusCounts['C_Problem'] ?? 0),
            'checkOrders' => $statusCounts['Checked'] ?? 0,
            'orderOrders' => $statusCounts['SO'] ?? 0,
            'printOrders' => $statusCounts['Printed'] ?? 0,
            'purchaseOrders' => $statusCounts['Purchased'] ?? 0,
            'shippedOrders' => $statusCounts['Shipped'] ?? 0,
            'paidOrders' => $statusCounts['Paid'] ?? 0,
            'invoicedOrders' => $statusCounts['Invoiced'] ?? 0,
        ];

        $query = Order::query()
            ->search($this->search) // now chaining works correctly
            ->with(['orderItems.status', 'categories'])
            ->withCount('orderItems')
            ->orderBy('order_no', 'desc');

        if ($this->catName) {
            $query->where('category_id', $this->catName);
        }

        $orders = $query->paginate(100);

        return view('livewire.orders', array_merge($counts, [
            'orders' => $orders,
            'title' => $this->title,
            'categories' => Category::pluck('name', 'de_cat'),
            'cargos' => Cargo::where('cargo_status', 'Open')->pluck('cargo_no', 'id'),
        ]));
    }

    public function selectCargo($oNo)
    {
        // dd($oNo);
        $this->orderNo = $oNo;
        $this->masterIds = Order_item::where('order_no', "$this->orderNo")->pluck('master_id')->toArray();
        // dd($this->masterIds);
        // $this->cargoId = $this->masterIds['0']->first();
        Flux::modal('myModal')->show();
    }

    public function changeCargo()
    {
        date_default_timezone_set('Europe/Berlin');

        Order_status::whereIn('master_id', $this->masterIds)
            ->update([
                'cargo_id' => $this->cargoId,
                'cargo_date' => now(),
            ]);

        session()->flash('success', 'Cargo assigned successfully.');
        Flux::modal('myModal')->close();
    }
}
