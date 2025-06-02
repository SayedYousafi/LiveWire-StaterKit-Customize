<?php

namespace App\Livewire;

use Flux\Flux;
use Carbon\Carbon;
use App\Models\Order;
use App\Models\Value;
use Livewire\Component;
use Livewire\Attributes\Title;

#[Title('Mangement')]
class Admin extends Component
{
    public $txtValue, $valueId;
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

    public function getValue($id=1)
    {
       
        $this->valueId = $id;
        $getvalue = Value::findOrFail($id);
        //dd($getvalue);
        $this->txtValue = $getvalue->value;
        Flux::modal('myModal')->show();
    }
    
    public function setValue()
    {
        Value::where('id',$this->valueId)->update([
            'value' => $this->txtValue,
        ]);
        Flux::modal('myModal')->close();
        session()->flash('success', 'Values Set Successfully !');
    }
}
