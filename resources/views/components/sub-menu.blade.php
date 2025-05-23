@php
    $currentRoute = Route::currentRouteName();
@endphp

<flux:navbar class="flex justify-center gap-x-6 py-4 bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-100">

    <flux:navbar.item
        href="{{ route('invoices') }}"
        wire:navigate
        class="{{ $currentRoute === 'invoices' ? 'font-bold text-blue-600 underline' : '' }}">
        Open invoices
    </flux:navbar.item>

    <flux:navbar.item
        href="#"
        wire:navigate
        class="{{ $currentRoute === 'closed-invoices' ? 'font-bold text-blue-600 underline' : '' }}">
        Closed invoices
    </flux:navbar.item>

    <flux:navbar.item
        href="#"
        wire:navigate
        class="{{ $currentRoute === 'customers' ? 'font-bold text-blue-600 underline' : '' }}">
        Customers
    </flux:navbar.item>

    <flux:navbar.item
        href="{{ route('cargos') }}"
        wire:navigate
        class="{{ $currentRoute === 'cargos' ? 'font-bold text-blue-600 underline' : '' }}">
        Cargos
    </flux:navbar.item>

    <flux:navbar.item
        href="{{ route('cargotypes') }}"
        wire:navigate
        class="{{ $currentRoute === 'cargotypes' ? 'font-bold text-blue-600 underline' : '' }}">
        Cargos type
    </flux:navbar.item>

</flux:navbar>
