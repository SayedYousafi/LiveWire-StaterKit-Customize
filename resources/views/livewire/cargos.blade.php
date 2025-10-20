<div class="container mx-auto">
    <div class="flex justify-between mt-3">
        <div>
            <flux:modal.trigger name="myModal">
                <flux:button wire:click="cancel" icon="plus-circle" class="bg-blue-800! text-white! hover:bg-blue-700!">
                    New {{ Str::before($title, 's') }}
                </flux:button>
            </flux:modal.trigger>
        </div>

        <x-sub-menu :current="$title" />
        <div>
            <flux:select size='sm' wire:model.live='filterByStatus' class="!w-50" placeholder="Filter by status...">
                <flux:select.option>Open</flux:select.option>
                <flux:select.option>Arrived</flux:select.option>
                <flux:select.option>Packed</flux:select.option>
                <flux:select.option>Shipped</flux:select.option>
                 <flux:select.option>All</flux:select.option>
            </flux:select>
        </div>
        <div>
            <flux:input class="md:w-50" size='sm' wire:model.live="search" icon="magnifying-glass"
                placeholder="Search {{ $title }}" />
        </div>
    </div>

    <flux:modal name="myModal" class="!w-[70rem] max-w-none">
        <div class="space-y-6">
            <flux:heading size="lg">Cargo Details</flux:heading>
            @if ($isUpdate)
            <div class="flex items-center gap-10 mt-2.5">
                <flux:switch wire:click="$toggle('enableEdit')" label="Enable edit" />
            </div>
            @endif
            <div class="grid grid-cols-4 gap-4">
                <div class="col-span-1">
                    <flux:input size="sm" wire:model="cargo_no" label="Cargo No." placeholder="Cargo Number"
                        class="w-full" autofocus :disabled="$isUpdate && !$enableEdit" />
                </div>

                <div class="col-span-1">
                    <flux:select size='sm' label="Cargo Type" heading="Select Cargo Type" wire:model="cargo_type_id"
                        :disabled="$isUpdate && !$enableEdit">
                        <flux:select.option value="">Select Cargo Type</flux:select.option>
                        @foreach ($cargo_types as $type)
                        <flux:select.option value="{{ $type->id }}">{{ $type->cargo_type }}</flux:select.option>
                        <flux:menu.separator />
                        @endforeach
                    </flux:select>
                </div>

                <div class="col-span-1">

                    <flux:select size='sm' label="Customer" heading="Select Customer" wire:model="customer_id"
                        :disabled="$isUpdate && !$enableEdit">
                        <flux:select.option value="">ID - BillTo - ShipTo</flux:select.option>
                        @foreach ($customers as $customer)
                        <flux:select.option value="{{ $customer->id }}">
                            {{ $customer->id }} -
                            {{ $customer->company_subname }} - {{ $customer->delivery_subname }}</flux:select.option>
                        <flux:menu.separator />
                        @endforeach
                    </flux:select>

                </div>

                <div class="col-span-1">
                    <flux:input size="sm" wire:model="dep_date" label="Est. departure" placeholder="departure at"
                        type="date" class="w-full" :disabled="$isUpdate && !$enableEdit" />
                </div>
                <div class="col-span-1">
                    <flux:input size="sm" wire:model="eta" label="Esitmated arrival time " placeholder="ETA" type="date"
                        class="w-full" :disabled="!$isUpdate || ($isUpdate && !$enableEdit)" />
                </div>

                <div class="col-span-2">
                    <flux:input size="sm" wire:model="remark" label="Remarks" placeholder="Enter remarks" class="w-full"
                        :disabled="$isUpdate && !$enableEdit" />
                </div>
                <div class="col-span-4">
                    <flux:input size="sm" wire:model="online_track" label="Online track" placeholder="Enter online track" class="w-full"
                        :disabled="$isUpdate && !$enableEdit" />
                </div>
            </div>

            <div class="flex">
                <flux:spacer />
                <flux:button type="button" variant="ghost" icon="x-circle" wire:click="cancel"
                    x-on:click="Flux.modal('myModal').close()">
                    Cancel
                </flux:button>
                <flux:button type="submit" wire:click="{{ $isUpdate ? 'update' : 'save' }}" icon="plus-circle"
                    variant="primary">
                    {{ $isUpdate ? 'Save changes' : 'Save' }}
                </flux:button>
            </div>
        </div>
    </flux:modal>

    @if (session('success'))
    <div class="mt-2 text-center">
        <flux:callout variant="success" icon="check-circle" heading="{{ session('success') }}" />
    </div>
    @endif

    <div class="relative overflow-x-auto">
        <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400 mt-2.5">
            <thead class="sticky top-0 bg-gray-100 text-xs text-gray-700 uppercase dark:bg-gray-700 dark:text-gray-400">
                <tr>
                    <th class="px-6 py-3">ID</th>
                    <th class="px-6 py-3">Cargo No</th>
                    <th class="px-6 py-3">Status</th>
                    <th class="px-6 py-3">Cargo type</th>

                    <th class="px-6 py-3">Bill To</th>
                    <th class="px-6 py-3">Ship To</th>
                    <th class="px-6 py-3">Est. Departure</th>
                    <th class="px-6 py-3">Shipped</th>
                    <th class="px-6 py-3">Est. Arrival</th>
                    <th>Arrived (days)</th>
                    <th>Online track</th>
                    <th class="px-6 py-3">Remarks</th>
                    <th class="px-6 py-3" colspan="2">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($cargos as $cargo)
                <tr wire:key="{{ $cargo->id }}"
                    class="border-b dark:border-gray-700 border-gray-200 bg-white dark:bg-gray-800">
                    <td class="px-2 py-1">{{ $cargo->id }}</td>
                    <td class="px-2 py-1">{{ $cargo->cargo_no }}</td>
                    <td class="px-2 py-1">{{ $cargo->cargo_status }}</td>
                    <td class="px-2 py-1">{{ $cargo->cargoType->cargo_type }}</td>
                    {{-- <td class="px-2 py-1">{{ $cargo->customer->customer_company_name }}</td> --}}
                    <td class="px-2 py-1">{{ $cargo->customer->company_subname }}</td>
                    <td class="px-2 py-1">{{ $cargo->customer->delivery_subname }}</td>
                    <td class="px-2 py-1">{{ formatGeneralDate($cargo->dep_date) }}</td>
                    <td class="px-2 py-1">{{$shippedAt= formatGeneralDate($cargo->shipped_at) }}</td>
                    <td class="px-2 py-1">{{$newAta= formatGeneralDate($cargo->eta) }}</td>
                    @if ($cargo->cargo_status == 'Arrived')

                    <td>
                        @php
                        $shippedAt = new DateTime($cargo->shipped_at);
                        $eta = new DateTime($cargo->eta);
                        $interval = $shippedAt->diff($eta);
                        $duration = $interval->format('%a'); // Customize format as needed
                        if($duration == $cargo->cargoType->duration ){
                        $success = 'OnTime';
                        }
                        elseif($duration > $cargo->cargoType->duration){
                        $success = 'Late';
                        }
                        else{
                        $success = 'Early';
                        }
                        @endphp
                        {{ $duration }} - {{ $success }}

                    </td>
                    @else
                    <td>-</td>
                    @endif
                    <td>
    <a href="{{ $cargo->online_track }}" target="_blank" class="!text-blue hover:cursor-pointer hover:font-bold hover:underline">
        {{ $cargo->online_track }}
    </a>
</td>
                    <td class="px-2 py-1">{{ $cargo->remark }}</td>
                    <td class="px-2 py-1">
                        <flux:button variant="primary" icon="pencil-square" wire:click="edit({{ $cargo->id }})"
                            size="sm">
                            Edit</flux:button>
                    </td>
                    @if ($cargo->note !=null AND $cargo->cargo_status !='Arrived')
                    <td>
                        <flux:button wire:click='arrive({{ $cargo->id }})' size='sm' icon='stop-circle'
                            variant='danger'>Arrive</flux:button>
                    </td>
                    @elseif ($cargo->cargo_status =='Arrived')
                    <td>Arrived</td>
                    @endif
                </tr>
                @empty
                <tr>
                    <td colspan="9" class="px-2 py-1 text-center font-medium text-gray-900 dark:text-white">
                        No records found
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>

        <div class="container mx-auto w-100">{{ $cargos->links() }}</div>
    </div>
</div>