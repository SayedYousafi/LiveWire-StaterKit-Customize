<flux:navbar class="flex justify-center gap-x-6 py-4 bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-100">
    <flux:navbar.item 
        href="{{ url('customers') }}" 
        class="hover:text-blue-500 dark:hover:text-blue-400"
        wire:navigate>
        Customers
    </flux:navbar.item>

    <flux:navbar.item 
        href="{{ route('cargos') }}" 
        class="hover:text-blue-500 dark:hover:text-blue-400"
        wire:navigate>
        Cargos
    </flux:navbar.item>

    <flux:navbar.item 
        href="{{ url('cargo-type') }}" 
        class="hover:text-cyan-500 dark:hover:text-cyan-400"
        wire:navigate>
        Cargos type
    </flux:navbar.item>

    <flux:navbar.item 
        href="{{ url('orders.index') }}" 
        class="hover:text-green-500 dark:hover:text-green-400"
        wire:navigate>
        Closed invoices
    </flux:navbar.item>

    <flux:navbar.item 
        href="{{ url('problems') }}" 
        class="hover:text-yellow-500 dark:hover:text-yellow-400"
        wire:navigate>
        Problems
    </flux:navbar.item>
</flux:navbar>
