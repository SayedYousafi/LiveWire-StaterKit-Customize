<?php

namespace App\Livewire;

use Carbon\Carbon;
use App\Models\Order;
use Livewire\Component;

class Admin extends Component
{
    public function render()
    {
        $oneYearAgo = Carbon::now()->subYear();
        $orderNos   = Order::where('created_at', '<=', $oneYearAgo)
            ->where('order_no', 'LIKE', '%DENI%')
            ->pluck('order_no')
            ->toArray();
        return view('livewire.admin')->with([
        
            'countOld' => count($orderNos),
            'orderNos' => $orderNos,
        
        ]);
    }
}
