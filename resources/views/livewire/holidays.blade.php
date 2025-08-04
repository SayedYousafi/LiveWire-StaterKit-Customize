<div>
    @include('partials.holidayModal')
    <div class="flex justify-between mt-3">
        <div class="mb-2">
            <flux:modal.trigger name="holidayModal">
                <flux:button wire:click="cancel" icon="plus-circle" class="bg-blue-800! text-white! hover:bg-blue-700!">
                    New {{ Str::before($title, 's') }}
                </flux:button>
            </flux:modal.trigger>
        </div>

        <flux:text color="blue" class="text-base">{{ $title }}</flux:text>
        <div class="flex gap-2">
            <flux:select wire:model.live='filterByCountry' class="!w-50" placeholder="Filter by country...">

                <flux:select.option>Germany</flux:select.option>
                <flux:select.option>Cyprus</flux:select.option>
                <flux:select.option>China</flux:select.option>
            </flux:select>

            <flux:input class="md:w-50" wire:model.live="search" icon="magnifying-glass"
                placeholder="Search {{ $title }}" />
        </div>
    </div>

    @if (session('success'))
    <div class="mt-2 text-center mb-2">
        <flux:callout variant="success" icon="check-circle" heading="{{ session('success') }}" />
    </div>
    @endif
    <table class="table-default">
        <thead>
            <tr>
                <th>ID</th>
                <th>Country</th>
                <th>Date</th>
                <th>Day</th>
                <th>Holiday name</th>
                <th>Type</th>
                <th>Comments</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($holidays as $holiday)
            <tr>
                <td>{{ $holiday->id }}</td>
                <td>{{ $holiday->country }}</td>
                <td>{{ $holiday->date }}</td>
                <td>{{ $holiday->day }}</td>
                <td>{{ $holiday->name }}</td>
                <td>{{ $holiday->type }}</td>
                <td>{{ $holiday->comments }}</td>
                <td>
                    <flux:button variant="primary" icon="pencil-square" wire:click="edit({{ $holiday->id }})" size="sm">
                        Edit</flux:button>
                </td>
            </tr>
            @empty
            <tr>
                <th colspan="10">No holiday records found</th>
            </tr>
            @endforelse
        </tbody>
    </table>
    <div class="mt-3">
        {{ $holidays->links() }}
    </div>
</div>