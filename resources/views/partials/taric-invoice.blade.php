<table class="table-nested">
    <thead class="">
        <tr class="table-highlighted">
            <th>Position</th>
            <th>Taric Name EN</th>
            <th>Taric Code</th>
            <th>Duty rate</th>
            <th>Total Qty</th>
            <th>Unit Price (€)</th>
            <th>Total Price (€)</th>
            <th>Operation</th>
        </tr>
    </thead>
    <tbody>
        @php
        $tp = 0;
        $tq = 0;
        $setValue = App\Models\Value::first();
        $myValue = $setValue->value;
        @endphp

        @foreach ($invoiceTarics as $item)
        @php
        $tq += $item->totalQty;
        $tp += $item->totalValue;
        @endphp

        <tr @if($item->set_taric_code !== null) class="!bg-green-200 !dark:bg-green-800" @endif>
            <td>{{ $loop->iteration }}</td>

            @if ($item->itemTaricId == 48)
            @php
            $fixedCode = $item->tariff ?? App\Models\Taric::where('id', $item->srqTaricID)->first();
            @endphp
            @if ($fixedCode)
            <td>{{ $fixedCode->name_en }}</td>
            <td class="!border !border-gray-300 !dark:border-gray-700 p-1 bg-yellow-200 !dark:bg-yellow-700">{{
                $fixedCode->code }}</td>
            @else
            <td class="!border !border-gray-300 !dark:border-gray-700 p-1 text-red-500">Tariff Name Missing</td>
            <td class="!border !border-gray-300 !dark:border-gray-700 p-1 bg-yellow-200 !dark:bg-yellow-700 text-red-500">
                Code Missing</td>
            @endif
            @else
            <td>{{ $item->name_en }}</td>
            <td>
                @if ($item->set_taric_code != $item->code)
                {{ $item->set_taric_code }} / {{ $item->code }}
                @else
                {{ $item->code }}
                @endif
            </td>
            @endif
            <td>{{ $item->duty_rate }}</td>
            <td>{{ $item->totalQty }}</td>
            <td>
                {{ $item->totalQty != 0 ? number_format($item->totalValue / $item->totalQty, 2) : '0.00' }}
            </td>

            <td>{{ number_format($item->totalValue, 2) }}</td>
            <td>
                @if ($item->totalValue < $myValue) <flux:button class="bg-blue-600! text-white! hover:bg-blue-500!"
                    size='sm' icon='cog-8-tooth'
                    wire:click="selectTotalPrice({{ $item->itemTaricId }}, {{ $item->setItemId }})">
                    Set taric
                    </flux:button>
                    @endif
            </td>
        </tr>

        @if ($pricedTaricId == $item->setItemId)
        <tr>
            <td colspan="7" align="left"> Current taric code is : {{ $item->code }}
                @include('partials.taric-code-setting', ['item' => $item])
            </td>
        </tr>
        @endif
        @endforeach

        <tr class="font-semibold bg-gray-50 !dark:bg-gray-800">
            <th colspan="4" class="border border-gray-300 !dark:border-gray-700 p-1 text-right">Grand Total</th>
            <th class="border border-gray-300 !dark:border-gray-700 p-1 text-right">{{ $tq }}</th>
            <th></th>
            <th class="border border-gray-300 !dark:border-gray-700 p-1 text-right">€{{ number_format($tp, 2) }}</th>
            <th></th>
        </tr>
    </tbody>
</table>