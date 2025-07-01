<?php
namespace App\Livewire;

use App\Models\Customer;
use App\Models\CustomerType;
use Flux\Flux;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Customers')]
class Customers extends Component
{
    public string $search = '';

    public string $title = 'Customers';

    public ?Customer $model = null;

    public array $customer = [];



    public function updatedCustomerCustomerTypeId($value): void
    {
        // Only when GT-Warehouse is selected
        if ($value == 1) {
            $this->fillCustomerFromTemplate();
        }
    }

    protected function fillCustomerFromTemplate(): void
    {
        // Replace 1 with the actual ID of your "Customer One"
        $templateCustomer = Customer::find(1);

        if ($templateCustomer) {
            // Fill the customer form, but preserve the selected customer_type_id
            $this->customer                     = $templateCustomer->toArray();
            $this->customer['customer_type_id'] = 1;
            $this->customer['customer_company_name'] = 'GT-DE-';
        }
    }

    public function render()
    {
        return view('livewire.customers')->with([
            'customers'     => Customer::with('customerType')->search($this->search)->get(),
            'customerTypes' => CustomerType::all(),
        ]);
    }

    public function save(): void
    {
        $validated = $this->validate([
            'customer.customer_type_id'        => 'required|exists:customer_types,id',
            'customer.customer_company_name'   => 'required|string|max:255',
            'customer.phone'                   => 'nullable|string|max:50',
            'customer.tax_no'                  => 'nullable|string|max:50',
            'customer.email'                   => 'nullable|email|max:255',
            'customer.website'                 => 'nullable|url|max:255',
            'customer.contact_first_name'      => 'nullable|string|max:255',
            'customer.contact_phone'           => 'nullable|string|max:50',
            'customer.contact_mobile'          => 'nullable|string|max:50',
            'customer.contact_email'           => 'nullable|email|max:255',
            'customer.country'                 => 'nullable|string|max:100',
            'customer.city'                    => 'nullable|string|max:100',
            'customer.postal_code'             => 'nullable|string|max:20',
            'customer.address_line1'           => 'nullable|string|max:255',
            'customer.delivery_country'        => 'nullable|string|max:100',
            'customer.delivery_city'           => 'nullable|string|max:100',
            'customer.delivery_postal_code'    => 'nullable|string|max:20',
            'customer.delivery_address_line1'  => 'nullable|string|max:255',
            'customer.delivery_company_name'   => 'nullable|string|max:255',
            'customer.delivery_contact_person' => 'nullable|string|max:255',
            'customer.delivery_contact_phone'  => 'nullable|string|max:50',
            'customer.remark'                  => 'nullable|string|max:500',
        ]);

        $this->model->fill($validated['customer']);
        $this->model->save();

        Flux::modal('customerEditModal')->close();

        $this->resetForm();
        session()->flash('success', 'Customer saved successfully!');
    }

    public function edit($id): void
    {
        $this->model    = Customer::findOrFail($id);
        $this->customer = $this->model->toArray();
        Flux::modal('customerEditModal')->show();
    }

    public function create(): void
    {
        $this->resetForm();
        Flux::modal('customerEditModal')->show();
    }

    private function resetForm(): void
    {
        $this->model    = new Customer;
        $this->customer = [
            'customer_type_id'        => '',
            'customer_company_name'   => '',
            'phone'                   => '',
            'tax_no'                  => '',
            'email'                   => '',
            'website'                 => '',
            'contact_first_name'      => '',
            'contact_phone'           => '',
            'contact_mobile'          => '',
            'contact_email'           => '',
            'country'                 => '',
            'city'                    => '',
            'postal_code'             => '',
            'address_line1'           => '',
            'delivery_country'        => '',
            'delivery_city'           => '',
            'delivery_postal_code'    => '',
            'delivery_address_line1'  => '',
            'delivery_company_name'   => '',
            'delivery_contact_person' => '',
            'delivery_contact_phone'  => '',
            'remark'                  => '',
        ];
    }
    public function cancel()
    {
        $this->resetForm();
        Flux::modal('customerEditModal')->close();
    }
}
