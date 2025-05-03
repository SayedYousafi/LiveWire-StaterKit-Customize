<div class="relative overflow-x-auto">
    <flux:modal.trigger name="add-item" class="mb-3">
        <flux:button class=" bg-blue-800! text-white! hover:bg-blue-700!">New Item</flux:button>
    </flux:modal.trigger>

    <flux:modal name="add-item" class="md:w-96">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">Add new item</flux:heading>
                <flux:text class="mt-2">Enter the details of this item here.</flux:text>
            </div>
            <flux:input wire:model='name' label="Item Name" placeholder="Item name" />

            <flux:input wire:model='price' label="Item Price" placeholder="Item price" />

            <div class="flex">
                <flux:spacer />
                <flux:button type="submit" wire:click="addItem" variant="primary">Save Item</flux:button>
            </div>
        </div>
    </flux:modal>
    <flux:modal name="edit-item" class="md:w-96">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">Update item</flux:heading>
                <flux:text class="mt-2">Make changes to your personal details.</flux:text>
            </div>

            <flux:input wire:model='name' label="Item Name" placeholder="Item name" />

            <flux:input wire:model='price' label="Item Price" placeholder="Item price" />

            <div class="flex">
                <flux:spacer />

                <flux:button type="submit" variant="primary" wire:click='updateItem'>Save changes</flux:button>
            </div>
        </div>
    </flux:modal>
    @if (session('success'))

    <flux:callout variant="success" icon="check-circle" heading="{{ session('success') }}" />

    @endif

    <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
        <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
            <tr>
                <th scope="col" class="px-6 py-3">
                    ID
                </th>
                <th scope="col" class="px-6 py-3">
                    Product name
                </th>
                <th scope="col" class="px-6 py-3">
                    Price
                </th>
                <th scope="col" class="px-6 py-3">
                    Action
                </th>
            </tr>
        </thead>
        <tbody>
            @forelse ($items as $item)

            <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 border-gray-200">
                <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                    {{ $item?->id }}
                </th>
                <td class="px-6 py-4">
                    {{ $item?->name }}
                </td>
                <td class="px-6 py-4">
                    {{ $item?->price }}
                </td>
                <td class="px-6 py-4">
                    <flux:button variant="primary" wire:click='editItem({{ $item->id }})'>Edit</flux:button>

                    <flux:button variant="danger" wire:confirm='Are you sure?' wire:click='deleteItem({{ $item->id }})'>
                        Delete</flux:button>
                </td>
            </tr>
            @empty
            <tr>
                <td class="px-6 py-4" colspan="4" align="center">No records found</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>