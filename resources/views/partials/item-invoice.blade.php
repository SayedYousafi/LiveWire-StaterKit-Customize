<div class="table-default overflow-y-auto">
    <table class="table-nested">
        <thead class="sticky top-0 z-10 bg-gray-100 dark:bg-gray-800">
            <tr class="table-highlighted">
                <th>ID</th>
                <th>EAN</th>
                <th>Item Name</th>
                <th>Taric code</th>
                <th>Remark</th>
                <th>Order_no</th>
                <th>SOID</th>
                <th>Status</th>
                <th>V(dm³)</th>
                <th>W(kg)</th>
                <th>QTY</th>
                <th>RMB</th>
                <th>EK</th>
            </tr>
        </thead>
        @php
        $totalQty = 0;
        $grandTotal = 0;
        //$volum = 0;
        $Tvolum = 0;
        //$weight = 0;
        $Tweight = 0;
        // Cache for tariff lookups (only used when code is '0000000000')
        $tariffCache = [];
        @endphp
        <tbody>
            @foreach ($invoiceItems as $item)
            @php

            // Gather all numeric quantities
            $allQuantities = collect([
            $item->qty_split,
            $item->qty,
            $item->qty_label,

            ])->filter(fn($q) => is_numeric($q) && $q > 0);

            // Get unique values for display
            $quantities = $allQuantities->unique()->values();
            $displayQty = $quantities->implode('/');

            // Get the first available numeric quantity for calculations
            $numericQty = $allQuantities->first() ?? 0;

            // Accumulate totals
            $totalQty += $numericQty;
            $grandTotal += $numericQty * $item->price_rmb;

            // Calculate volume and total weight
        if ($item->is_dimension_special == 'N') {

        $width = floatval($item->width ?? 0);
        $height = floatval($item->height ?? 0);
        $length = floatval($item->length ?? 0);
        $weight = floatval($item->weight ?? 0) * $numericQty;

        $volum = ($width * $height * $length) * $numericQty;
        $Tvolum += $volum;
        $Tweight += $weight;
        }
        else {

        $width = floatval($item->dwidth ?? 0);
        $height = floatval($item->dheight ?? 0);
        $length = floatval($item->dlength ?? 0);
        $dimqty = (float) ($item->dimqty ?? 0);
        // take care of zero or null dimqty
        $weight = $dimqty != 0 ? floatval($item->dweight ?? 0) * $numericQty / $dimqty : 0;
        $Tweight += $weight;

        $volum = ($dimqty != 0) ? ($width * $height * $length) * ($numericQty / $dimqty) : 0;
        $Tvolum += $volum;
        }
            @endphp

            <tr @if(str_contains($item->master_id, '-1')) class="bg-blue-100 dark:bg-blue-900" @else wire:key="{{
                $item->id }}" @endif>
                <td class=" space-x-1">
                    <flux:button size="sm" icon='code-bracket-square' class="bg-zinc-600! text-white! hover:bg-zinc-500"
                        wire:click="selectQtyDelivery('{{ $item->master_id }}')">QTY</flux:button>

                    <flux:button size="sm" icon='share' class="bg-yellow-600! text-white! hover:bg-yellow-500"
                        wire:confirm='Are you sure spliting this?' wire:click="splitDelivery('{{ $item->master_id }}')">
                        Split</flux:button>

                    <flux:button size="sm" icon='arrow-path' class="bg-indigo-600! text-white! hover:bg-indigo-500"
                        wire:click="reAssign('{{ $item->master_id }}')">ReAssign</flux:button>
                </td>
                {{-- <td>{{ $item->ean }}</td> --}}
                <td><a href="{{ route('itemEdit', $item->item_id) }}" target="_blank">{{ $item->ean }}</a></td>

                @if ($item->code === '0000000000')
                <td>{{ $item->item_name }}</td>
                @php
                if (isset($tariffCache[$item->srqTaricID])) {
                $fixedCode = $tariffCache[$item->srqTaricID];
                } else {
                $fixedCode = App\Models\Taric::find($item->srqTaricID, ['code', 'name_en']);
                $tariffCache[$item->srqTaricID] = $fixedCode;
                }
                @endphp
                <td class=" text-red-500">
                    {{ $item->code }}
                    @if ($fixedCode && $item->code != $fixedCode->code)
                    <br>
                    <span class="text-xs">Fixed code is:</span><br>
                    <span class="text-green-500">{{ $fixedCode->code }}</span>
                    @endif
                    <flux:button size="sm" variant='danger' class="float-right mt-1" icon="wrench-screwdriver"
                        wire:click="selectCode('{{ $item->master_id }}')">Fix</flux:button>
                </td>
                @else
                <td>{{ $item->item_name }}</td>
                <td>{{ $item->code }}</td>
                @endif

                <td>{{ $item->remark_de }} / {{ $item->remarks_cn }} / {{ $item->remark }}</td>
                <td>{{ $item->order_no }}</td>
                <td>{{ $item->supplier_order_id }}</td>
                <td>{{ $item->status }}</td>
                {{-- <td>{{ formatDecimal((($width * $height * $length) * $qty) / 1000) }}</td> --}}
                <td>{{ formatDecimal($volum/1000) }}</td>
                <td>{{ formatDecimal($weight) }}</td>
                <td>{{ $displayQty }} </td>

                @php $price = $item->is_rmb_special == 'Y' ? $item->rmb_special_price : $item->price_rmb; @endphp
                <td class=" {{ $item->is_rmb_special == 'Y' ? 'bg-yellow-300 dark:bg-yellow-700' : '' }}">
                    {{ $price }}@if($item->is_rmb_special == 'Y')<sup>*</sup>@endif
                </td>
                <td>
                    @if ($price == 0 && $item->is_rmb_special == 'Y')
                    <span class="text-xs text-red-500">Special item with zero RMB price</span>
                    @elseif ($item->is_eur_special == 'Y')
                    {{ $item->eur_special_price }}
                    @else
                    {{ EK_net($price, $item->cat_id) }}
                    @endif
                </td>

                @if ($item->is_eur_special == 'Y')
                <td>
                    <flux:button size="sm" icon="currency-euro" variant='danger'
                        wire:click='itemToSet({{ $item->master_id }})'>Set EUR Price</flux:button>
                </td>
                @endif
            </tr>

            @if ($changId == $item->master_id)
            <tr>
                <td colspan="14" class="text-center  bg-gray-50 dark:bg-gray-800">
                    <b>Set Special Taric Code for EAN: {{ $item->ean }}</b><br>
                    <span>Current Taric code in item is: {{ $item->code }}</span>
                    @include('partials.taric-code-fix')
                </td>
            </tr>
            @endif

            @if ($itemToSetId == $item->master_id)
            <div class=" mt-2 text-center mb-2">
                @if (session('success'))
                <flux:callout variant="success" icon="check-circle" heading="{{ session('success') }}" />
                @endif
            </div>
            <tr class="mt-3 mb-3">
                <td colspan="15" class="text-center  !bg-gray-50 dark:!bg-gray-800" align="center">
                    <b>SET EUR PRICE HERE</b>
                    <div class="my-2">
                        <flux:input type="number" wire:model="eur_special_price" label="EUR Special Price"/>

                    </div>
                    <div class="space-x-2">
                        <flux:button size="sm" icon='x-circle' wire:click="cancel">Cancel</flux:button>
                        <flux:button size="sm" icon="currency-euro"
                            class="!bg-green-600 !text-white hover:!bg-green-500" wire:click="eurSpecialPrice">Set Price
                        </flux:button>
                    </div>
                </td>
            </tr>
            @endif
            @endforeach
        </tbody>
        <tfoot class="bg-gray-100 dark:bg-gray-900 font-semibold">
            <tr>
                <td colspan="8">Grand</td>
                <td>{{ formatDecimal($Tvolum / 1000) }}</td>
                <td>{{ formatDecimal($Tweight) }}</td>
                <td>{{ $totalQty }}</td>
            </tr>
        </tfoot>
    </table>
</div>