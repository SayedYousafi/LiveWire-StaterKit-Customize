<div class="container mx-auto">
    <div class="flex justify-between mt-3">
        <div>
            <flux:modal.trigger name="myModal">
                <flux:button wire:click="cancel" icon="plus-circle" class="bg-blue-800! text-white! hover:bg-blue-700!">
                    New Cargo Type
                </flux:button>
            </flux:modal.trigger>
        </div>
        <x-sub-menu :current="$title" />
        <div class="flex justify-end items-center gap-4 my-2">
            {{-- <flux:text color="blue" class="text-base">Cargo Types</flux:text> --}}
            <flux:input class="md:w-50" wire:model.live="search" icon="magnifying-glass"
                placeholder="Search {{ $title }}" />
        </div>
    </div>

    {{-- Modal for Create / Edit --}}
    <flux:modal name="myModal" class="!w-[40rem] max-w-none">
        <div class="space-y-6">
            <flux:heading size="lg">Cargo Type Details</flux:heading>

            @if ($isUpdate)
            <div class="flex items-center gap-10 mt-2.5">
                <flux:switch wire:click="$toggle('enableEdit')" label="Enable edit" />
            </div>
            @endif

            <div class="grid grid-cols-3 gap-4">
                <flux:input size="sm" wire:model="cargo_type" label="Cargo Type" placeholder="Enter name"
                    :disabled="$isUpdate && !$enableEdit" class="w-full" />
                <flux:input size="sm" wire:model="duration" label="Duration (days)" placeholder="Enter duration"
                    :disabled="$isUpdate && !$enableEdit" class="w-full" />
                {{-- <flux:input size="sm" type="time" wire:model="time_pre" label="Preparation Time (h:min)"
                    :disabled="$isUpdate && !$enableEdit" class="w-full" />
                <flux:input size="sm" type="time" wire:model="time_rec" label="Receiving Time (h:min)"
                    :disabled="$isUpdate && !$enableEdit" class="w-full" />
                <flux:input size="sm" type="time" wire:model="time_ship" label="Shipping Time (h:min)"
                    :disabled="$isUpdate && !$enableEdit" class="w-full" /> --}}
            </div>

            <div class="flex justify-end">
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

    {{-- Success Message --}}
    @if (session('success'))
    <div class="mt-2 text-center">
        <flux:callout variant="success" icon="check-circle" heading="{{ session('success') }}" />
    </div>
    @endif

    {{-- Table --}}
    <div class="relative overflow-x-auto mt-4">
        <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
            <thead class="sticky top-0 bg-gray-100 text-xs text-gray-700 uppercase dark:bg-gray-700 dark:text-gray-400">
                <tr>
                    <th class="px-6 py-3">ID</th>
                    <th class="px-6 py-3">Type</th>
                    <th class="px-6 py-3">Duration (days)</th>
                    {{-- <th class="px-6 py-3">Prep Time</th>
                    <th class="px-6 py-3">Recv Time</th>
                    <th class="px-6 py-3">Ship Time</th> --}}
                    <th class="px-6 py-3" colspan="2">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($cargoTypes as $cargoType)
                <tr wire:key="{{ $cargoType->id }}" class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                    <td class="px-6 py-2">{{ $cargoType->id }}</td>
                    
                    <td class="px-6 py-2">{{ $cargoType->cargo_type }}</td>
                    <td class="px-6 py-2">{{ $cargoType->duration }}</td>
                    {{-- <td class="px-6 py-2">{{ $cargoType->time_pre }} min</td>
                    <td class="px-6 py-2">{{ $cargoType->time_rec }} min</td>
                    <td class="px-6 py-2">{{ $cargoType->time_ship }} min</td> --}}
                    <td class="px-2 py-1">
                        <flux:button variant="primary" icon="pencil-square" wire:click="edit({{ $cargoType->id }})"
                            size="sm">Edit</flux:button>
                    </td>
                    <td class="px-2 py-1">
                        {{-- <flux:button variant="danger" icon="minus-circle" wire:click="delete({{ $cargoType->id }})"
                            wire:confirm="Are you sure?" size="sm">Delete</flux:button> --}}
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-6 py-2 text-center">No cargo types found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>

        {{-- Pagination --}}
        <div class="mt-4">
            {{ $cargoTypes->links() }}
        </div>
    </div>
</div>