<flux:modal name="holidayModal" class="w-full">

    <div class="space-y-6">
        <flux:heading size="lg">Holiday Details</flux:heading>
        @if ($isUpdate)
        <div class="flex items-center gap-10 mt-2.5">
            <flux:switch wire:click="$toggle('enableEdit')" label="Enable edit" />
        </div>
        @endif
        <div class="grid grid-cols-4 gap-4">
            <div class="col-span-2">
                <flux:select size='sm' wire:model="country" label="Country" placeholder="Choose Country...">
                    <flux:select.option value=''>Select the country</flux:select.option>
                    <flux:select.option>Germany</flux:select.option>
                    <flux:select.option>Cyprus</flux:select.option>
                    <flux:select.option>China</flux:select.option>
                </flux:select>
            </div>
            <div class="col-span-2">
                <flux:input size="sm" wire:model="name" label="Holiday name" placeholder="holiday name" class="w-full"
                    :disabled="$isUpdate && !$enableEdit" />
            </div>
            <div class="col-span-2">
                <flux:input size="sm" wire:model="date" type="date" label="Date" placeholder="Date" class="w-full"
                    :disabled="$isUpdate && !$enableEdit" />
            </div>
            <div class="col-span-2">
                <flux:input size="sm" wire:model="day" label="Day name" placeholder="week day name" class="w-full"
                    :disabled="$isUpdate && !$enableEdit || true"/>
            </div>

            <div class="col-span-2">
                <flux:input size="sm" wire:model="type" label="Holiday Type" placeholder="Ex. public holiday"
                    class="w-full" :disabled="$isUpdate && !$enableEdit" />
            </div>
            <div class="col-span-2">
                <flux:input size="sm" wire:model="comments" label="Comments" placeholder="Comments" class="w-full"
                    :disabled="$isUpdate && !$enableEdit" />
            </div>

        </div>

        <div class="flex">
            <flux:spacer />
            <flux:button type="button" variant="ghost" icon="x-circle" wire:click="cancel"
                x-on:click="Flux.modal('myModal').close()">Cancel
            </flux:button>
            <flux:button type="submit" wire:click="{{ $isUpdate ? 'update' : 'save' }}" icon="plus-circle"
                variant="primary">
                {{ $isUpdate ? 'Save changes' : 'Save' }}
            </flux:button>
        </div>
    </div>
</flux:modal>