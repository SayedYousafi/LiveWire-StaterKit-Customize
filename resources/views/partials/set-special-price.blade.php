<flux:modal name="edit-price" class="md:w-96">
    <div class="space-y-6">
        <div>
            <flux:heading size="lg">Update Special Price</flux:heading>
            {{-- <flux:text class="mt-2">Make changes to your personal details.</flux:text> --}}
        </div>

        <flux:input wire:model="rmb_special_price" label="RMB Special Price" placeholder="new rmb price" />

        <flux:textarea label="Remark" />

        <div class="flex">
            <flux:spacer />
            <flux:button icon='x-circle' type="submit" wire:click='cancel'>Cancel</flux:button>
            <flux:button icon='plus-circle' type="submit" 
            wire:click='setSpecialPrice' variant='primary'>Save changes</flux:button>
        </div>
    </div>
</flux:modal>