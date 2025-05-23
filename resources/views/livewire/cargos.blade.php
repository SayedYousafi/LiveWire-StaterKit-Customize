<div class="container mx-auto">
    <div class="flex justify-between mt-3">
        <div>
            <flux:modal.trigger name="myModal">
                <flux:button wire:click="cancel" icon="plus-circle" class="bg-blue-800! text-white! hover:bg-blue-700!">
                    New {{ Str::before($title, 's') }}
                </flux:button>
            </flux:modal.trigger>
        </div>

<x-sub-menu/>
        <div>
            <flux:input class="md:w-50" wire:model.live="search" icon="magnifying-glass"
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
                    <flux:input size="sm" wire:model="cargo_status" label="Status" placeholder="Cargo Status"
                        class="w-full" :disabled="$isUpdate && !$enableEdit" />
                </div>

                <div class="col-span-1">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Cargo Type</label>
                    <flux:dropdown>
                        <flux:button icon:trailing="chevron-down">
                            {{ $cargo_type_id ? 'Cargo Type: ' . $cargo_type_id : 'Cargo Type' }}
                        </flux:button>
                        <flux:menu>
                            @foreach ($cargo_types as $type)
                            <flux:menu.item wire:click="$set('cargo_type_id', {{ $type->id }})"
                                :disabled="$isUpdate && !$enableEdit">{{ $type->cargo_type }}</flux:menu.item>
                            <flux:menu.separator />
                            @endforeach
                        </flux:menu>
                    </flux:dropdown>
                </div>

                <div class="col-span-1">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Customer</label>
                    <flux:dropdown>
                        <flux:button icon:trailing="chevron-down">
                            {{ $customer_id ? 'Customer: ' . $customer_id : 'Customer' }}
                        </flux:button>
                        <flux:menu>
                            @foreach ($customers as $customer)
                            <flux:menu.item wire:click="$set('customer_id', {{ $customer->id }})"
                                :disabled="$isUpdate && !$enableEdit">{{ $customer->customer_company_name }}</flux:menu.item>
                            <flux:menu.separator />
                            @endforeach
                        </flux:menu>
                    </flux:dropdown>
                </div>

                <div class="col-span-1">
                    <flux:input size="sm" wire:model="dep_date" label="Departure Date" placeholder="YYYY-MM-DD"
                        type="date" class="w-full" :disabled="$isUpdate && !$enableEdit" />
                </div>

                <div class="col-span-1">
                    <flux:input size="sm" wire:model="pickup_date" label="Pickup Date" placeholder="YYYY-MM-DD"
                        type="date" class="w-full" :disabled="$isUpdate && !$enableEdit" />
                </div>

                <div class="col-span-2">
                    <flux:input size="sm" wire:model="shipped_at" label="Shipped At" placeholder="YYYY-MM-DD HH:MM:SS"
                        type="datetime-local" class="w-full" :disabled="$isUpdate && !$enableEdit" />
                </div>

                <div class="col-span-4">
                    <flux:input size="sm" wire:model="remark" label="Remarks" placeholder="Enter remarks" class="w-full"
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
                    
                    <th class="px-6 py-3">Type</th>
                    <th class="px-6 py-3">Customer</th>
                    <th class="px-6 py-3">Departure</th>
                    <th class="px-6 py-3">Pickup</th>
                    <th class="px-6 py-3">Shipped</th>
                    <th class="px-6 py-3">Remarks</th>
                    <th class="px-6 py-3">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($cargos as $cargo)
                <tr wire:key="{{ $cargo->id }}"
                    class="border-b dark:border-gray-700 border-gray-200 bg-white dark:bg-gray-800">
                    <td class="px-2 py-1">{{ $cargo->id }}</td>
                    <td class="px-2 py-1">{{ $cargo->cargo_no }}</td>
                    
                    <td class="px-2 py-1">{{ $cargo->cargo_type_id }}</td>
                    <td class="px-2 py-1">{{ $cargo->customer_id }}</td>
                    <td class="px-2 py-1">{{ $cargo->dep_date }}</td>
                    <td class="px-2 py-1">{{ $cargo->pickup_date }}</td>
                    <td class="px-2 py-1">{{ $cargo->shipped_at }}</td>
                    <td class="px-2 py-1">{{ $cargo->remark }}</td>
                    <td class="px-2 py-1">
                        <flux:button variant="primary" icon="pencil-square" wire:click="edit({{ $cargo->id }})"
                            size="sm">
                            Edit</flux:button>
                    </td>
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