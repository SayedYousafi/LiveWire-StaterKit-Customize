<div>

    @if (session('success'))
    <div class="mt-2 text-center">
        <flux:callout variant="success" icon="check-circle" heading="{{ session('success') }}" />
    </div>
    @endif

    @if (request()->route('packingId'))
    <table class="w-full border border-gray-300">
        <thead>
            <tr>
                <th></th>
                <th>Description</th>
                <th>Qty</th>
                <th>Client</th>
                <th>Pallet</th>
                <th>G.W.(KG)</th>
                <th>L</th>
                <th>B</th>
                <th>H</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($packingLists as $index => $list)
            <tr>
                <td>
                    <button type="button" wire:click="duplicateRow({{ $index }})"
                        class="bg-green-600 p-1 hover:bg-green-500 rounded text-white">
                        +
                    </button>
                </td>
                <td>
                    <flux:input readonly wire:model="packingLists.{{ $index }}.itemDescription"
                        placeholder="Item Description" />
                </td>
                <td>
                    <flux:input readonly wire:model="packingLists.{{ $index }}.itemQty" placeholder="Qty"
                        class="w-20" />
                </td>
                <td>
                    <flux:select wire:model="packingLists.{{ $index }}.client1">
                        <flux:select.option value="">-- Select --</flux:select.option>
                        <flux:select.option>GTECH-GT</flux:select.option>
                        <flux:select.option>K011111</flux:select.option>
                        <flux:select.option>K022222</flux:select.option>
                    </flux:select>
                </td>
                <td>
                    <flux:select wire:model="packingLists.{{ $index }}.pallet">
                        <flux:select.option value="">-- Select --</flux:select.option>
                        @for ($i = 1; $i <= 20; $i++) <flux:select.option>P{{ $i }}</flux:select.option>
                            @endfor
                    </flux:select>
                </td>
                <td>
                    <flux:input type="number" wire:model="packingLists.{{ $index }}.weight" placeholder="Weight" />
                </td>
                <td>
                    <flux:input type="number" wire:model="packingLists.{{ $index }}.length" placeholder="L" />
                </td>
                <td>
                    <flux:input type="number" wire:model="packingLists.{{ $index }}.width" placeholder="B" />
                </td>
                <td>
                    <flux:input type="number" wire:model="packingLists.{{ $index }}.height" placeholder="H" />
                </td>
            </tr>
            @endforeach

            <tr>
                <td colspan="10" align="center">
                    <flux:button variant='primary' icon='plus-circle' wire:click="save">Save Paking List</flux:button>
                </td>
            </tr>
            @else
             <flux:button size='sm' href="{{ route('invoicesClosed') }}"> Create packing list from closed invoices</flux:button>
            @endif

        </tbody>
    </table>

    <div class="mt-4 mb-4 content-center">
        <table class="table-default">
            <thead>
                <tr>
                    <th>No. </th>
                    <th>Date Create</th>
                    <th>Count Items</th>
                    <th colspan="2">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($packs as $pack)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $pack->created_at }}</td>
                    <td>{{ $pack->CountList }}</td>
                    <td> <a href="{{ route('packList', $pack->created_at)  }}"> Show packlist</a></td>
                    <td>
                        <flux:button href="{{ route('packList', $pack->created_at)  }}">
                        <img src="{{ asset('img/icon_download_PDF.svg') }}" alt="MySVG" class="w-10 h-10">
                    </flux:button>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>