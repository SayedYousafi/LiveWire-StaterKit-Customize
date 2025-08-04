<div class="bg-white dark:bg-gray-900 rounded-2xl shadow p-4">
    <div class="flex items-center justify-between mb-4">
        <div>
            @if(!empty($sup_name))
            <p class="text-gray-700 dark:text-gray-300">Current default supplier is: <strong> {{ $item->name }} - {{ $item->supplierId }}</strong>
            </p>
            @endif
        </div>
    </div>
        <div class=" mt-2 text-center mb-2.5">
        @if (session('success'))
        <flux:callout variant="success" icon="check-circle" heading="{{ session('success') }}" />
        @endif
    </div>
    @include('partials.default-supplier')
        <table class="table-nested">
            <thead>
                <tr class="table-highlighted">
                    <th class="px-3 py-2">ID</th>
                    <th class="px-3 py-2">Item ID</th>
                    <th class="px-3 py-2">EAN</th>
                    <th class="px-3 py-2">Item Name / CN</th>
                    <th class="px-3 py-2">Supplier ID / Name</th>
                    <th class="px-3 py-2">Default?</th>
                    <th class="px-3 py-2">Price</th>
                    <th class="px-3 py-2">MoQ</th>
                    <th class="px-3 py-2">OI</th>
                    <th class="px-3 py-2">Is_PO?</th>
                    <th class="px-3 py-2" colspan="4">Action</th>
                </tr>
            </thead>
            <tbody>
                @php
                    //dd($supplierItems)
                @endphp
                @forelse ($supplierItems as $item)
                <tr wire:key="{{ $item->id }}" >
                    <td class="px-3 py-2">{{ $item->id }}</td>
                    <td class="px-3 py-2">{{ $item->item_id }}</td>
                    <td class="px-3 py-2">{{ optional($item->item)->ean }}</td>
                    <td class="px-3 py-2">{{ optional($item->item)->item_name }} / {{
                        optional($item->item)->item_name_cn }}</td>
                    <td class="px-3 py-2">{{ $item->supplier_id }} / {{ optional($item->supplier)->name }}</td>
                    <td class="px-3 py-2">{{ $item->is_default }}</td>
                    <td class="px-3 py-2">{{ $item->price_rmb }}</td>
                    <td class="px-3 py-2">{{ $item->moq }}</td>
                    <td class="px-3 py-2">{{ $item->oi }}</td>
                    <td class="px-3 py-2">{{ $item->is_po }}</td>
                    <td class="px-3 py-2">
                        <flux:button wire:click="getSuppItem({{ $item->id }}, {{ $item->supplier_id }})" 
                        icon='pencil-square' size="sm" class="!bg-blue-600 !text-white hover:bg-blue-500!">Edit</flux:button>
                    </td>
                    <td class="px-3 py-2">
                        @if ($item->is_default == 'Y')
                        <flux:button variant="primary" size="sm" icon='minus-circle'
                            onclick="alert('This item already has a default supplier')"
                            class="!bg-gray-600 !text-white hover:bg-gray-500!"
                            >Default</flux:button>
                        @else
                        <flux:button wire:click="makeDefault({{ $item->id }}, {{ $item->item_id }})" variant="primary" icon='minus-circle'
                            size="sm">
                            Default
                        </flux:button>
                        @endif
                    </td>
                    <td class="px-3 py-2">
                        @if ($item->is_default == 'Y')
                        <flux:button variant="danger" size="sm" icon='trash'
                            onclick="alert('You are not allowed to delete the default supplier')">Delete</flux:button>
                        @else
                        <flux:button wire:click="deleteSupp({{ $item->id }})" icon='trash' variant="danger" size="sm"
                            onclick="return confirm('Are you sure you want to delete this supplier?')">
                            Delete
                        </flux:button>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="12" class="text-center px-3 py-4 text-gray-500">No records found</td>
                </tr>
                @endforelse
                @if ($suppliers->count())
                <tr>
                    <td colspan="2" class="px-3 py-2">
                        <flux:button variant='primary' icon='plus-circle' wire:click="insert({{ $item->id }})" size="sm">New Supplier</flux:button>
                    </td>
                </tr>
                @endif
            </tbody>
        </table>


    <div class="mt-4 flex justify-center">
        {{-- {{ $suppliers->links() }} --}}
    </div>
</div>