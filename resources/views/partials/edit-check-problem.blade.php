<flux:modal name="edit-check-problem" class="md:w-96">
    <div class="space-y-6">
        <div>
            <flux:heading size="lg">Recording Check Problem</flux:heading>
            {{-- <flux:text class="mt-2">Make changes to QTY.</flux:text> --}}
        </div>

        {{-- <flux:input wire:model='currentQty' label="New QTY" placeholder="Enter New QTY" /> --}}
        <flux:textarea wire:model='txtProblem' rows="3" label='About Check Problem:' autofocus
        placeholder="Short description of check problem" />

        <div class="flex">
            <flux:spacer />

            <flux:button wire:click='updateStatus' type="submit" variant="primary">Save check problem</flux:button>
        </div>
    </div>
</flux:modal>