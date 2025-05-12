<?php

namespace App\Livewire;

use App\Models\Supplier;
use Flux\Flux;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Suppliers management')]
class Suppliers extends Component
{
    public $name, $contact, $address, $update = false, $supplierId;

    public function render()
    {
        return view('livewire.suppliers')->with([
            'suppliers' => Supplier::all(),
        ]);
    }

    public function save()
    {
        $validated = $this->validate([
            'name' => 'required',
            'contact' => 'required',
            'address' => 'required',
        ]);
        $done = Supplier::create($validated);
        //dd($done);
        Flux::modal('suppliersModal')->close();
        session()->flash('success', 'Supplier added susscessfully');
        $this->reset();
    }

    public function edit($id)
    {
        $this->supplierId=$id;
        $this->update=true;
        Flux::modal('suppliersModal')->show();
        $supplier = Supplier::findOrFail($id);
        $this->name = $supplier->name;
        $this->contact = $supplier->contact;
        $this->address = $supplier->address;

    }

    public function updateSuppler()
    {
        $validated = $this->validate([
            'name' => 'required',
            'contact' => 'required',
            'address' => 'required',
        ]);
        $done = Supplier::where('id', $this->supplierId)->update($validated);
        Flux::modal('suppliersModal')->close();
        session()->flash('success', 'Supplier updated susscessfully');
        $this->reset();
    }

    public function delete($id)
    {
        $supplier = Supplier::findOrFail($id);
        $supplier->delete();
        $this->update=false;
    }
    
    public function cancel()
    {
        $this->update=false;
        $this->reset();
    }
}
