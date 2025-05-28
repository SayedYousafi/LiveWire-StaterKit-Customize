<div>
    <!-- Header: Button, Title, Search -->
    <div class="flex justify-between items-center mb-4">
        <!-- New Item Button -->
        <a href="{{ route('itemAdd') }}">
            <flux:button icon="plus-circle" class="bg-blue-800! text-white! hover:bg-blue-700!">
                New {{-- {{ $title }} --}}
            </flux:button>
        </a>

        <!-- Title -->
        <flux:text color="blue" class="text-base">
            {{ $title }}
        </flux:text>

        <!-- Search Input -->
        <div>
            <flux:input class="md:w-50" wire:model.live.debounce.500ms="search" icon="magnifying-glass"
                placeholder="Search {{ $title }}" autofocus />
        </div>
    </div>

    <!-- Success Message -->
    @if (session('success'))
    <div class="mt-2 text-center">
        <flux:callout variant="success" icon="check-circle" heading="{{ session('success') }}" />
    </div>
    @endif

    <!-- Data Table -->
    <table class="table-default mt-2.5 w-full">
        <thead>
            <tr>
                <th>ID</th>
                <th>EAN</th>
                <th>ParentNo.</th>
                <th>Item name - CN</th>
                <th>Supplier ID - Name</th>
                <th>Price</th>
                <th>Remark</th>
                <th colspan="2">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($items as $item)
            <tr wire:key="{{ $item->itemId }}" @if ($item->isActive == 'N')
                class="bg-red-50 border-b dark:bg-gray-800"
                @endif
                >
                <td>{{ $item->itemId }}</td>
                <td>{{ $item->ean }}</td>
                <td>{{ $item->parent_no_de }}</td>
                <td>{{ $item->item_name }} - {{ $item->item_name_cn }}</td>
                <td>{{ $item->name }} - {{ $item->supplierId }}</td>
                <td>{{ $item->RMB_Price }}</td>
                <td>{{ $item->remark }}</td>

                <!-- Action: Details -->
                <td>
                    <flux:button variant="primary" icon="pencil-square" class="text-white!"
                        href="{{ route('itemDetail', $item->itemId) }}" size="sm">
                        Details
                    </flux:button>
                </td>

                <!-- Action: Suppliers -->
                <td>
                    <flux:button icon="users" wire:click="suppliers({{ $item->itemId }})"
                        class="bg-blue-700! text-white! hover:bg-blue-600!" size="sm">
                        Suppliers
                    </flux:button>
                </td>
            </tr>

            <!-- Conditional Supplier Component -->
            @if ($selectedSupplier === $item->itemId)
            <tr>
                <td colspan="9">
                    @livewire('default-supplier', ['id' => $item->itemId], key('supplier-'.$item->itemId))
                </td>
            </tr>
            @endif

            @empty
            <tr>
                <td colspan="9" class="text-center">No records found</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <!-- Pagination -->
    <div class="mt-3">
        {{ $items->links() }}
    </div>
</div>