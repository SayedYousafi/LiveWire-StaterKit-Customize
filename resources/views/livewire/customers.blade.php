<div class="p-6 bg-white dark:bg-gray-900 rounded shadow">
    @if (session('success'))
    <flux:callout variant="success" heading="{{ session('success') }}" />
    @endif

    <flux:modal name="customerEditModal" class="max-w-6xl">
        @include('partials.customer-form')
    </flux:modal>
<div class="flex justify-between mt-3">
        <flux:button icon="plus-circle" class="bg-blue-600! text-white! hover:bg-blue-500!" wire:click="create">
    New customer
</flux:button>
<flux:text color="blue" class="text-base">{{ $title }}</flux:text>

        <div>
            <flux:input class="md:w-50" wire:model.live="search" icon="magnifying-glass"
                placeholder="Search {{ $title }}" />
        </div>
</div>


    {{-- <flux:button icon='plus-circle' class="bg-blue-600! text-white! hover:bg-blue-500!">New customer</flux:button> --}}
    
    <table class="table-default mt-3">
        <thead>
            <tr>
                <th>ID</th>
                <th>Customer Type</th>
                <th>Company</th>
                <th>Email</th>
                <th>Phone</th>
                <th>Website</th>
                <th colspan="2"> Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($customers as $key => $customer )
            <tr>
                <td>{{ $customer->id }}</td>
                <td>{{ $customer->customerType?->type_name }}</td>
                <td>{{ $customer->customer_company_name }}</td>
                <td>{{ $customer->email }}</td>
                <td>{{ $customer->phone }}</td>
                <td>{{ $customer->website }}</td>
                <td>
                    <flux:button icon='pencil-square' size="sm" variant="primary" wire:click="edit({{ $customer->id }})">
                        Edit
                    </flux:button>

                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" align="center"> No records found</td>
            </tr>

            @endforelse
        </tbody>
    </table>
</div>