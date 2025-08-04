<div>
    <table class="w-full border border-gray-300">
        <thead>
            <tr>
                
                <th>Goods Description</th>
               
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
                    <flux:input readonly wire:model="packingLists.{{ $index }}.itemDescription"
                        placeholder="Item Description as per taric" size="70"/>
                </td>
                
                <td>
                    <flux:input readonly wire:model="packingLists.{{ $index }}.itemQty" placeholder="Qty"
                        class="!w-16" />
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
                    <flux:select wire:model="packingLists.{{ $index }}.pallet" class="!w-20">
                        <flux:select.option value="">-- Select --</flux:select.option>
                        @for ($i = 1; $i <= 20; $i++) <flux:select.option>P{{ $i }}</flux:select.option>
                            @endfor
                    </flux:select>
                </td>
                <td>
                    <flux:select wire:model="packingLists.{{ $index }}.ptype" class="!w-auto">
                        <flux:select.option value="">-- Select --</flux:select.option>
                        <flux:select.option selected>Tray</flux:select.option>
                        <flux:select.option>Wooden Crate</flux:select.option>
                        <flux:select.option>Carton Parcel</flux:select.option>
                    </flux:select>
                </td>
                <td>
                    <flux:input type="number" step="1" wire:model="packingLists.{{ $index }}.weight" placeholder="W" class="!w-20"/>
                </td>
                <td>
                    <flux:input type="number" step="1" wire:model="packingLists.{{ $index }}.length" placeholder="L" class="!w-20"/>
                </td>
                <td>
                    <flux:input type="number" step="1" wire:model="packingLists.{{ $index }}.width" placeholder="B" class="!w-20"/>
                </td>
                <td>
                    <flux:input type="number" step="1" wire:model="packingLists.{{ $index }}.height" placeholder="H" class="!w-20"/>
                </td>
            </tr>
            @endforeach
            <tr>
                <td colspan="10" align="center">
                    <flux:button 
                    class="mt-2 mb-2"
                    variant='danger' icon='pencil' wire:click="updateList">Update Paking List</flux:button>
                </td>
            </tr>
        </tbody>
    </table>
</div>