<flux:modal name="working-profile" class="md:w-96">
    <div class="space-y-6">
        <div>
            <flux:heading size="lg">Working profile settings</flux:heading>
            <flux:text class="mt-2">Make changes to your personal details.</flux:text>
        </div>
        <flux:input wire:model='name' label="Profile Name:" placeholder="Working profile name" />
        <flux:input wire:model='entitlement' label="Leave Entitlement:" placeholder="Annual leave entitlement" />

        <flux:checkbox.group wire:model="working_days" label='Working Days'>
            <flux:checkbox value="Monday" label='Monday' />
            <flux:checkbox value="Tuesday" label='Tuesday' />
            <flux:checkbox value="Wednesday" label='Wednesday' />
            <flux:checkbox value="Thursday" label='Thursday' />
            <flux:checkbox value="Friday" label='Friday' />
            <flux:checkbox value="Saturday" label='Saturday' />
            <flux:checkbox value="Sunday" label='Sunday' />
        </flux:checkbox.group>
        <div>
            <flux:select size='sm' wire:model="public_holiday" label="Public Holiday" placeholder="Choose public holiday...">
                <flux:select.option>Germany</flux:select.option>
                <flux:select.option>Cyprus</flux:select.option>
                <flux:select.option>China</flux:select.option>
            </flux:select>
        </div>

        <div class="flex">
            <flux:spacer />
            @isset($editId)
            <flux:button wire:click='update' icon='plus-circle' type="submit" variant="danger">Save changes
            </flux:button>

            @else
            <flux:button wire:click='save' icon='plus-circle' type="submit" variant="primary">Save changes</flux:button>
            @endisset
        </div>
    </div>
</flux:modal>