<div>
    {{-- Header with Back, Title and Search --}}
    <div class="flex justify-between mt-0 items-center">
        <flux:button icon="backward" onclick="history.back()" class="bg-blue-800! text-white! hover:bg-blue-700!">
            Back
        </flux:button>

        <flux:text color="blue" class="text-base">{{ $title }}</flux:text>

        <div class="flex">
            <flux:input class="md:w-50" wire:model.live="search" icon="magnifying-glass"
                placeholder="Search {{ $title }}" />
        </div>
    </div>

    {{-- Flash message --}}
    @if (session('success'))
    <div class="mt-2 text-center">
        <flux:callout variant="success" icon="check-circle" heading="{{ session('success') }}" />
    </div>
    @endif

    {{-- Express Orders --}}
    <p class="mt-6 font-semibold text-gray-700 dark:text-gray-200">Express Orders</p>

    <table class="custom-table w-full text-sm text-gray-500 dark:text-gray-400 mt-2.5">
        <thead class="sticky top-0 z-10 bg-white dark:bg-gray-800">
            <tr>
                <th>Supplier ID</th>
                <th>Supplier</th>
                <th>Order Type</th>
                <th>Count</th>
                <th>QTY</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($expressOrders as $order)
            <tr wire:key="express-{{ $order->SUPPID }}" @if (str_contains($order->comment, 'Expres')) class='bg-red-50'
                @endif>
                <td>
                    <flux:button 
                    class=" bg-gray-500! hover:bg-gray-400! text-white!"
                    size='sm' icon:trailing='arrow-right-circle'
                        wire:click="showTable({{ $order->SUPPID }} ,'Express')">
                        {{ $order->SUPPID }}
                    </flux:button>

                </td>
                <td>{{ $order->name }}</td>
                <td>{{ $order->type_name }}</td>
                <td>{{ $order->countItems }}</td>
                <td>{{ $order->QTY }}</td>
                <td>
                    <flux:button variant="primary" wire:click="createSupplierOrder({{ $order->SUPPID }})"
                        wire:confirm='Are you sure?' size="sm"
                        icon="plus-circle">Supplier order</flux:button>
                </td>
            </tr>
            @if($tableId == $order->SUPPID)
            <tr>
                <td colspan="6">@include('partials.itemOrders')</td>
            </tr>
            @endif
            @empty
            <tr>
                <td colspan="6" class="text-center">No records found</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    {{-- Normal NSO Orders --}}
    @isset($supplierId)
    {{ $supplierId }}
    @endisset
    <p class="mt-6 font-semibold text-gray-700 dark:text-gray-200">Normal Orders</p>
    <table class="custom-table w-full text-sm text-gray-500 dark:text-gray-400 mt-2.5">
        <thead class="sticky top-0 z-10 bg-white dark:bg-gray-800">
            <tr>
                <th>Supplier ID</th>
                <th>Supplier</th>
                <th>Order Type</th>
                <th>Count</th>
                <th>QTY</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($nsoOrders as $order)
            <tr wire:key="nso-{{ $order->SUPPID }}">
                <td>
                    <flux:button 
                    class=" bg-gray-500! hover:bg-gray-400! text-white!"
                    size='sm' icon:trailing='arrow-right-circle'
                        wire:click="showTable({{ $order->SUPPID }} ,'Normal')">
                        {{ $order->SUPPID }}
                    </flux:button>

                </td>
                <td>{{ $order->name }}</td>
                <td>{{ $order->type_name }}</td>
                <td>{{ $order->countItems }}</td>
                <td>{{ $order->QTY }}</td>
                <td>
                    <flux:button variant="primary" wire:click="createSupplierOrder({{ $order->SUPPID }})" size="sm"
                        icon="plus-circle">Supplier order</flux:button>
                </td>
            </tr>
            @if($tableId == $order->SUPPID)
            <tr>
                <td colspan="6">@include('partials.itemOrders')</td>
            </tr>
            @endif
            @empty
            <tr>
                <td colspan="6" class="text-center">No records found</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>