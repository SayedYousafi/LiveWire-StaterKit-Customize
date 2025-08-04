<flux:modal name="user-password" class="md:w-96">
    <div class="space-y-6">
        <div>
            <flux:heading size="lg">Change {{ $name }} password.</flux:heading>
            <flux:text class="mt-2">Update {{ $name }} password.</flux:text>
        </div>
        <flux:input type="password" label="New Password" wire:model.defer='password'/>
        <flux:input type="password" label="Confirm Password" wire:model.defer='password_confirmation'/>
  
        <div class="flex">
            <flux:spacer />

            <flux:button wire:click='changePassword' type="submit" icon='arrow-path-rounded-square' variant="primary">Change Password</flux:button>
        </div>
    </div>
</flux:modal>