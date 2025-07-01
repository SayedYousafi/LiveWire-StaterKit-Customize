<div>
    @include('partials.editDimentions')


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

        <!-- Parameters -->

        @isset($param)

        @switch($param)
        @case('edited')
        {{-- <a href="{{ url('export/updated') }}"
            class="text-green-600 hover:underline hover:font-semibold flex items-center gap-1 text-sm">
            <i data-lucide="file-down" class="w-5 h-5"></i> Export updated item list
        </a> --}}
        {{-- <a href="#"> <img src="{{ asset('img/mySVG.svg') }}" alt="MySVG" class="w-10 h-10"> Download</a> --}}
        <a href="{{ url('export/updated') }}" class="text-green-700 hover:underline hover:font-semibold flex items-center space-x-2">
            <img src="{{ asset('img/icon_download_CSV.svg') }}" alt="MySVG" class="w-10 h-10">
            <span>Export updated item list</span>
        </a>



        @break
        @case('is_new')

            <a href="{{ url('export/isNew') }}" class="text-green-700 hover:underline hover:font-semibold flex items-center space-x-2">
            <img src="{{ asset('img/icon_download_CSV.svg') }}" alt="MySVG" class="w-10 h-10">
            <span>Export New items list</span>
        </a>
        @break
        @default

        @endswitch
        @else

        
            <a href="{{ url('export') }}" class="text-green-700 hover:underline hover:font-semibold flex items-center space-x-2">
            <img src="{{ asset('img/icon_download_CSV.svg') }}" alt="MySVG" class="w-10 h-10">
            <span>Export Full items list</span>
            </a>
        @endisset

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
                <th>Shipp_class</th>
                <th>Remark</th>
                <th colspan="2">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($items as $item)
            @php
            $ship_class=ShippingClass($item->weight, $item->length, $item->width, $item->height)
            @endphp
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
                <td nowrap @if($ship_class=='Na' ) class="bg-red-50 border-b dark:bg-gray-800" @endif>
                    {{ $ship_class }}
                    @if($ship_class =='Na')
                    <div class="mt-2 mb-2">
                        <flux:button icon='wrench-screwdriver' size='sm' wire:click="fixDimentions({{ $item->itemId }})"
                            variant="danger">Fix</flux:button>
                    </div>
                    @endif

                </td>
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
            <tr wire:key="{{ $item->itemId }}">
                <td colspan="10">
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