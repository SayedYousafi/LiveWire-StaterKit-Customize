<div class="container mx-auto text-center py-4">
    <nav class="space-x-2">
        <a href="{{ route('customers') }}" 
           class="inline-block px-4 py-2 rounded-md bg-gray-200 text-gray-800 hover:bg-gray-300 dark:bg-gray-700 dark:text-gray-100 dark:hover:bg-gray-600"
           wire:navigate>
            Customers
        </a>
        <a href="{{ route('cargos') }}" 
           class="inline-block px-4 py-2 rounded-md bg-blue-500 text-white hover:bg-blue-600 dark:bg-blue-600 dark:hover:bg-blue-700"
           wire:navigate>
            Cargos
        </a>
        <a href="{{ url('cargo-type') }}" 
           class="inline-block px-4 py-2 rounded-md bg-cyan-500 text-white hover:bg-cyan-600 dark:bg-cyan-600 dark:hover:bg-cyan-700"
           wire:navigate>
            Cargos type
        </a>
        <a href="orders.index" 
           class="inline-block px-4 py-2 rounded-md bg-green-500 text-white hover:bg-green-600 dark:bg-green-600 dark:hover:bg-green-700"
           wire:navigate>
            Closed invoices
        </a>
        <a href="{{ url('problems') }}" 
           class="inline-block px-4 py-2 rounded-md bg-yellow-400 text-black hover:bg-yellow-500 dark:bg-yellow-500 dark:text-gray-900 dark:hover:bg-yellow-600"
           wire:navigate>
            Problems
        </a>
    </nav>
</div>
