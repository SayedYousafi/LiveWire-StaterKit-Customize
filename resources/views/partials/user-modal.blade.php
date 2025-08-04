<flux:modal name="user-modal" class="md:w-96">
    <div class="space-y-6">
        <div>
            <flux:heading size="lg">Update {{ $name }} role or work profile</flux:heading>
            <flux:text class="mt-2">Make changes to {{ $name }} profile details.</flux:text>
        </div>

        <flux:select wire:model='role' label="User role" placeholder="user role" size='sm'>
                <flux:select.option value="admin">Admin</flux:select.option>
                <flux:select.option value="user">User</flux:select.option>
        </flux:select>

        <flux:select wire:model='work_profile' label="Work profile" placeholder="user work profile" size='sm'>
            @foreach ($this->workProfiles as $profile )
                <flux:select.option value="{{ $profile->id }}">{{ $profile->name }}</flux:select.option>
            @endforeach
            
        </flux:select>

        
        <div class="flex">
            <flux:spacer />

            <flux:button wire:click='update' type="submit" variant="primary">Save changes</flux:button>
        </div>
    </div>
</flux:modal>