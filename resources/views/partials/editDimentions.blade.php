<flux:modal name="itemDimentions" class="md:w-96">
    <div class="space-y-6">
        <div>
            <flux:heading size="lg">Item Dimnsions details<br>

                <flux:text></flux:text>
            </flux:heading>
            <flux:text class="mt-2">
                @isset($name) {{ $name }}@endisset

            </flux:text>
            <flux:heading size="lg">@isset($ean)EAN : {{$ean}}@endisset</flux:heading>
        </div>
        <flux:spacer />
        <div class="grid grid-cols-2 gap-4">
            @isset( $item_id)
                <input type="hidden" value='{{ $item_id }}'>
            @endisset
            
            <flux:input label="Weight" placeholder="Enter weight" wire:model="weight" />
            <flux:input label="Length" placeholder="Enter length" wire:model="length" />
            <flux:input label="Width" placeholder="Enter width" wire:model="width" />
            <flux:input label="Height" placeholder="Enter height" wire:model="height" />
            @isset($qty)
            <flux:input label="QTY" placeholder="Enter QTY" wire:model="qty" />
            @endisset
        </div>
        <div class="flex">
            <flux:spacer />
            <flux:button icon='x-circle' type="submit" wire:click='cancel'>Cancel</flux:button>
            <flux:button icon='plus-circle' type="submit" wire:click="update" variant="primary">Save changes
            </flux:button>
        </div>
    </div>
</flux:modal>