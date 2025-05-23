<div class="col-md-3 mb-3 text-center">
    <flux:dropdown>
        <flux:button icon:trailing="chevron-down">
            {{ $special_code ? "Selected: $special_code" : 'Select a code from below' }}
        </flux:button>
        <flux:menu>
            @php
                $items = App\Models\Taric::all();
            @endphp

            <flux:menu.item wire:click="$set('special_code', '')">
                Select a code from below
            </flux:menu.item>

            @foreach ($items as $item)
                <flux:menu.item wire:click="$set('special_code', {{ $item->id }})">
                    {{ $item->id }} - {{ $item->code }} - {{ $item->name_en }}
                </flux:menu.item>
            @endforeach
        </flux:menu>
    </flux:dropdown>

    @error('special_code')
        <flux:text class="text-red-600 text-sm mt-1 block">{{ $message }}</flux:text>
    @enderror

    <div class="flex justify-center gap-4 mt-4">
        <flux:button icon="check-circle" size='sm'
        class="bg-green-600! text-white! hover:bg-green-500!"
        wire:click="changeCode">Save</flux:button>
        <flux:button size='sm' icon="x-circle" wire:click="cancel">Cancel</flux:button>
    </div>
</div>
