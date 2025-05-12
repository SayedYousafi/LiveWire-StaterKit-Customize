<div class="container mx-auto">
    <flux:modal.trigger name="itemModal" class="mb-3">
        <flux:button icon='plus-circle' class=" bg-blue-800! text-white! hover:bg-blue-700!">New item</flux:button>
    </flux:modal.trigger>

    <flux:modal name="itemModal" class="md:w-96">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">Item Detials</flux:heading>
                {{-- <flux:text class="mt-2">Enter the details of this item here.</flux:text> --}}
            </div>
            <flux:input wire:model='name' label="Item Name" placeholder="Item name" />

            <flux:input wire:model='price' label="Item Price" placeholder="Item price" />

            <div class="flex">
                <flux:spacer />
                @if ($update)
                <flux:button type="submit" variant="ghost" icon='x-circle' x-on:click="Flux.modal('itemModal').close()">
                    Cancel</flux:button>
                <flux:button type="submit" icon='plus-circle' variant="primary" wire:click='updateItem'>Save changes
                </flux:button>
                @else
                <flux:button type="submit" variant="ghost" icon='x-circle' x-on:click="Flux.modal('itemModal').close()">
                    Cancel</flux:button>
                <flux:button type="submit" wire:click="addItem" icon='plus-circle' variant="primary">Save Item
                </flux:button>
                @endif

            </div>
        </div>
    </flux:modal>
    <div class=" mt-2">
        @if (session('success'))
        <flux:callout variant="success" icon="check-circle" heading="{{ session('success') }}" />
        @endif
    </div>


    <table class="w-full text-sm text-left rtl:text-right text-gray-600 dark:text-gray-400 mt-2.5">
        <thead
            class="sticky top-0  bg-gray-100 text-xs text-bold text-gray-700 uppercase dark:bg-gray-700 dark:text-gray-400">
            <tr>
                <th scope="col" class="px-6 py-3">
                    ID
                </th>
                <th scope="col" class="px-6 py-3">
                    Item name
                </th>
                <th scope="col" class="px-6 py-3">
                    RMB Price
                </th>
                <th colspan="2" scope="col" class="px-6 py-3">
                    Actions
                </th>
            </tr>
        </thead>
        <tbody>
            @forelse ($items as $item)
            <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 border-gray-200">

                <td class="px-2 py-1">
                    {{ $item->id }}
                </td>
                <td class="px-2 py-1">
                    {{ $item->name }}
                </td>
                <td class="px-2 py-1">
                    {{ $item->price }}
                </td>

                <td class="px-2 py-1">
                    <flux:button variant='primary' icon='pencil-square' wire:click='edit({{ $item->id }})' size='sm'>
                        Edit</flux:button>
                </td>
                <td class="px-2 py-1">
                    <flux:button variant='danger' icon='minus-circle' wire:click='delete({{ $item->id }})'
                        wire:confirm='Are you sure deleting this record?' size='sm'>Delete</flux:button>
                </td>
            </tr>
            @empty
            <tr>
                <td class="px-2 py-1" colspan="4" align="center">No records found</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>