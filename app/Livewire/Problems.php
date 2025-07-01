<?php

namespace App\Livewire;

use App\Models\Order_status;
use App\Services\OrderItemService;
use Flux\Flux;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Label Reprent & Problems')]
class Problems extends Component
{
    protected OrderItemService $orderItemService;

    public function boot(OrderItemService $orderItemService)
    {
        $this->orderItemService = $orderItemService;
    }

    public string $title = 'Problems';

    public $problemType;

    public $newStatus;

    public $m_id;

    public $remark;

    public $SOID;

    public $qty;

    public $remarks_cn;

    public function render()
    {
        $problmes = $this->orderItemService->getItemsData()
            ->whereIn('order_statuses.status', ['C_Problem', 'P_Problem', 'D_Problem'])->get();

        // dd($problmes);
        return view('livewire.problems')->with([
            'problmes' => $problmes,
        ]);
    }

    public function adjustProblem($m_id)
    {
        $this->m_id = $m_id;
        $edit = Order_status::where('id', $m_id)->first();
        // dd($edit);
        $this->newStatus = $edit->status;
        $this->problemType = $edit->status;
        $this->remark = $edit->problems;
        $this->SOID = $edit->supplier_order_id;
        $this->qty = $edit->qty_label;
        $this->remarks_cn = $edit->remarks_cn;
        Flux::modal('adjust-problem')->show();

        // dd($this->m_id , 'waiting for Joschua Busniss logic');
    }

    public function editProblem()
    {
        // Check if the m_id is empty and show an error message
        if (empty($this->m_id)) {
            session()->flash('error', 'You need to refresh your page!');

            return; // Stop further execution if there's no m_id
        }

        // Prepare the base data for update
        $updateData = [
            'status' => $this->newStatus,
            'problems' => $this->remark,
            'qty_label' => $this->qty,
            'remarks_cn' => $this->remarks_cn,
        ];

        // Check for a specific remark and adjust the data accordingly
        if ($this->remark !== 'C_Problem') {
            if ($this->newStatus === 'NSO') {
                $updateData['supplier_order_id'] = null;
            } else {
                $updateData['supplier_order_id'] = $this->SOID;
            }
        } else {

            $updateData['supplier_order_id'] = $this->SOID;
        }

        // Update the status with the prepared data
        Order_status::where('id', $this->m_id)->update($updateData);

        // Flash success message
        session()->flash('success', 'Problem updated successfully!');
        Flux::modal('adjust-problem')->close();

    }
}
