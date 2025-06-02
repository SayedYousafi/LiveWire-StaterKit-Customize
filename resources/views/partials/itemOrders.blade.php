<table class="table-default">
    <thead>
        <tr class="table-highlighted">
            <th>#</th>
            <th>EAN</th>
            <th>Item Name</th>
            <th>Remarks</th>
            <th>Order_no</th>
            <th>CargoId</th>
            <th>V(dm³)</th>
            <th>W(kg)</th>
            <th>QTY</th>
            <th>RMB</th>
            <th>Total</th>
            <th>Status</th>
            <th colspan="3">Actions</th>
        </tr>
    </thead>

    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
        @php
        $totalQty = 0;
        $grandTotal = 0;
        $volum = 0;
        $Tweight = 0;
        @endphp

        @if(!empty($itemOrders))
        @foreach ($itemOrders as $itemOrder)

        @php
        // Set status
        $order_state = $itemOrder->status;

        // Gather all numeric quantities
        $allQuantities = collect([
        is_numeric($itemOrder->qty_split) ? $itemOrder->qty_split : null,
        is_numeric($itemOrder->qty) ? $itemOrder->qty : null,
        is_numeric($itemOrder->qty_label) ? $itemOrder->qty_label : null,
        ])->reject(fn($q) => $q === null);

        // Get unique values for display
        $quantities = $allQuantities->unique()->values();
        $displayQty = $quantities->implode('/');

        // Get the first available numeric quantity for math
        $numericQty = $allQuantities->first() ?? 0;

        // Accumulate totals
        $totalQty += $numericQty;
        $grandTotal += $numericQty * $itemOrder->price_rmb;

        $width = $itemOrder->width;
        $height = $itemOrder->height;
        $length = $itemOrder->length;
        $weight = $itemOrder->weight * $numericQty;

        $volum += ($width * $height * $length) * $numericQty;
        $Tweight += $weight;
        @endphp

        <tr wire:key="status-buttons-{{ $itemOrder->master_id }}-{{ $itemOrder->status }}" 
            @if(str_contains($itemOrder->comment, 'express')) class="bg-red-50 dark:bg-red-900" @endif>
            <td>{{ $loop->iteration }}</td>
            <td>
                @if ($itemOrder->status=='SO')
                <flux:button icon='arrow-left-start-on-rectangle' size='sm'
                    wire:click="setBackSO('{{$itemOrder->master_id}}')" wire:confirm='Are you sure?' variant="danger">
                    NSO
                </flux:button>
                @endif
                {{ $itemOrder->ean }}
            </td>
            <td>{{ $itemOrder->item_name }}</td>
            <td>{{ $itemOrder->remark_de }} / {{ $itemOrder->remarks_cn }} / {{ $itemOrder->remark }}</td>
            <td>{{ $itemOrder->order_no }}</td>
            <td>{{ $itemOrder->cargo_id }}</td>
            <td>{{ formatDecimal((($width*$height*$length)*$numericQty)/1000) }}</td>
            <td>{{ $weight }}</td>
            <td>
                {{-- @if (str_contains($itemOrder->master_id, '-1'))
                {{ $itemOrder->qty_split }}/{{ $itemOrder->qty }}/{{ $itemOrder->qty_label }}
                @else
                {{ $itemOrder->qty }}{{ $itemOrder->qty != $itemOrder->qty_label ? '/' . $itemOrder->qty_label : '' }}
                @endif --}}

                {{ $displayQty }}

            </td>

            @if ($itemOrder->is_rmb_special=='Y')
            <td>{{ $itemOrder->rmb_special_price }}</td>
            <td>{{ $itemOrder->qty * $itemOrder->rmb_special_price }}</td>
            @else
            <td>{{ $itemOrder->price_rmb }}</td>
            <td>{{ $itemOrder->qty * $itemOrder->price_rmb }}</td>
            @endif

            <td>{{ $itemOrder->status }}</td>
            @if ($title !== 'NSOs')
            @if (!in_array($order_state, ['Invoiced', 'Shipped']))
            @if ($itemOrder->is_rmb_special=='Y')
            <td nowrap>
                <flux:button size='sm' icon='currency-dollar' class=" bg-red-500! hover:bg-red-400! text-white!"
                    wire:click="specialPriceSelected({{$itemOrder->sqrID}})">
                    Set price
                </flux:button>
            </td>
            @endif
             
            <td>
                <flux:button class=" bg-gray-500! hover:bg-gray-400! text-white!" size='sm' icon='pencil'
                    wire:click="changeQty({{ $itemOrder->sqrID}})">
                    QTY
                </flux:button>
            </td>
            @else
            <td> Finished</td>
            @endif
            @if ($order_state=='SO')
            <td>
                <flux:button wire:click="openDetails('{{$itemOrder->master_id}}')" size='sm' icon='currency-euro'
                    class=" bg-green-500! hover:bg-green-400! text-white!">
                    Purchase
                </flux:button>
            </td>
            <td>
                <flux:button wire:click="pProblem('{{$itemOrder->master_id}}')" size='sm' icon='speaker-wave'
                    class="bg-amber-500! hover:bg-amber-400! text-white!">
                    P_Problem
                </flux:button>
            </td>
            @elseif ($count_item==$count_purchased && $order_state=='Purchased')
            <td nowrap>
                <flux:button wire:click="pProblem('{{$itemOrder->master_id}}', 'problem')" size='sm' icon='speaker-wave'
                    class="bg-amber-500! hover:bg-amber-400! text-white!">
                    P_Problem
                </flux:button>
                <flux:button wire:click="getRefNo('{{$itemOrder->supplier_order_id}}')" size='sm' icon='receipt-refund'
                    class="bg-purple-500! hover:bg-purple-400! text-white!">
                    Refrence#
                </flux:button>
            </td>
            @elseif ($order_state=='Purchased')
            <td>
                <flux:button wire:click="pProblem('{{$itemOrder->master_id}}', 'problem')" size='sm' icon='speaker-wave'
                    class="bg-amber-500! hover:bg-amber-400! text-white!">
                    P_Problem
                </flux:button>
            </td>
            @elseif ($order_state=='Paid')
            <td>
                <flux:button wire:click="openCheck('{{$itemOrder->master_id}}')" size='sm' icon='check-badge'
                    class=" bg-pink-500! hover:bg-pink-400! text-white!">
                    Check
                </flux:button>
            </td>
            <td>
                <flux:button wire:click="cProblem('{{$itemOrder->master_id}}', 'check')" size='sm' icon='speaker-wave'
                    class=" bg-violet-500! hover:bg-violet-400! text-white!">
                    C_Problem
                </flux:button>
            </td>
            @elseif ($order_state=='Checked' && $itemOrder->cargo_id !=null)
            <td>
                <flux:button as="a" href="{{ route('print', $itemOrder->ID) }}" size='sm' icon='printer'
                    class="bg-green-500! hover:bg-green-400! text-white!" onclick="return confirm('Are you sure?')"
                    wire:click="$refresh">
                    Print
                </flux:button>
            </td>
            @elseif($order_state=='C_Problem' || $order_state=='P_Problem')
            <td colspan="2">
                <flux:button class="bg-green-500! hover:bg-green-400! text-white!" icon='adjustments-horizontal'
                    size='sm' wire:click="adjustProblem({{ $itemOrder->sqrID }})">
                    Adjust
                </flux:button>
            </td>
            @endif
            @endif
        </tr>
        @if ($title !== 'NSOs')

        @if($purchaseDetailsNo == $itemOrder->master_id)
        @if($itemOrder->is_rmb_special == 'Y' && ($itemOrder->rmb_special_price == '' || $itemOrder->rmb_special_price
        == 0))
        <tr wire:key="details-warning-{{ $itemOrder->master_id }}">
            <td colspan="17">
                <flux:callout variant="danger" icon="x-circle" heading="Cannot purchase unless you set RMB Price." />
            </td>
        </tr>
        @else
        <tr wire:key="details-row-{{ $itemOrder->master_id }}">
            <td colspan="17" align="center">
                @include('partials.purchase-order-details')
            </td>
        </tr>
        @endif
        @endif

        @if ($editDetails)
        <tr wire:key="edit-details">
            <td colspan="17" align="center">
                @include('orders.editSupplyOrder')
            </td>
        </tr>
        @endif

        @if($chkDetailsNo == $itemOrder->master_id)
        <tr wire:key="check-details-{{ $itemOrder->master_id }}">
            <td colspan="17" align="center">
                @include('partials.check-order-details')
            </td>
        </tr>
        @endif
        </tr>
        @endif
        @endforeach
        <tr>
            <th colspan="5">Grand</th>
            <td><strong>{{ formatDecimal($volum/1000) }}</strong></td>
            <td><strong>{{ $Tweight }}</strong></td>
            <td align="left" valign="middle"><strong>{{ $totalQty }}</strong></td>
            <td></td>
            <td><strong>{{ $grandTotal }}</strong></td>
        </tr>
        @endif
    </tbody>
</table>