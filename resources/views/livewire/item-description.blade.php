<div class="container card" x-data="{ rows: @entangle('rows').defer }">
    <div class="card-header text-center">
        @if(session('success'))
        <flux:callout class="success" heading ="{{ session('success') }}"/>
        @endif
        <div class="flex justify-between">
            <flux:button size='sm' onclick="history.back()" class=" bg-blue-600! text-white! hove:bg-blue-500!">Back
            </flux:button>
            <flux:heading size='lg'>Short Description Template items of {{ $pItems->de_no }}, {{ $pItems->name_en }}
            </flux:heading>
            <flux:button size="sm" wire:click="exportCsv" variant="danger">Export to CSV</flux:button>
        </div>
    </div>
    <div class="card-body">

        <form wire:submit.prevent="saveRows">
            <table class="table-default mt-3">
                <thead>
                    <tr>
                        <th class="w-25 p-3">Type</th>
                        <th>English values</th>
                        <th>German values</th>
                    </tr>
                </thead>
                @foreach ($rows as $index => $row)
                <tr>
                    <td class="w-25 p-3">
                        <select wire:model.live="rows.{{ $index }}.type" class="h-10"
                            wire:change="checkLastRow({{ $index }})">
                            <!-- Trigger row addition on change -->
                            <option value="">Select an option from below</option>
                            <option value="1">TEXT</option>
                            <option value="2">Text with Inquiry</option>
                            <option value="3">AnchorSTP by ItemNameDE</option>
                            <option value="6">AnchorSTP by Model</option>
                            <option value="4">AnchorPDF</option>
                            <option value="5">AnchorPDFred</option>
                        </select>
                    </td>
                    <td>
                        @if (!empty($row['type']) && (int)$row['type'] !== 0)
                        <input type="text" class="w-full h-10  !border-gray-200 rounded-2xl"
                            wire:model.live="rows.{{ $index }}.value"
                            :disabled="['3', '4', '6'].includes(String(@js($row['type'])))">

                        @endif
                    </td>
                    <td>
                        @if (!empty($row['type']) && (int)$row['type'] !== 0)
                        <input type="text" class="w-full h-10 !border-gray-200 rounded-2xl"
                            wire:model.live="rows.{{ $index }}.value2"
                            :disabled="['3', '4', '6'].includes(String(@js($row['type'])))">
                        @endif
                    </td>
                </tr>
                @endforeach
            </table>
            <div class="text-center mt-2.5">
                <flux:button type="submit" icon='plus-circle' variant="primary">Save</flux:button>
            </div>

        </form>
        <table class="table-default mt-4">
            <tr class="table-highlighted">
                <th>ID</th>
                <th>EAN</th>
                <th>Display on Website</th>
                <th>Short Description EN</th>
                <th>Short Description DE</th>
            </tr>
            @php
            $groupedItems = collect($shortDescs)->groupBy('item_id');
            @endphp

            @foreach ($groupedItems as $item_id => $items)
            @php
            $item_name = $items->first()->item_name;
            $ean = $items->first()->ean;

            $fullText = collect($items)
            ->filter(fn($item) => !($item->type == 6 && empty($item->model)))
            ->map(fn($item) => match($item->type) {
            1 => "<p>{$item->value}</p>",
            2 => "<p>{$item->value} <strong><a
                        href='mailto:info@gtech.de?subject=Inquiry {$item->parent_name}'>{$item->parent_name}
                        Inquiry</a></strong></p>",
            3 => "<p><a href='https://data.gtech-shop.de/CAD/" . str_replace(' ', ' _', $item->item_name) . ".stp'><img
                        src='https://data.gtech-shop.de/data/Icons/STP_Icon.jpg' /> <strong>Download {$item->value}
                        &quot;{$item->item_name}.stp&quot;</strong></a></p>",
            4 => "<p><a href='https://data.gtech-shop.de/Datasheets/" . str_replace(' ', ' _', $item->parent_name) .
                    ".pdf' target='_blank'><img src='https://data.gtech-shop.de/data/Icons/Datenblatt_GT_Icon.jpg' />
                    <strong>Download {$item->value} {$item->parent_name}.pdf</strong></a></p>",
            5 => "<p><a href='https://data.gtech-shop.de/Datasheets/{$item->value}.pdf' target='_blank'><img
                        src='https://data.gtech-shop.de/data/Icons/Datenblatt_Norm_Icon.jpg' /> <strong>Download
                        {$item->value}.pdf</strong></a></p>",
            6 => "<p><a href='https://data.gtech-shop.de/CAD/{$item->model}.stp'><img alt=''
                        src='https://data.gtech-shop.de/data/Icons/STP_Icon.jpg' /> <strong>Download
                        &quot;{$item->model}.stp&quot;</strong></a> <a
                    href='mailto:info@gtech.de?subject=Inquiry {$item->parent_name}'><strong>Inquire other
                        {$item->parent_name} CAD files</strong></a></p>",
            })->implode('');

            $fullText2 = collect($items)
            ->filter(fn($item) => !($item->type == 6 && empty($item->model)))
            ->map(fn($item) => match($item->type) {
            1 => "<p>{$item->value2}</p>",
            2 => "<p>{$item->value2} <strong><a
                        href='mailto:info@gtech.de?subject=Inquiry {$item->parent_name}'>{$item->parent_name}
                        Inquiry</a></strong></p>",
            3 => "<p><a href='https://data.gtech-shop.de/CAD/" . str_replace(' ', ' _', $item->item_name) . ".stp'><img
                        src='https://data.gtech-shop.de/data/Icons/STP_Icon.jpg' /> <strong>Download {$item->value2}
                        &quot;{$item->item_name}.stp&quot;</strong></a></p>",
            4 => "<p><a href='https://data.gtech-shop.de/Datasheets/" . str_replace(' ', ' _', $item->parent_name) .
                    ".pdf' target='_blank'><img src='https://data.gtech-shop.de/data/Icons/Datenblatt_GT_Icon.jpg' />
                    <strong>Download {$item->value2} {$item->parent_name}.pdf</strong></a></p>",
            5 => "<p><a href='https://data.gtech-shop.de/Datasheets/{$item->value2}.pdf' target='_blank'><img
                        src='https://data.gtech-shop.de/data/Icons/Datenblatt_Norm_Icon.jpg' /> <strong>Download
                        {$item->value2}.pdf</strong></a></p>",
            6 => "<p><a href='https://data.gtech-shop.de/CAD/{$item->model}.stp'><img alt=''
                        src='https://data.gtech-shop.de/data/Icons/STP_Icon.jpg' /> <strong>Download
                        &quot;{$item->model}.stp&quot;</strong></a> <a
                    href='mailto:info@gtech.de?subject=Inquiry {$item->parent_name}'><strong>Inquire other
                        {$item->parent_name} CAD files</strong></a></p>",
            })->implode('');

            @endphp
            <tr>
                <td valign='middle'>{{ $item_id }}</td>
                <td valign='middle'>{{ $ean }}</td>
                <td>{!! $fullText !!}</td>
                <td>{{ $fullText }}</td>
                <td>{{ $fullText2 }}</td>
            </tr>
            @endforeach

            @if ($groupedItems->isEmpty())
            <tr>
                <td colspan="4" class="text-center text-danger fw-bolder">No description found for parent id:
                    {{$parent_id }}
                    Click back or <a href="{{ route('parents') }}"> HERE </a> to choose/change parent
                </td>
            </tr>
            @endif
        </table>
    </div>
</div>