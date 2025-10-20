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
@if($details)
<flux:modal name="confirms" class="!md:w-96">
    <div class="space-y-6">
        <div>
            <flux:heading size="lg">Confirmation based on quality criteria</flux:heading>
            {{-- <flux:text class="mt-2">Make changes to your personal details.</flux:text> --}}
        </div>

        <div class="text-sm">
            <table class="table-default">
                <thead>
                    <th>ID</th>
                    <th>Item ID</th>
                   
                    <th>Confirmed by</th>
                    <th>Qaulity</th>
                    <th>Bad QTY</th>
                    
                    <th>Remarks</th>
                </thead>
                <tbody>
                    @forelse ($confirms as $confirm )
                        <tr>
                            <td>{{ $confirm->id }}</td>
                            <td>{{ $confirm->item_id }}</td>
                            <td>{{ $confirm->confirm_by }}</td>
                            <td>{{ $confirm->quality->name }}</td>
                            <td>{{ $confirm->poorQty }}</td>
                            <td>{{ $confirm->remarks }}</td>
                        </tr>
                    @empty
                        
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="flex">
            <flux:spacer />

            <flux:modal.close>
                <flux:button variant="danger">Close</flux:button>
            </flux:modal.close>
        </div>
    </div>
</flux:modal>
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
                <td nowrap='nowrap'>{{ $problem->status }}<sup class="text-blue-600 text-bold hover:underline"> <a href="#" wire:click='more({{ $problem->master_id }})'> More...</a></sup></td>
                <td>{{ $problem->problems }} </td>
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