<?php

namespace App\Livewire;

use App\Models\Supplier;
use App\Models\Supplier_type;
use Flux\Flux;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Title;

#[Title('Suppliers management')]
class Suppliers extends Component
{
    use WithPagination;

    public bool $enableEdit = false;
    public bool $isUpdate=false;
    
    public $supplierId;
    public string $search = '';
    public string $title = 'Suppliers';

    public $name, $name_cn, $company_name, $extra_note, $min_order_value, $order_type_id, $province;
    public $is_fully_prepared, $is_tax_included, $is_freight_included, $city, $street, $full_address;
    public $contact_person, $phone, $mobile, $email, $website;

    protected array $rules = [
        'name'           => 'required',
        'contact_person' => 'required',
        'full_address'   => 'required',
    ];

    public function render()
    {
        return view('livewire.suppliers')->with([
            'suppliers'    => Supplier::search($this->search)->with('orderType')->orderBy('id')->paginate(100),
            'title'        => $this->title,
            'order_types'  => Supplier_type::all(),
        ]);
    }

    public function save()
    {
        $this->validate();

        $created = Supplier::create($this->getSupplierData());

        if (! $created) {
            session()->flash('error', 'Something went wrong in creating new supplier');
            return;
        }

        Flux::modal('myModal')->close();
        session()->flash('success', 'Supplier added successfully');
        $this->reset();
    }

    public function edit($id)
    {
        $supplier = Supplier::findOrFail($id);

        $this->supplierId = $id;
        $this->isUpdate   = true;
        $this->fillSupplierData($supplier);

        Flux::modal('myModal')->show();
    }

    public function update()
    {
        $this->validate();

        $updated = Supplier::where('id', $this->supplierId)->update($this->getSupplierData());

        if (! $updated) {
            session()->flash('error', 'Something went wrong in updating supplier');
            return;
        }

        Flux::modal('myModal')->close();
        session()->flash('success', 'Supplier updated successfully');
        $this->reset();
    }

    public function delete($id)
    {
        dd('Delete is temporarily blocked');
        $supplier = Supplier::findOrFail($id);
        $supplier->delete();

        $this->isUpdate = false;
        session()->flash('success', 'Supplier deleted successfully');
    }

    public function cancel()
    {
        $this->isUpdate = false;
         $this->reset();
    }

    private function fillSupplierData(Supplier $supplier): void
    {
        $this->fill($supplier->only([
            'name', 'name_cn', 'company_name', 'extra_note', 'min_order_value',
            'order_type_id', 'province', 'is_fully_prepared', 'is_tax_included',
            'is_freight_included', 'city', 'street', 'full_address',
            'contact_person', 'phone', 'mobile', 'email', 'website'
        ]));
    }

    private function getSupplierData(): array
    {
        return collect([
            'name', 'name_cn', 'company_name', 'extra_note', 'min_order_value',
            'order_type_id', 'province', 'is_fully_prepared', 'is_tax_included',
            'is_freight_included', 'city', 'street', 'full_address',
            'contact_person', 'phone', 'mobile', 'email', 'website'
        ])->mapWithKeys(fn ($field) => [$field => $this->{$field}])->toArray();
    }
}
