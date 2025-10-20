<div>
@include('partials.cargos')
    <div class="flex justify-between mt-0">
        <div>
            <flux:button icon="backspace" onclick="history.back()" class="bg-blue-800! text-white! hover:bg-blue-700!">
                Back
            </flux:button>
        </div>
        <flux:text color="blue" class="text-base">{{ $title }}</flux:text>
        <div class="flex">
            <flux:dropdown>
                <flux:button icon:trailing="chevron-down">Category</flux:button>
                <flux:menu>

                    @foreach ($categories as $de_cat => $name)
                    <flux:separator />
                    <flux:menu.item icon='plus' wire:click="$set('catName', {{ $de_cat }})">
                        {{ $name }}
                    </flux:menu.item>
                    @endforeach
                    {{-- <flux:menu.item wire:click="$set('catName',)"> --}}
                </flux:menu>
            </flux:dropdown>


            <flux:input class="md:w-50" wire:model.live="search" icon="magnifying-glass"
                placeholder="Search {{ $title }}" />
        </div>
    </div>
    @if (session('success'))
    <div class="mt-2 text-center">
        <flux:callout variant="success" icon="check-circle" heading="{{ session('success') }}" />
    </div>
    @endif

    <table class="table-default mt-2.5">
        <thead class="sticky! top-0! z-10!">
            <tr>
                <th rowspan="2">No</th>
                <th rowspan="2">Re Assign</th>
                <th rowspan="2">Order No.</th>
                <th rowspan="2">Catgy</th>
                <th rowspan="2">Cargo</th>
                <th rowspan="2">Comment</th>
                <th rowspan="2">Created</th>
                <th rowspan="2">Emailed</th>
                <th rowspan="2">Delivery</th>
                <th>Total</th>
                <th>NSO</th>
                <th>SO</th>
                <th>Problem</th>
                <th>Purchase</th>
                 <th>Paid</th>
                <th>Checked</th>
               
                <th>Printed</th>
                <th>Invoiced</th>
                <th>Shipped</th>
            </tr>
            <tr>
                <th>{{ $totalItemOrders }}</th>
                <th>{{ $openOrders }}</th>
                <th>{{ $orderOrders }}</th>
                <th>{{ $problemOrders }}</th>
                <th>{{ $purchaseOrders }}</th>
                <th>{{ $paidOrders }}</th>
                <th>{{ $checkOrders }}</th>
                
                <th>{{ $printOrders }}</th>
                <th>{{ $invoicedOrders }}</th>
                <th>{{ $shippedOrders }}</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($orders as $order)
            <tr wire:key="{{ $order->id }}">
                <td>{{ $loop->iteration }}</td>
                <td>
                    <flux:button 
                        icon='arrow-path' size='sm'
                        wire:click="selectCargo('{{ $order->order_no }}')" 
                        class=" bg-gray-500! hover:bg-gray-400! text-white!">
                        ReAssign
                    </flux:button>
                </td>
                <td>
                    <a class="active-link" href="{{ route('orderItems') }}/{{ $order->order_no }}" wire:navigate>{{
                        $order->order_no }}</a>
                </td>
                <td>{{ $order->categories?->name }}</td>
                <td>
                    @php
                    $cargoIds = $order->orderItems->pluck('status.cargo_id')->filter()->unique();
                    @endphp
                    {{ $cargoIds->implode(', ') }}
                </td>
                <td class="text-left! whitespace-normal break-words">{{ $order->comment }}</td>

                <td>{{ formatGeneralDate($order->date_created) }}</td>
                <td>{{ formatGeneralDate($order->date_emailed) }}</td>
                <td>{{ formatGeneralDate($order->date_delivery) }}</td>
                <td>{{ $order->orderItems->count() }}</td>

                @php $nso = $order->status_counts['NSO'] ?? 0; @endphp
                <td>
                    <a wire:navigate href="{{ route('orderItems') }}/{{ $order->order_no }}/NSO"
                        class="{{ $nso > 0 ? 'active-link' : '' }}">
                        {{ $nso }}
                    </a>
                </td>

                @php $so = $order->status_counts['SO'] ?? 0; @endphp
                <td>
                    <a wire:navigate href="{{ route('orderItems') }}/{{ $order->order_no }}/SO"
                        class="{{ $so > 0 ? 'active-link' : '' }}">
                        {{ $so }}
                    </a>
                </td>

                @php
                $problem = ($order->status_counts['P_Problem'] ?? 0) +
                ($order->status_counts['C_Problem'] ?? 0) +
                ($order->status_counts['D_Problem'] ?? 0);
                @endphp
                <td>
                    <a wire:navigate href="{{ route('orderItems') }}/{{ $order->order_no }}/Problem"
                        class="{{ $problem > 0 ? 'active-link' : '' }}">
                        {{ $problem }}
                    </a>
                </td>

                @php $purchased = $order->status_counts['Purchased'] ?? 0; @endphp
                <td>
                    <a wire:navigate href="{{ route('orderItems') }}/{{ $order->order_no }}/Purchased"
                        class="{{ $purchased > 0 ? 'active-link' : '' }}">
                        {{ $purchased }}
                    </a>
                </td>

                
                @php $paid = $order->status_counts['Paid'] ?? 0; @endphp
                <td>
                    <a wire:navigate href="{{ route('orderItems') }}/{{ $order->order_no }}/Paid"
                        class="{{ $paid > 0 ? 'active-link' : '' }}">
                        {{ $paid }}
                    </a>
                </td>

                @php $checked = $order->status_counts['Checked'] ?? 0; @endphp
                <td>
                    <a wire:navigate href="{{ route('orderItems') }}/{{ $order->order_no }}/Checked"
                        class="{{ $checked > 0 ? 'active-link' : '' }}">
                        {{ $checked }}
                    </a>
                </td>


                @php $printed = $order->status_counts['Printed'] ?? 0; @endphp
                <td>
                    <a wire:navigate href="{{ route('orderItems') }}/{{ $order->order_no }}/Printed"
                        class="{{ $printed > 0 ? 'active-link' : '' }}">
                        {{ $printed }}
                    </a>
                </td>

                @php $invoiced = $order->status_counts['Invoiced'] ?? 0; @endphp
                <td>
                    <a wire:navigate href="{{ route('orderItems') }}/{{ $order->order_no }}/Invoiced"
                        class="{{ $invoiced > 0 ? 'active-link' : '' }}">
                        {{ $invoiced }}
                    </a>
                </td>

                @php $shipped = $order->status_counts['Shipped'] ?? 0; @endphp
                <td>
                    <a wire:navigate href="{{ route('orderItems') }}/{{ $order->order_no }}/Shipped"
                        class="{{ $shipped > 0 ? 'active-link' : '' }}">
                        {{ $shipped }}
                    </a>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="19">No records found</td>
            </tr>
            @endforelse
        </tbody>
    </table>
    <div class="container mx-auto w-100">{{ $orders->links() }}</div>
</div>