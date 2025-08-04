<flux:modal name="edit-qty" class="md:w-96">
    <div class="space-y-6">
        <div>
            <flux:heading size="lg">Update QTY</flux:heading>
            {{-- <flux:text class="mt-2">Make changes to QTY.</flux:text> --}}
        </div>

        <flux:input wire:model='currentQty' label="New QTY:" placeholder="Enter New QTY" autofocus/>
        <flux:textarea wire:model='remarks_cn' rows="3" label="Enter Remarks" />

        <div class="flex">
            <flux:spacer />
            <flux:button wire:click='updateQty' type="submit" variant="primary">Save QTY changes</flux:button>
        </div>
    </div>
</flux:modal>