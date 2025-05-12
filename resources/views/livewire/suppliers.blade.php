<div class="container mx-auto">
    <flux:modal.trigger name="suppliersModal">
        <flux:button wire:click='cancel' icon='plus-circle' class="bg-blue-800! text-white! hover:bg-blue-700!">New
            supplier</flux:button>
    </flux:modal.trigger>

    <flux:modal name="suppliersModal" class="md:w-96">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">Supplier Detail</flux:heading>
                {{-- <flux:text class="mt-2">Make changes to your personal details.</flux:text> --}}
            </div>

            <flux:input wire:model='name' label="Name" placeholder="Supplier name" />
            <flux:input wire:model='contact' label="contact" placeholder="Supplier contact" />
            <flux:input wire:model='address' label="address" placeholder="Supplier address" />

            <div class="flex">
                <flux:spacer />
                @if ($update)
                <flux:button wire:click='updateSuppler' type="submit" variant="primary">Update supplier</flux:button>
                @else
                <flux:button wire:click='save' type="submit" variant="primary">Add supplier</flux:button>
                @endif

            </div>
        </div>
    </flux:modal>
    @if (session('success'))
    <flux:callout heading="{{ session('success') }}" variant='success' />
    @endif
    <div class="relative overflow-x-auto">
        <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400 mt-2.5">
            <thead class="sticky top-0  bg-gray-100 round text-xs text-gray-700 uppercase dark:bg-gray-700 dark:text-gray-400">
                <tr>
                    <th scope="col" class="px-6 py-3">
                        ID
                    </th>
                    <th scope="col" class="px-6 py-3">
                        Name
                    </th>
                    <th scope="col" class="px-6 py-3">
                        Contact
                    </th>
                    <th scope="col" class="px-6 py-3">
                        Address
                    </th>
                    <th colspan="2" scope="col" class="px-6 py-3">
                        Action
                    </th>
                </tr>
            </thead>
            <tbody>
                @forelse ($suppliers as $supplier )
                <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 border-gray-200">

                    <td class="px-2 py-1">
                        {{ $supplier->id }}
                    </td>
                    <td class="px-2 py-1">
                        {{ $supplier->name }}
                    </td>
                    <td class="px-2 py-1">
                        {{ $supplier->contact }}
                    </td>
                    <td class="px-2 py-1">
                        {{ $supplier->address }}
                    </td>
                    <td class="px-2 py-1">
                        <flux:button variant='primary' icon='pencil-square' wire:click='edit({{ $supplier->id }})'
                            size='sm'>Edit</flux:button>
                    </td>
                    <td class="px-2 py-1">
                        <flux:button variant='danger' icon='minus-circle' wire:click='delete({{ $supplier->id }})'
                            wire:confirm='Are you sure deleting this record?' size='sm'>Delete</flux:button>
                    </td>
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
    </div>

</div>