<div>
    @include('partials.cargos')
    @include('partials.edit-qty')
    <div class="flex justify-between mt-0">
        <div>
            <flux:button icon="backspace" 
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

    <table class="table-default mt-2">
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
                <th>SOID</th>
                <th colspan="2">Actions</th>
            </tr>

        </thead>
        <tbody>
            @forelse ($orderItems as $order)
            @php
            // Gather all numeric quantities
            $allQuantities = collect([
            //is_numeric($order->qty_split) ? $order->qty_split : null,
            is_numeric($order->qty) ? $order->qty : null,
            is_numeric($order->qty_label) ? $order->qty_label : null,
            ])->reject(fn($q) => $q === null);
            // Get unique values for display
            $quantities = $allQuantities->unique()->values();
            $displayQty = $quantities->implode('/');

            // Get the first available numeric quantity for math
            $numericQty = $allQuantities->first() ?? 0;
            @endphp
            <tr wire:key="{{ $order->ID }}" >
                <td>{{ $loop->iteration }}</td>
                <td><a href="{{ route('itemEdit', $order->item_id) }}" target="_blank" class="!text-blue-600 hover:!underline">{{ $order->ean }}</a> </td>
                <td class="text-left! whitespace-normal break-words">{{ $order->item_name }}
                    / {{ $order->item_name_cn }}
                </td>
                <td>{{ $p = $order->price_rmb }}</td>
                <td>{{ $displayQty }}</td>
                <td>{{ $p*$numericQty }}</td>
                <td class="text-left!"> {{ $order->name }} - {{ $order->supplierId }}</td>
                <td>{{ $order->order_no }}</td>
                <td class="text-left! whitespace-normal break-words">{{ $order->remark}} / 
                    {{ $order->remark_de }} / {{ $order->remarks_cn }} / {{ $order->note_cn }}
                </td>
                <td>{{ $order->status }}</td>
                <td>{{ $order->cargo_id }}</td>
                <td>{{ $order->supplier_order_id }}</td>
                <td>
                    <flux:button size="sm" icon='share' class="bg-yellow-600! text-white! hover:bg-yellow-500"
                    wire:confirm='Are you sure spliting this?' wire:click="splitDelivery('{{ $order->master_id }}')">
                    Split</flux:button>
                </td>
                <td>
                    <flux:button
                    wire:click="selectCargo({{ $order->ID }})" 
                    class=" bg-gray-500! hover:bg-gray-400! text-white!"
                    size='sm' icon='arrow-uturn-left'>ReAssign</flux:button></td>
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