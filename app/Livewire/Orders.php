<?php
namespace App\Livewire;

use App\Models\Category;
use App\Models\Order;
use App\Models\Order_item;
use App\Models\Order_status;
use DB;
use Livewire\Component;
use Livewire\WithPagination;

class Orders extends Component
{
    use WithPagination;

    public $catName;
    public string $search = '';
    public string $title  = 'Orders';

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
            'openOrders'      => $statusCounts['NSO'] ?? 0,
            'problemOrders'   => ($statusCounts['P_Problem'] ?? 0) + ($statusCounts['C_Problem'] ?? 0),
            'checkOrders'     => $statusCounts['Checked'] ?? 0,
            'orderOrders'     => $statusCounts['SO'] ?? 0,
            'printOrders'     => $statusCounts['Printed'] ?? 0,
            'purchaseOrders'  => $statusCounts['Purchased'] ?? 0,
            'shippedOrders'   => $statusCounts['Shipped'] ?? 0,
            'paidOrders'      => $statusCounts['Paid'] ?? 0,
            'invoicedOrders'  => $statusCounts['Invoiced'] ?? 0,
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
            'title'  => $this->title,
            'categories' => Category::pluck('name','de_cat'),
        ]));
    }
}
