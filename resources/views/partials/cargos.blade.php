<flux:modal name="myModal" class="md:w-96">
    <div class="space-y-6">
        <div>
            <flux:heading size="lg">Update cargo</flux:heading>
            {{-- <flux:text class="mt-2">Assigning cargo to Order No.  {{ $orderNo }}</flux:text>--}}
        </div>
        <flux:dropdown>
            <flux:button icon:trailing="chevron-down">Select a cargo {{ $cargoId }}</flux:button>
            <flux:menu>
                @foreach ($cargos as $id => $cargo_no)
                <flux:separator />
                <flux:menu.item icon='plus' wire:click="$set('cargoId', {{ $id }})">{{ $id }} - {{ $cargo_no }}
                </flux:menu.item>
                @endforeach
            </flux:menu>
            
        </flux:dropdown>
            <flux:spacer />
             <flux:text class="sm">New selected cargo: {{ $cargoId }}</flux:text>
        <div class="flex">

            <flux:spacer />
            <flux:button wire:click='changeCargo' size='sm' icon='archive-box'
            type="submit" variant="primary">Save changes</flux:button>
        </div>
    </div>
</flux:modal>