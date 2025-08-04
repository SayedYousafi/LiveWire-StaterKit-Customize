<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    @include('partials.head')
</head>

<body class="min-h-screen bg-white dark:bg-zinc-800 container mx-auto">

    <flux:header container class="border-b border-zinc-200 bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-900">
        <flux:sidebar.toggle class="lg:hidden" icon="bars-2" inset="left" />

        <a href="{{ route('dashboard') }}" class="ms-2 me-5 flex items-center space-x-2 rtl:space-x-reverse lg:ms-0"
            wire:navigate>
            <x-app-logo />
        </a>
        <flux:navbar class="-mb-px max-lg:hidden">
            {{-- <flux:navbar.item icon="layout-grid" :href="route('dashboard')"
                :current="request()->routeIs('dashboard')" wire:navigate>
                {{ __('Dashboard') }}
            </flux:navbar.item> --}}

            {{-- <flux:navbar.item icon="clipboard-document" :href="route('orders')"
                :current="request()->routeIs('orders')" wire:navigate>
                {{ __('Orders') }}
            </flux:navbar.item> --}}

            <flux:dropdown position="top">
                <flux:navbar.item icon="clipboard-document" icon:trailing="chevron-down"
                    :current="request()->routeIs('so')">Orders</flux:navbar.item>
                <flux:navmenu>
                    {{-- <flux:menu.item href="#" icon="plus">New item</flux:menu.item>
                    <flux:menu.separator /> --}}
                    <flux:navmenu.item href="{{ route('nso') }}" icon="queue-list" wire:navigate>NSO</flux:navmenu.item>
                    <flux:navmenu.separator />
                    <flux:navmenu.item href="{{ route('so') }}" icon="building-storefront" wire:navigate>Suppliers
                        order</flux:navmenu.item>
                    <flux:navmenu.separator />
                    <flux:navmenu.item href="{{ route('orders') }}" icon="clipboard-document-list" wire:navigate>List
                        orders</flux:navmenu.item>
                    <flux:navmenu.separator />
                    <flux:navmenu.item href="{{ route('orderItems') }}" icon="clipboard-document-list" wire:navigate>
                        List
                        order items </flux:navmenu.item>
                    <flux:navmenu.separator />

                    <flux:navmenu.item href="{{ route('problems') }}" icon="arrow-path-rounded-square" wire:navigate>
                        Problems
                    </flux:navmenu.item>
                </flux:navmenu>
            </flux:dropdown>

            <flux:dropdown position="top">
                <flux:navbar.item icon="banknotes" icon:trailing="chevron-down"
                    :current="request()->routeIs('invoices')">Invoices</flux:navbar.item>
                <flux:navmenu>
                    {{-- <flux:menu.item href="#" icon="plus">New item</flux:menu.item>
                    <flux:menu.separator /> --}}
                    <flux:navmenu.item href="{{ route('invoices') }}" icon="queue-list" wire:navigate>Open invoices
                    </flux:navmenu.item>
                    <flux:navmenu.separator />
                    <flux:navmenu.item href="{{ route('invoicesClosed') }}" icon="gift-top" wire:navigate>Closed
                        invoices</flux:navmenu.item>
                    <flux:navmenu.separator />
                    <flux:navmenu.item href="{{ route('customers') }}" icon="user-plus" wire:navigate>Customers
                    </flux:navmenu.item>
                    <flux:navmenu.separator />
                    <flux:navmenu.item href="{{ route('cargos') }}" icon="rocket-launch" wire:navigate>Cargos
                    </flux:navmenu.item>
                    <flux:navmenu.separator />
                    <flux:navmenu.item href="{{ route('cargotypes') }}" icon="truck" wire:navigate> Cargo Types
                    </flux:navmenu.item>
                    <flux:navmenu.separator />
                    <flux:navmenu.item href="{{ route('packingList') }}" icon="queue-list" wire:navigate>
                        Packing List
                    </flux:navmenu.item>
                    <flux:navmenu.separator />
                </flux:navmenu>
            </flux:dropdown>

            <flux:dropdown position="top">
                <flux:navbar.item icon="book-open-text" icon:trailing="chevron-down"
                    :current="request()->routeIs('items')">Items</flux:navbar.item>
                <flux:menu>
                    {{-- <flux:menu.item href="#" icon="plus">New item</flux:menu.item>
                    <flux:menu.separator /> --}}
                    <flux:menu.item href="{{ route('items') }}" icon="list-bullet" wire:navigate>List items
                    </flux:menu.item>
                    <flux:menu.separator />
                    <flux:menu.item href="{{ route('items') }}/edited" icon="queue-list" wire:navigate>Edited items
                    </flux:menu.item>
                    <flux:menu.separator />
                    <flux:menu.item href="{{ route('items') }}/is_new" icon="star" wire:navigate>List new items
                    </flux:menu.item>
                    <flux:menu.separator />
                    <flux:menu.item href="{{ route('parents') }}" icon="building-storefront" wire:navigate>Parents
                    </flux:menu.item>
                    <flux:menu.separator />
                    <flux:menu.item href="{{ route('tarics') }}" icon="currency-euro" wire:navigate>Tarics
                    </flux:menu.item>
                    <flux:menu.separator />
                    <flux:menu.item href="{{ route('categories') }}" icon="cube" wire:navigate>Category</flux:menu.item>
                    <flux:menu.separator />
                </flux:menu>
            </flux:dropdown>

            <flux:navbar.item icon="user-group" :href="route('suppliers')" :current="request()->routeIs('suppliers')"
                wire:navigate>
                {{ __('Suppliers') }}
            </flux:navbar.item>


            <flux:dropdown position="top">
                <flux:navbar.item icon="layout-grid" icon:trailing="chevron-down"
                    :current="request()->routeIs('controls')">Management</flux:navbar.item>
                <flux:menu>
                    <flux:menu.item href="{{ route('controls') }}" icon="presentation-chart-bar" wire:navigate>Control
                    </flux:menu.item>
                    <flux:menu.separator />
                    <flux:menu.item href="{{ route('admin') }}" icon="wrench" wire:navigate>Admin</flux:menu.item>
                    <flux:menu.separator />
                    <flux:menu.item href="{{ route('teams') }}" icon="user-group" wire:navigate>Team</flux:menu.item>
                    <flux:menu.separator />
                    <flux:menu.item href="{{ url('users') }}" icon="users" wire:navigate>Users</flux:menu.item>
                </flux:menu>
            </flux:dropdown>

        </flux:navbar>

        <flux:spacer />

        <flux:navbar class="me-1.5 space-x-0.5 rtl:space-x-reverse py-0!">
            @auth
            {{ __('Hello') }} {{ Auth::user()->name }} &nbsp; &#128515; &nbsp;
            @endauth
            {{--<flux:tooltip :content="__('Search')" position="bottom">
                <flux:navbar.item class="!h-10 [&>div>svg]:size-5" icon="magnifying-glass" href="#"
                    :label="__('Search')" />
            </flux:tooltip>
            <flux:tooltip :content="__('Repository')" position="bottom">
                <flux:navbar.item class="h-10 max-lg:hidden [&>div>svg]:size-5" icon="folder-git-2"
                    href="https://github.com/laravel/livewire-starter-kit" target="_blank" :label="__('Repository')" />
            </flux:tooltip>
            <flux:tooltip :content="__('Documentation')" position="bottom">
                <flux:navbar.item class="h-10 max-lg:hidden [&>div>svg]:size-5" icon="book-open-text"
                    href="https://laravel.com/docs/starter-kits#livewire" target="_blank" label="Documentation" />
            </flux:tooltip> --}}
        </flux:navbar>

        <!-- Desktop User Menu -->
        <flux:dropdown position="top" align="end">
            @auth
            <flux:profile class="cursor-pointer" :initials="auth()->user()->initials()" />
            @else
            <flux:profile class="cursor-pointer" :initials="'G'" />
            @endauth

            <flux:menu>
                <flux:menu.radio.group>
                    <div class="p-0 text-sm font-normal">
                        <div class="flex items-center gap-2 px-1 py-1.5 text-start text-sm">
                            <span class="relative flex h-8 w-8 shrink-0 overflow-hidden rounded-lg">
                                <span
                                    class="flex h-full w-full items-center justify-center rounded-lg bg-neutral-200 text-black dark:bg-neutral-700 dark:text-white">
                                    @auth
                                    {{ auth()->user()->initials() }}
                                    @else
                                    G
                                    @endauth
                                </span>
                            </span>

                            <div class="grid flex-1 text-start text-sm leading-tight">
                                @auth
                                <span class="truncate font-semibold">{{ auth()->user()->name }}</span>
                                <span class="truncate text-xs">{{ auth()->user()->email }}</span>
                                @else
                                <span class="truncate font-semibold">Guest</span>
                                <span class="truncate text-xs">Not logged in</span>
                                @endauth
                            </div>
                        </div>
                    </div>
                </flux:menu.radio.group>

                <flux:menu.separator />

                @auth
                <flux:menu.radio.group>
                    <flux:menu.item :href="route('settings.profile')" icon="cog" wire:navigate>
                        {{ __('Settings') }}
                    </flux:menu.item>
                </flux:menu.radio.group>

                <flux:menu.separator />

                <form method="POST" action="{{ route('logout') }}" class="w-full">
                    @csrf
                    <flux:menu.item as="button" type="submit" icon="arrow-right-start-on-rectangle" class="w-full">
                        {{ __('Log Out') }}
                    </flux:menu.item>
                </form>
                @endauth
            </flux:menu>
        </flux:dropdown>


    </flux:header>

    <!-- Mobile Menu -->
    <flux:sidebar stashable sticky
        class="lg:hidden border-e border-zinc-200 bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-900">
        <flux:sidebar.toggle class="lg:hidden" icon="x-mark" />

        <a href="{{ route('dashboard') }}" class="ms-1 flex items-center space-x-2 rtl:space-x-reverse" wire:navigate>
            <x-app-logo />
        </a>

        <flux:navlist variant="outline">
            <flux:navlist.group heading="Platform">
                <flux:navlist.item icon="layout-grid" :href="route('dashboard')"
                    :current="request()->routeIs('dashboard')" wire:navigate>
                    {{ __('Dashboard') }}
                </flux:navlist.item>
            </flux:navlist.group>

            <flux:navlist.group heading="Orders">
                <flux:navlist.item icon="queue-list" :href="route('nso')" wire:navigate>NSO</flux:navlist.item>
                <flux:navlist.item icon="building-storefront" :href="route('so')" wire:navigate>Suppliers Order
                </flux:navlist.item>
                <flux:navlist.item icon="clipboard-document-list" :href="route('orders')" wire:navigate>List Orders
                </flux:navlist.item>
                <flux:navlist.item icon="clipboard-document-list" :href="route('orderItems')" wire:navigate>List Order
                    Items</flux:navlist.item>
                <flux:navlist.item icon="arrow-path-rounded-square" :href="route('problems')" wire:navigate>Problems
                </flux:navlist.item>
            </flux:navlist.group>

            <flux:navlist.group heading="Invoices">
                <flux:navlist.item icon="queue-list" :href="route('invoices')" wire:navigate>Open Invoices
                </flux:navlist.item>
                <flux:navlist.item icon="gift-top" :href="route('invoicesClosed')" wire:navigate>Closed Invoices
                </flux:navlist.item>
                <flux:navlist.item icon="user-plus" :href="route('customers')" wire:navigate>Customers
                </flux:navlist.item>
                <flux:navlist.item icon="rocket-launch" :href="route('cargos')" wire:navigate>Cargos</flux:navlist.item>
                <flux:navlist.item icon="truck" :href="route('cargotypes')" wire:navigate>Cargo Types
                </flux:navlist.item>
                <flux:navlist.item icon="queue-list" :href="route('packingList')" wire:navigate>Packing List
                </flux:navlist.item>
            </flux:navlist.group>

            <flux:navlist.group heading="Items">
                <flux:navlist.item icon="list-bullet" :href="route('items')" wire:navigate>List Items
                </flux:navlist.item>
                <flux:navlist.item icon="queue-list" :href="route('items').'/edited'" wire:navigate>Edited Items
                </flux:navlist.item>
                <flux:navlist.item icon="star" :href="route('items').'/is_new'" wire:navigate>List New Items
                </flux:navlist.item>
                <flux:navlist.item icon="building-storefront" :href="route('parents')" wire:navigate>Parents
                </flux:navlist.item>
                <flux:navlist.item icon="currency-euro" :href="route('tarics')" wire:navigate>Tarics</flux:navlist.item>
                <flux:navlist.item icon="cube" :href="route('categories')" wire:navigate>Category</flux:navlist.item>
            </flux:navlist.group>

            <flux:navlist.group heading="Suppliers">
                <flux:navlist.item icon="user-group" :href="route('suppliers')" wire:navigate>Suppliers
                </flux:navlist.item>
            </flux:navlist.group>

            <flux:navlist.group heading="Management">
                <flux:navlist.item icon="presentation-chart-bar" :href="route('controls')" wire:navigate>Control
                </flux:navlist.item>
                <flux:navlist.item icon="wrench" :href="route('admin')" wire:navigate>Admin</flux:navlist.item>
                <flux:navlist.item icon="user-group" :href="route('teams')" wire:navigate>Team</flux:navlist.item>
                <flux:navlist.item icon="users" href="{{ url('users') }}" wire:navigate>Users</flux:navlist.item>
            </flux:navlist.group>
        </flux:navlist>

        <flux:spacer />
    </flux:sidebar>


    {{ $slot }}


    @fluxScripts
    <script src="https://unpkg.com/lucide@latest"></script>

    @stack('scripts')

    <script>
        function renderLucideIcons() {
        if (window.lucide) {
            window.lucide.createIcons();
        }
    }

    document.addEventListener("DOMContentLoaded", renderLucideIcons);

    // Livewire 3 event after navigation (page transitions or component swaps)
    document.addEventListener('livewire:navigated', renderLucideIcons);

    // Livewire 3 event after a component is updated via wire interaction
    document.addEventListener('livewire:updated', renderLucideIcons);
    </script>


</body>

</html>