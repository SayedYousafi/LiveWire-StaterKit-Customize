<div class="container mx-auto">
    <div class="flex justify-between">
        <flux:modal.trigger name="myModal" class="mb-3">
            <flux:button wire:click='cancel' icon='plus-circle' class=" bg-blue-800! text-white! hover:bg-blue-700!">
                New {{-- {{ $title }} --}}
            </flux:button>
        </flux:modal.trigger>

        <flux:text color="blue" class="text-base">{{ $title }}</flux:text>

        <div>
            <flux:input class="md:w-50" wire:model.live.debounce.500ms="search" icon="magnifying-glass"
                placeholder="Search {{ $title }}" autofocus/>
        </div>
    </div>
    <flux:modal name="myModal" class="md:w-96">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">Item Detials</flux:heading>
                {{-- <flux:text class="mt-2">Enter the details of this item here.</flux:text> --}}
            </div>
            <flux:input size="sm" wire:model='name' label="Item Name" placeholder="Item name" />

            <flux:input size="sm" wire:model='price' label="Item Price" placeholder="Item price" />

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


    <table class="w-full text-sm text-left rtl:text-right text-gray-600 dark:text-gray-400 mt-2.5">
        <thead
            class="sticky top-0  bg-gray-100 text-xs text-bold text-gray-700 uppercase dark:bg-gray-700 dark:text-gray-400">
            <tr>
                <th scope="col" class="px-6 py-3">
                    ID
                </th>
                <th scope="col" class="px-6 py-3">
                    EAN
                </th>
                <th scope="col" class="px-6 py-3">
                    ParentNo.
                </th>
                <th scope="col" class="px-6 py-3">
                    Item name - CN
                </th>
                <th scope="col" class="px-6 py-3">
                    Supplier ID - Name
                </th>
                <th scope="col" class="px-6 py-3">
                    RMB Price
                </th>
                <th scope="col" class="px-6 py-3">
                    Remark
                </th>
                <th colspan="2" scope="col" class="px-6 py-3">
                    Actions
                </th>
            </tr>
        </thead>
        <tbody>
            @forelse ($items as $item)
            <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 border-gray-200"
            wire:key="{{ $item->itemId }}" @if ($item->isActive =='N')
            class="bg-red-50 border-b dark:bg-gray-800"
            @endif>

                <td class="px-2 py-1">
                    {{ $item->itemId }}
                </td>
                <td class="px-2 py-1">
                    {{ $item->ean }}
                </td>
                <td class="px-2 py-1">
                    {{ $item->parent_no_de }}
                </td>
                <td class="px-2 py-1">
                    {{ $item->item_name }} - {{ $item->item_name_cn }}
                </td>
                <td class="px-2 py-1">
                    {{ $item->name }} - {{ $item->supplierId }}
                </td>
                <td class="px-2 py-1">
                    {{ $item->RMB_Price }}
                </td>
                <td class="px-2 py-1">
                    {{ $item->remark }}
                </td>
                <td class="px-2 py-1">
                    <flux:button  variant='primary' icon='pencil-square' wire:navigate href="{{ route('itemDetail', $item->itemId) }}"
                        size='sm'>
                        Details</flux:button>
                </td>
                {{-- <td class="px-2 py-1">
                    <flux:button variant='danger' icon='minus-circle' wire:click='delete({{ $item->itemId }})'
                        wire:confirm='Are you sure deleting this record?' size='sm'>Delete</flux:button>
                </td> --}}
                <td class="px-2 py-1">
                    <flux:button icon='users' wire:click='suppliers({{ $item->itemId }})'
                        class=" bg-blue-700! text-white! hover:bg-blue-600!" size='sm'>Suppliers</flux:button>
                </td>
            </tr>
            @empty
            <tr>
                <td class="px-2 py-1" colspan="4" align="center">No records found</td>
            </tr>
            @endforelse
        </tbody>
    </table>
    <div class="mt-3"> {{ $items->links() }}</div>
</div>