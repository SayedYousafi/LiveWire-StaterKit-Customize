<div class="container mx-auto">
    <div class="flex justify-between mt-3">
        <div>
            <flux:modal.trigger name="myModal">
                <flux:button wire:click='cancel' icon='plus-circle' class="bg-blue-800! text-white! hover:bg-blue-700!">
                    New {{ Str::before($title,'s') }}</flux:button>
            </flux:modal.trigger>
        </div>
        <flux:text color='blue' class="text-base">{{ $title }}</flux:text>

        <div>
            <flux:input class="md:w-50" wire:model.live='search' icon="magnifying-glass"
                placeholder="Search {{ $title }}" />
        </div>
    </div>
    <flux:modal name="myModal" class="w-200">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">Taric Details</flux:heading>
                {{-- <flux:text class="mt-2">Make changes to your personal details.</flux:text> --}}
            </div>
            <div class="grid grid-cols-4 gap-2">

                <div class="col-span-1">
                    <flux:input size="sm" wire:model='code' label="Taric code:" placeholder="Taric code" autofocus/>
                </div>
                <div class="col-span-1">
                    <flux:input size="sm" wire:model='duty_rate' label="Duty rate:" placeholder="Duty rate " />
                </div>
                <div class="col-span-1">
                    <flux:input size="sm" wire:model='reguler_artikel' label="Reguler artikel:"
                        placeholder="Reguler artikel" />
                </div>
                <div class="col-span-4">
                    <flux:input size="sm" wire:model='name_en' label="Taric Name English" placeholder="Taric Name EN" />
                </div>
                <div class="col-span-4">
                    <flux:input size="sm" wire:model='name_de' label="Taric Name DE" placeholder="Taric name DE" />
                </div>

                <div class="col-span-4">
                    <flux:input size="sm" wire:model='description_en' label="Description EN:"
                        placeholder="Taric Description EN" />
                </div>
                <div class="col-span-4">
                    <flux:input size="sm" wire:model='description_de' label="Description DE:"
                        placeholder="Taric Description DE" />
                </div>

            </div>

            <div class="flex">
                <flux:spacer />
                <flux:button type="submit" variant="ghost" icon="x-circle" x-on:click="Flux.modal('myModal').close()">
                    Cancel
                </flux:button>
                <flux:button type="submit" wire:click="{{ $update ? 'Update' : 'Save' }}" icon="plus-circle"
                    variant="primary">
                    {{ $update ? 'Save changes' : 'Save' }}
                </flux:button>
            </div>
        </div>
    </flux:modal>
    <div class=" mt-2 text-center">
        @if (session('success'))
        <flux:callout variant="success" icon="check-circle" heading="{{ session('success') }}" />
        @endif
    </div>
    <div class="relative overflow-x-auto">
        <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400 mt-2.5">
            <thead
                class="sticky top-0  bg-gray-100 round text-xs text-gray-700 uppercase dark:bg-gray-700 dark:text-gray-400">
                <tr>
                    <th scope="col" class="px-6 py-3">
                        ID
                    </th>
                    <th scope="col" class="px-6 py-3">
                        Code
                    </th>
                    <th scope="col" class="px-6 py-3">
                        Name - EN
                    </th>
                    <th scope="col" class="px-6 py-3">
                        Name DE
                    </th>
                    <th scope="col" class="px-6 py-3">
                        Duty rate
                    </th>
                    <th colspan="2" scope="col" class="px-6 py-3">
                        Actions
                    </th>
                </tr>
            </thead>
            <tbody>
                @forelse ($tarics as $taric )
                <tr wire:key='{{ $taric->id }}' class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 border-gray-200">

                    <td class="px-2 py-1">
                        {{ $taric->id }} <span class=" text-blue-700 text-bold ">({{ $taric->items_count }})</span> 
                    </td>
                    <td class="px-2 py-1">
                        {{ $taric->code }}
                    </td>
                    <td class="px-2 py-1">
                        {{ $taric->name_en }}
                    </td>
                    <td class="px-2 py-1">
                        {{ $taric->name_de }}
                    </td>
                    <td class="px-2 py-1">
                        {{ $taric->duty_rate }}
                    </td>
                    
                    <td class="px-2 py-1">
                        <flux:button variant='primary' icon='pencil-square' wire:click='edit({{ $taric->id }})'
                            size='sm'>Details</flux:button>
                    </td>
                    {{-- <td class="px-2 py-1">
                        <flux:button variant='danger' icon='minus-circle' wire:click='delete({{ $taric->id }})'
                            wire:confirm='Are you sure deleting this record?' size='sm'>Delete</flux:button>
                    </td> --}}
                </tr>
                @empty
                <tr>
                    <th colspan="5" scope="row"
                        class="px-2 py-1 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                        No records found
                    </th>
                </tr>
                @endforelse
            </tbody>
        </table>
        <div class="container mx-auto w-100">{{ $tarics->links() }}</div>
    </div>

</div>