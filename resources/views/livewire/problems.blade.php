<div>
    <x-sub-menu :current="$title" />
    @include('partials.adjust-problem')
    @if (session('success'))
    <div class=" mt-2 text-center mb-2">
        <flux:callout variant="success" icon="check-circle" heading="{{ session('success') }}" />
    </div>
    @endif
    @if (session('error'))
    <div class=" mt-2 text-center mb-2">
        <flux:callout variant="danger" icon="x-circle" heading="{{ session('error') }}" />
    </div>
    @endif
    <table class="table-default">
        <thead>
            <tr>
                <th>ID</th>
                <th>Supplier ID</th>
                <th>Item name</th>
                <th>Order No.</th>
                <th>SOID</th>
                <th>QTY</th>
                <th>Problem type</th>
                <th>Description</th>
                <th>Remark</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($problmes as $problem)
            <tr>
                @php
                // Gather all numeric quantities
                $allQuantities = collect([
                is_numeric($problem->qty_split) ? $problem->qty_split : null,
                is_numeric($problem->qty) ? $problem->qty : null,
                is_numeric($problem->qty_label) ? $problem->qty_label : null,
                ])->reject(fn($q) => $q === null);

                // Get unique values for display
                $quantities = $allQuantities->unique()->values();
                $displayQty = $quantities->implode('/');
                @endphp
                <td>{{ $problem->sqrID }}</td>
                <td>{{ $problem->SUPPID }}</td>
                <td>{{ $problem->item_name }}</td>
                <td>{{ $problem->order_no }}</td>
                <td>{{ $problem->supplier_order_id }}</td>
                <td>{{ $displayQty }}</td>
                <td>{{ $problem->status }}</td>
                <td>{{ $problem->problems }}</td>
                <td>{{ $problem->remark }}</td>
                <td>
                    <flux:button class="bg-blue-600! hover:bg-blue-500! text-white!" icon='adjustments-horizontal'
                        size='sm' wire:click="adjustProblem({{ $problem->sqrID }})">
                        Adjust
                    </flux:button>
                </td>

            </tr>
            @empty
            <tr>
                <th colspan="10">No problem found</th>
            </tr>
            @endforelse
        </tbody>
    </table>
    <livewire:repreints />
</div>