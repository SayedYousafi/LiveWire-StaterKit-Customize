<flux:modal name="leaveRejectModal" class="md:w-96">
    <div class="space-y-6">
        <div>
            <flux:heading size="lg">Rejecting leave request</flux:heading>
            <flux:text class="mt-2">Please provide a rejection reason</flux:text>
        </div>

        <flux:input wire:model='remarks' label="Reason" placeholder="Short reason for rejections:" />

        <div class="flex">
            <flux:spacer />

            <flux:button wire:click='rejected' variant="danger" icon='light-bulb'>Reject Leave</flux:button>
        </div>
    </div>
</flux:modal>