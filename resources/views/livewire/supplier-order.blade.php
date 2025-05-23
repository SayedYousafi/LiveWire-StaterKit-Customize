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
    <div class="mt-3 text-center mb-3">
        <flux:callout variant="success" icon="check-circle" heading="{{ session('success') }}" />
    </div>
    @endif
    @if (session('error'))
    <div class="mt-2 text-center">
        <flux:callout variant="danger" icon="check-circle" heading="{{ session('error') }}" />
    </div>
    @endif

    @include('partials.edit-so')
    @include('partials.edit-qty')
    @include('partials.edit-refeNo')
    @include('partials.edit-problem')
    @include('partials.edit-check-problem')
    @include('partials.adjust-problem')
    @include('partials.set-special-price')

    <table class="table-default">
        <thead class="sticky top-0 z-10 bg-white dark:bg-gray-800">
            <tr>
                <th>SOID
                    <flux:button variant="danger" size='sm' wire:click='cancel' icon='x-circle'></flux:button>
                </th>
                <th>Supplier - ID</th>
                <th>Order Type</th>
                <th>Ref No.</th>
                <th>Remark</th>
                <th>Date created</th>
                <th colspan="2">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($sos as $order)
            <tbody wire:key="sos-group-{{ $order->id }}">
            <tr wire:key="sos-{{ $order->id }}" @if (str_contains($order->comment, 'Expres')) class='bg-red-50' @endif>
                <td>
                    <flux:button class=" bg-gray-500! hover:bg-gray-400! text-white!" size='sm'
                        icon:trailing='arrow-right-circle' wire:click="showTable({{$order->id }})">
                        {{ $order->id }}
                    </flux:button>
                </td>
                <td>{{ $order->supplier->name }} - {{ $order->supplier->id }}</td>
                <td>{{ $order->orderTypes->type_name }}</td>
                <td>{{ $order->ref_no }}</td>
                <td>{{ $order->remark }}</td>
                <td>{{ myDate($order->created_at, 'd-m-Y') }}</td>
                <td>
                    <flux:button class=" bg-blue-600! hover:bg-blue-500! text-white!"
                        wire:click="editSO({{ $order->id }})" size="sm" icon="pencil-square">Edit</flux:button>
                </td>
                @if(
                ($order->paid === 'N' && !is_null($order->ref_no)) ||
                ($order->status->status === 'Purchased' && !is_null($order->ref_no))
                )
                <td>
                    <flux:button wire:click="payOrder({{ $order->id }}, {{ $order->supplier->supplier_id }})"
                        class=" bg-amber-500! hover:bg-amber-400! text-white!" icon="currency-euro" size="sm"
                        title="If there is ref. no and all are purchased">
                        Pay
                    </flux:button>
                </td>
                @endif

            </tr>
            @if($tableId == $order->id)
            <tr wire:key="sos-items-{{ $order->id }}">
                <td colspan="8">@include('partials.itemOrders')</td>
            </tr>
            @endif
            </tbody>
            @empty
            <tr>
                <td colspan="8" class="text-center">No records found</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<script>
    document.addEventListener('scroll-to-top', () => {
        window.scrollTo({ top: 0, behavior: 'smooth' });
    });
</script>
