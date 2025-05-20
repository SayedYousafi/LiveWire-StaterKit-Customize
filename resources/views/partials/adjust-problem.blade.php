<flux:modal name="adjust-problem" class="md:w-96">
    <div class="space-y-6">
        <div>
            <flux:heading size="lg">Adjusting the Problem</flux:heading>
            {{-- <flux:text class="mt-2">Make changes to QTY.</flux:text> --}}
        </div>
        <div>
            <input type="hidden" wire:model="newStatus" />
            <p class="mt-2 text-sm">Current problem status is: {{ $problemType }}</p>
            <flux:dropdown>
                <flux:button icon:trailing="chevron-down">
                    {{ $newStatus ? "Selected status: $newStatus" : 'Select status' }}
                </flux:button>

                <flux:menu>
                    <flux:menu.item wire:click="$set('newStatus', 'SO')">Supply order</flux:menu.item>
                    <flux:menu.item wire:click="$set('newStatus', 'NSO')">Not supply order</flux:menu.item>

                    @if ($problemType !== 'P_Problem')
                    <flux:menu.item wire:click="$set('newStatus', 'Paid')">Paid</flux:menu.item>
                    @endif
                </flux:menu>
            </flux:dropdown>


        </div>
        <flux:textarea wire:model='remark' rows="3" label='Adjustment remark:' autofocus
            placeholder="Short description of adjustment" />
        <flux:input wire:model='qty' label="New QTY" placeholder="Enter New QTY" />
        <flux:textarea wire:model='remarks_cn' rows="3" label='Lable/CN Remark:' placeholder="cn remarks" />

        <div class="flex">
            <flux:spacer />

            <flux:button wire:click='editProblem' type="submit" variant="primary">Adjust Problem</flux:button>
        </div>
    </div>
</flux:modal>