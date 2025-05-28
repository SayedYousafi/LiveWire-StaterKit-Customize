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
        $volum = 0;
        $Tweight = 0;
        // Cache for tariff lookups (only used when code is '0000000000')
        $tariffCache = [];
        @endphp
        <tbody>
            @foreach ($invoiceItems as $item)
            @php
            // Gather all numeric quantities
            $allQuantities = collect([
            is_numeric($item->qty_split) ? $item->qty_split : null,
            is_numeric($item->qty) ? $item->qty : null,
            is_numeric($item->qty_label) ? $item->qty_label : null,
            ])->reject(fn($q) => $q === null);

            // Get unique values for display
            $quantities = $allQuantities->unique()->values();
            $displayQty = $quantities->implode('/');

            // Get the first available numeric quantity for math
            $numericQty = $allQuantities->first() ?? 0;

            // Accumulate totals
            $totalQty += $numericQty;
            $grandTotal += $numericQty * $item->price_rmb;

            $width = $item->width;
            $height = $item->height;
            $length = $item->length;
            $weight = $item->weight * $numericQty;

            $volum += ($width * $height * $length) * $numericQty;
            $Tweight += $weight;
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
                <td>{{ $item->ean }}</td>

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
                <td>{{ formatDecimal((($width * $height * $length) * $qty) / 1000) }}</td>
                <td>{{ $weight }}</td>
                <td>{{ $displayQty }}</td>
                
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
                <td colspan="15" class="text-center  bg-gray-50 dark:bg-gray-800">
                    <b>SET EUR PRICE HERE</b>
                    <div class="my-2">
                        <label for="eur_special_price">EUR Special Price:</label>
                        <input type="number" wire:model="eur_special_price"
                            class="border rounded  dark:bg-gray-700 dark:border-gray-600">
                        @error('eur_special_price')
                        <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="space-x-2">
                        <flux:button size="sm" icon='x-circle' wire:click="cancel">Cancel</flux:button>
                        <flux:button size="sm" icon="currency-euro"
                            class="bg-green-600! text-white! hover:bg-green-500!" wire:click="eurSpecialPrice">Set Price
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
                <td>{{ formatDecimal($volum / 1000) }}</td>
                <td>{{ $Tweight }}</td>
                <td>{{ $totalQty }}</td>
            </tr>
        </tfoot>
    </table>
</div>