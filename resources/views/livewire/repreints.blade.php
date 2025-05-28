<div class="container">
    <div class="flex justify-between mt-3">
        <flux:header size='lg'>Label Reprint</flux:header>
        <div>
            <flux:input class="md:w-50" wire:model.live.debounce.500ms="search" icon="magnifying-glass"
                placeholder="Search" />
        </div>
    </div>
    @if (session('success'))
    <div class=" mt-2 text-center mb-2">
        <flux:callout variant="success" icon="check-circle" heading="{{ session('success') }}" />
    </div>
    @endif
    @if (session('error'))
    <div class=" mt-2 text-center mb-2">
        <flux:callout variant="danger" icon="x-circle" heading="{{ session('error') }}" />
    </div>
    @endif
    {{-- @include('partials.adjust-problem') --}}
    @include('partials.edit-qty')
    <table class="table-default mt-2">
        <thead>
            <tr>
                <th>ID</th>
                <th>EAN</th>
                <th>Item Name</th>
                <th>Remark</th>
                <th>Order_no</th>
                <th>QTY</th>
                <th>RMB</th>
                <th>SOID</th>
                <th>Status</th>
                <th colspan="2">Actions</th>
            </tr>
        </thead>
        @php
        $totalQty = 0;
        $grandTotal = 0;
        $volum = 0;
        $Tweight = 0;

        @endphp
        <tbody>
            @foreach ($labels as $item)
            @php
            $qty = $item->qty;
            $qtySplit = $item->qty_split;
            $width = $item->width;
            $height = $item->height;
            $length = $item->length;
            $weight = $item->weight;

            $currentQty = ($qtySplit != $qty) ? $qtySplit : $qty;
            $totalQty += $currentQty;
            $grandTotal += $qty * $item->price_rmb;

            $volum += ($width * $height * $length) * $qty;
            $Tweight += $weight;
            @endphp

            <tr @if(str_contains($item->master_id, '-1')) class="bg-blue-100 dark:bg-blue-900" @else
                wire:key="{{$item->ID }}" @endif>
                <td>{{$item->ID }}</td>
                <td>{{ $item->ean }}</td>
                <td>{{ $item->item_name }}</td>
                <td>{{ $item->remark_de }} / {{ $item->remarks_cn }} / {{ $item->remark }}</td>
                <td>{{ $item->order_no }}</td>
                {{-- <td>QTY = {{ $item->qty_label }}</td> --}}
                <td>
                    @if (str_contains($item->master_id, '-1'))
                    {{ $item->qty_split }}/{{ $item->qty }}/{{ $item->qty_label }}
                    @else
                    {{ $item->qty }}{{ $item->qty != $item->qty_label ? '/' . $item->qty_label : '' }}
                    @endif
                </td>

                <td>{{ $item->price_rmb }}</td>
                <td>{{ $item->supplier_order_id }}</td>


                <td>{{ $item->status }}</td>
                <td>
                    <flux:button size="sm" icon='code-bracket-square' class="bg-zinc-600! text-white! hover:bg-zinc-500"
                        wire:click="selectQtyDelivery('{{ $item->master_id }}')">QTY</flux:button>
                </td>
                <td>

                    <flux:button as="a" href="{{ route('print', $item->ID) }}" size='sm' icon='printer'
                        class="bg-green-500! hover:bg-green-400! text-white!" onclick="return confirm('Are you sure?')"
                        wire:click="$refresh">
                        Print
                    </flux:button>
                </td>
                @endforeach
        </tbody>

    </table>

</div>