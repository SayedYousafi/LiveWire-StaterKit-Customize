@props(['current'])

<div class="flex justify-between">
    @if ($current==='invoices' || $current ==='invoicesClosed' || $current ==='Problems')
        <div><flux:button onclick="history.back()">Back</flux:button></div>
    @endif
    
    <div>
        <flux:navbar
            class="flex justify-center gap-x-6 py-4 bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-100">

            <flux:navbar.item href="{{ route('invoices') }}" wire:navigate
                class="{{ $current === 'invoices' ? 'font-bold text-blue-600 underline' : '' }}">
                Open invoices
            </flux:navbar.item>

            <flux:navbar.item href="{{ route('invoicesClosed') }}" wire:navigate
                class="{{ $current === 'invoicesClosed' ? 'font-bold text-blue-600 underline' : '' }}">
                Closed invoices
            </flux:navbar.item>

            <flux:navbar.item href="{{ route('customers') }}" wire:navigate
                class="{{ $current === 'customers' ? 'font-bold text-blue-600 underline' : '' }}">
                Customers
            </flux:navbar.item>

            <flux:navbar.item href="{{ route('cargos') }}" wire:navigate
                class="{{ $current === 'cargos' ? 'font-bold text-blue-600 underline' : '' }}">
                Cargos
            </flux:navbar.item>

            <flux:navbar.item href="{{ route('cargotypes') }}" wire:navigate
                class="{{ $current === 'cargotypes' ? 'font-bold text-blue-600 underline' : '' }}">
                Cargos type
            </flux:navbar.item>

        </flux:navbar>
    </div>

 @if ($current==='invoices' || $current ==='invoicesClosed' || $current ==='Problems')
    <div>
        <flux:input class="md:w-50" wire:model.live.debounce.500ms="search" icon="magnifying-glass"
            placeholder="Search {{ $current }}"/>
    </div>
@endif
</div>