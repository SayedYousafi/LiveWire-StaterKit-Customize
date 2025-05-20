<flux:modal name="edit-problem" class="md:w-96">
    <div class="space-y-6">
        <div>
            <flux:heading size="lg">Update Problem</flux:heading>
            {{-- <flux:text class="mt-2">Make changes to QTY.</flux:text> --}}
        </div>

        {{-- <flux:input wire:model='currentQty' label="New QTY" placeholder="Enter New QTY" /> --}}
        <flux:textarea wire:model='txtProblem' rows="3" label='What is the problem:'
         placeholder="Short description of the problem" autofocus/>

        <div class="flex">
            <flux:spacer />

            <flux:button wire:click='updateProblem' type="submit" variant="primary">Save purchase problem</flux:button>
        </div>
    </div>
</flux:modal>