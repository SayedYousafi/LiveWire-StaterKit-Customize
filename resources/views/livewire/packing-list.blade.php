<div>
    @if (session('success'))
    <div class="mt-2 text-center">
        <flux:callout variant="success" icon="check-circle" heading="{{ session('success') }}" />
    </div>
    @endif

    @if ($errors->has('pallet_sequence'))
    <div class="text-red-600 font-semibold mb-2">
        {{ $errors->first('pallet_sequence') }}
    </div>
    @endif
    <div class="flex justify-center mb-3">
        <x-sub-menu :current="$title" />
    </div>

    <table class="w-full border border-gray-300">
        <thead>
            <tr>
                <th></th>
                <th>Description</th>
                <th>Qty</th>
                <th>Client</th>
                <th>Package</th>
                <th>P. Type</th>
                <th>Weight (kg)</th>
                <th>Length (cm)</th>
                <th>Width (cm)</th>
                <th>Height (cm)</th>
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
                        placeholder="Item Description" size="70" />
                </td>
                <td>
                    <flux:input readonly wire:model="packingLists.{{ $index }}.itemQty" placeholder="Qty"
                        class="!w-20" />
                </td>
                <td>
                    <flux:select wire:model="packingLists.{{ $index }}.client1" class="!w-auto">
                        <flux:select.option value="">-- Select --</flux:select.option>
                        <flux:select.option>GTECH-GT</flux:select.option>
                        <flux:select.option>K011111</flux:select.option>
                        <flux:select.option>K022222</flux:select.option>
                    </flux:select>
                </td>
                <td>
                    <flux:select wire:model="packingLists.{{ $index }}.pallet" class="!w-auto">
                        <flux:select.option value="">Select</flux:select.option>
                        @for ($i = 1; $i <= 20; $i++) <flux:select.option>P{{ $i }}</flux:select.option>
                            @endfor
                    </flux:select>
                </td>
                <td>
                    <flux:select wire:model="packingLists.{{ $index }}.ptype" class="!w-auto">
                        <flux:select.option value="">-- Select --</flux:select.option>
                        <flux:select.option>Tray</flux:select.option>
                        <flux:select.option>Wooden Crate</flux:select.option>
                        <flux:select.option>Carton Parcel</flux:select.option>
                    </flux:select>
                </td>
                <td>
                    <flux:input type="number" wire:model="packingLists.{{ $index }}.weight" placeholder="W"
                        class="!w-20" />
                </td>
                <td>
                    <flux:input type="number" wire:model="packingLists.{{ $index }}.length" placeholder="L"
                        class="!w-20" />
                </td>
                <td>
                    <flux:input type="number" wire:model="packingLists.{{ $index }}.width" placeholder="B"
                        class="!w-20" />
                </td>
                <td>
                    <flux:input type="number" wire:model="packingLists.{{ $index }}.height" placeholder="H"
                        class="!w-20" />
                </td>

            </tr>
            @endforeach

            <tr>
                <td colspan="10" align="center">
                    <flux:button class="mt-3 mb-3" variant='primary' icon='plus-circle' wire:click="save">
                        Save Paking List
                    </flux:button>
                </td>
            </tr>
        </tbody>
    </table>

    <div class="mt-4 mb-4 content-center">
        <table class="table-default">
            <thead>
                <tr>
                    <th>No. </th>
                    <th>Cargo No.</th>
                    <th>Invoice No.</th>
                    <th>Date Created</th>
                    <th>Count Items</th>
                    <th colspan="2">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($packs as $pack)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $pack->cargo_no }}</td>
                    <td>{{ $pack->invoice_no }}</td>
                    <td>{{ formatGeneralDate($pack->created_at) }}</td>
                    <td>{{ $pack->CountList }}</td>
                    <td>
                        <flux:button wire:click="getPack({{ $pack->cargo_id }})" icon='pencil-square' variant='primary'>
                            Edit </flux:button>
                    </td>
                    <td>
                        <flux:button
                            href="{{ route('packList', ['id' => $pack->cargo_id, 'name' => $pack->invoice_no]) }}"
                            target="_blank">
                            <img src="{{ asset('img/icon_download_PDF.svg') }}" alt="MySVG" class="w-10 h-10">
                        </flux:button>
                    </td>

                </tr>
                @if ($packToEdit == $pack->cargo_id )
                <tr>
                    <td colspan="8">
                        <livewire:edit-packing-list :packingId="$pack->cargo_id"
                            wire:key="edit-packing-list-{{ $pack->cargo_id }}" />
                    </td>
                </tr>
                @endif

                @endforeach
            </tbody>
        </table>
    </div>

</div>