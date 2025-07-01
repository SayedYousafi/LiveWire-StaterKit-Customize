<?php

namespace App\Livewire;

use App\Models\Order_status;
use App\Services\OrderItemService;
use Flux\Flux;
use Livewire\Component;
use Livewire\WithPagination;

class Repreints extends Component
{
    use WithPagination;

    protected OrderItemService $orderItemService;

    public function boot(OrderItemService $orderItemService)
    {
        $this->orderItemService = $orderItemService;
    }

    public $search = '';

    public $qty_no;

    public $currentQty;

    public function render()
    {
        $labels = $this->orderItemService->getItemsData(($this->search))
            ->where('order_statuses.status', 'Printed')->paginate(50);

        // dd($labels);
        return view('livewire.repreints')->with([
            'labels' => $labels,
        ]);
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function clearSearch()
    {
        $this->search = '';
        $this->resetPage(); // Ensure it resets to the first page

    }

    public function selectQtyDelivery($id)
    {
        $this->qty_no = $id;

        $qty = Order_status::where('master_id', "$this->qty_no")->first();
        $this->currentQty = $qty->qty_label;
        Flux::modal('edit-qty')->show();
    }

    public function updateQty()
    {
        $done = Order_status::where('master_id', "$this->qty_no")->first()
            ->update(
                [
                    'qty_label' => $this->currentQty,
                ]);
        if ($done) {
            session()->flash('success', 'QTY delivery set successfully !');
            $this->reset('currentQty');
            Flux::modal('edit-qty')->close();
        }
    }
}
