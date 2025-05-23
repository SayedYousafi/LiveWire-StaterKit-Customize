<div class="mt-2 mb-3">
    <flux:heading>Select new taric code</flux:heading>

    <flux:dropdown>
        <flux:button icon:trailing="chevron-down">
            {{ $special_code ? "Taric Code: $special_code" : 'Select Taric Code' }}
        </flux:button>
        <flux:menu>
            @php
                $items = App\Models\Taric::all();
            @endphp

            @foreach ($items as $item)
                <flux:menu.item wire:click="$set('special_code', '{{ $item->code }}')">
                    {{ $item->id }} - {{ $item->code }} - {{ $item->name_en }}
                </flux:menu.item>
            @endforeach
        </flux:menu>
    </flux:dropdown>

    @error('special_code')
        <flux:text class="text-red-600 text-sm mt-1 block">{{ $message }}</flux:text>
    @enderror

    <div class="flex justify-center gap-4 mt-4">
        
        <flux:button size='sm' icon="x-circle" wire:click="cancel">Cancel</flux:button>
        <flux:button icon="check-circle" size='sm' class="bg-green-600! text-white! hover:bg-green-500!" 
        wire:click='setTarifCode'>Save</flux:button>
    </div>
</div>
