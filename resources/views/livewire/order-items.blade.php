<div>
    @include('partials.cargos')
    <div class="flex justify-between mt-0">
        <div>
            <flux:button icon="backward" 
            onclick="history.back()"
            class="bg-blue-800! text-white! hover:bg-blue-700!">
                Back
            </flux:button>
        </div>
        <flux:text color="blue" class="text-base">{{ $title }}</flux:text>
        <div class="flex">
            <flux:input class="md:w-50" wire:model.live="search" icon="magnifying-glass"
                placeholder="Search {{ $title }}" />
        </div>
    </div>
    @if (session('success'))
    <div class="mt-2 text-center">
        <flux:callout variant="success" icon="check-circle" heading="{{ session('success') }}" />
    </div>
    @endif
    

    <table class="custom-table w-full text-sm text-gray-500 dark:text-gray-400 mt-2.5">
        <thead class="sticky! top-0! z-10!">
            <tr>
                <th>S. No</th>
                <th>EAN</th>
                <th>Item name</th>
                <th>Price</th>
                <th>QTY</th>
                <th>Total</th>
                <th>Supplier</th>
                <th>Order No.</th>
                <th>Remarks</th>
                <th>Status</th>
                <th>Cargo</th>
                <th colspan="2">Actions</th>
            </tr>

        </thead>
        <tbody>
            @forelse ($orderItems as $order)
            <tr wire:key="{{ $order->ID }}">
                <td>{{ $loop->iteration }}</td>
                <td>{{ $order->ean }}</td>
                <td class="text-left! whitespace-normal break-words">{{ $order->item_name }}
                    / {{ $order->item_name_cn }}
                </td>
                <td>{{ $p = $order->price_rmb }}</td>
                <td>{{ $q = $order->qty }}</td>
                <td>{{ $p*$q }}</td>
                <td class="text-left!"> {{ $order->name }} - {{ $order->supplierId }}</td>
                <td>{{ $order->order_no }}</td>
                <td class="text-left! whitespace-normal break-words">{{ $order->remark}} / 
                    {{ $order->remark_de }} / {{ $order->remarks_cn }} / {{ $order->note_cn }}
                </td>
                <td>{{ $order->status }}</td>
                <td>{{ $order->cargo_id }}</td>
                <td><flux:button variant='primary' size='sm' icon='divide'>Split</flux:button></td>
                <td>
                    <flux:button
                    wire:click="selectCargo({{ $order->ID }})" 
                    class=" bg-gray-500! hover:bg-gray-400! text-white!"
                    size='sm' icon='arrow-uturn-left'>Re-Assign</flux:button></td>
            </tr>
            @empty
            <tr>
                <td colspan="19">No records found</td>
            </tr>
            @endforelse
        </tbody>
    </table>
    <div class="flex mt-3 justify-center"> {{ $orderItems->links() }}</div>
</div>