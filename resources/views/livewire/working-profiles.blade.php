<div>

    @include('partials.working-profile')
    <div class="flex justify-between mb-3">
        <flux:modal.trigger name="working-profile">
            <flux:button wire:click='cancel' icon="plus-circle" class="bg-blue-800! text-white! hover:bg-blue-700!">New working profile
            </flux:button>
        </flux:modal.trigger>
        <flux:text color="blue" class="text-base">{{ $title }}</flux:text>
        <flux:input class="md:w-50" wire:model.live="search" icon="magnifying-glass"
            placeholder="Search {{ $title }}" />
    </div>
    @if (session('success'))
    <flux:callout variant="success" icon="check-circle" heading="{{ session('success') }}" class="mb-2" />
    @endif
    <table class="table-default">
        <thead>
            <tr>
                <th>ID</th>
                <th>Profile name</th>
                <th>Public Holiday</th>
                <th>Leave entitlement</th>
                <th >Working days</th>
                <th colspan="2">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($profiles as $profile)
            <tr>
                <td>{{ $profile->id }}</td>
                <td>{{ $profile->name }}</td>
                <td>{{ $profile->public_holiday }}</td>
                <td>{{ $profile->entitlement }}</td>
                <td>
                    @foreach ($profile->working_days as $days )
                    <li>
                        {{ $days }}
                    </li>
                    @endforeach
                </td>

                <td>
                    <flux:button wire:confirm='Are you sure?' variant="danger" icon="minus-circle" wire:click="delete({{ $profile->id }})" size="sm">
                        Delete
                    </flux:button>
                </td>
                <td>
                    <flux:button variant="primary" icon="pencil-square" wire:click="edit({{ $profile->id }})" size="sm">
                        Edit
                    </flux:button>
                </td>
            </tr>
            @empty

            @endforelse
        </tbody>
    </table>
</div>