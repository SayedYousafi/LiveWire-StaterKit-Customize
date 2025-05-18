<flux:modal name="edit-refNo" class="md:w-96">
    <div class="space-y-6">
        <div>
            <flux:heading size="lg">Update Ref #</flux:heading>
            {{-- <flux:text class="mt-2">Make changes to QTY.</flux:text> --}}
        </div>

        <flux:input wire:model='ref_no' label="New Reference #" placeholder="Enter Ref No." />
        <flux:textarea wire:model='remark' rows="3" label="Supplier order remark:" placeholder="is there any supplier order remark?" />

        <div class="flex">
            <flux:spacer />

            <flux:button wire:click='updateRefNo' type="submit" variant="primary">Save Reference #</flux:button>
        </div>
    </div>
</flux:modal>