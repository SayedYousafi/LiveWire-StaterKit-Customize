@php use Illuminate\Support\Facades\Auth; @endphp

<div class="max-w-7xl mx-auto p-6 space-y-6">
    <div class=" mt-2 text-center">
        @if (session('success'))
        <flux:callout variant="success" icon="check-circle" heading="{{ session('success') }}" />
        @endif
    </div>
    <div class=" mt-2 text-center">
        @if (session('error'))
        <flux:callout variant="danger" icon="check-circle" heading="{{ session('error') }}" />
        @endif
    </div>

    <flux:modal name="myModal" class="md:w-96">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">Update value for taric setting</flux:heading>
                <flux:text class="mt-2">Current values is: {{ $txtValue }}.</flux:text>
            </div>

            <flux:input wire:model='txtValue' label="Value:" placeholder="Enter New value" />

            <div class="flex">
                <flux:spacer />

                <flux:button wire:click='setValue' icon='plus-circle' type="submit" variant="primary">Save changes
                </flux:button>
            </div>
        </div>
    </flux:modal>

    <div class="bg-white rounded-xl shadow p-6">
        <div class="text-center text-xl font-bold mb-6">
            Welcome <a href="{{ url('profile') }}" class="text-blue-600 hover:underline">{{ Auth::user()->name }}</a>,
            you logged in as an Admin
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

            {{-- Administration --}}
            <fieldset class="border border-gray-300 rounded-xl p-4">
                <legend class="text-lg font-semibold text-gray-700 px-2">Administration</legend>
                <div class=" mt-2 text-center">
                    @if (session('success'))
                    <flux:callout variant="success" icon="check-circle" heading="{{ session('success') }}" />
                    @endif
                </div>
                <div class="space-y-2 mt-2">
                    <a href="{{ url('users') }}" class="text-blue-600 hover:underline flex items-center gap-2">
                        <i data-lucide="user-cog" class="w-4 h-4"></i> Administrators and users
                    </a>

                    <a href="{{ route('register') }}" class="text-blue-600 hover:underline flex items-center gap-2">
                        <i data-lucide="user-plus" class="w-4 h-4"></i> Register a new MIS user
                    </a>
                    <a href="#" wire:click='getValue(1)' class="text-blue-600 hover:underline flex items-center gap-2">
                        <i data-lucide="settings" class="w-4 h-4"></i> Set value for taric setting
                    </a>
                </div>
            </fieldset>

            {{-- Reports --}}
            <fieldset class="border border-gray-300 rounded-xl p-4">
                <legend class="text-lg font-semibold text-gray-700 px-2">Exports/Reports</legend>
                <div class="space-y-2 mt-2">
                    <a href="{{ url('export') }}" class="text-blue-600 hover:underline flex items-center gap-2">
                        <i data-lucide="list-checks" class="w-4 h-4"></i>Export Full items list
                    </a>
                    <a href="{{ url('export/isNew') }}" class="text-blue-600 hover:underline flex items-center gap-2">
                        <i data-lucide="list-checks" class="w-4 h-4"></i>Export New items list
                    </a>
                    <a href="{{ url('export/updated') }}" class="text-blue-600 hover:underline flex items-center gap-2">
                        <i data-lucide="users" class="w-4 h-4"></i> Export updated item list
                    </a>
                    <a href="{{ url('confirmed') }}" class="text-blue-600 hover:underline flex items-center gap-2">
                        <i data-lucide="list-checks" class="w-4 h-4"></i>Export confirmed items list
                    </a>

                    <a href="{{ url('WarehouseValue') }}" class="text-blue-600 hover:underline flex items-center gap-2">
                        <i data-lucide="warehouse" class="w-4 h-4"></i> DE Warehouse StockQTY
                    </a>


                </div>
            </fieldset>

            {{-- Downloads --}}
            <fieldset class="border border-gray-300 rounded-xl p-4">
                <legend class="text-lg font-semibold text-gray-700 px-2">Downloads</legend>
                <div class="space-y-2 mt-2">
                    <a href="{{ url('downloadStockQty') }}"
                        class="text-blue-600 hover:underline flex items-center gap-2">
                        <i data-lucide="download" class="w-4 h-4"></i> Download warehouse stockQty
                    </a>
                    <a href="{{ url('download') }}" class="text-blue-600 hover:underline flex items-center gap-2">
                        <i data-lucide="database" class="w-4 h-4"></i> Download MIS Database Backup
                    </a>
                </div>
            </fieldset>

            {{-- Others --}}
            <fieldset class="border border-gray-300 rounded-xl p-4">
                <legend class="text-lg font-semibold text-gray-700 px-2">Others</legend>
                <div class="space-y-2 mt-2">
                    @if ($countOld != 0)
                    <form action="{{ url('oldOrders') }}" method="POST" class="flex flex-col gap-1">
                        @csrf
                        <input type="hidden" name="orderNos" value="{{ json_encode($orderNos) }}">
                        <button class="text-red-600 hover:underline flex items-center gap-2" type="submit">
                            <i data-lucide="trash-2" class="w-4 h-4"></i> Remove MIS old order 1 year
                            <span class="bg-red-100 text-red-700 px-2 py-0.5 rounded-full text-sm font-semibold">
                                {{ $countOld }}
                            </span>
                        </button>
                    </form>
                    @else
                    <div class="text-green-600 flex items-center gap-2">
                        <i data-lucide="check-circle" class="w-4 h-4"></i>
                        Remove MIS old order 1 year
                        <span class="bg-green-100 text-green-700 px-2 py-0.5 rounded-full text-sm font-semibold">
                            {{ $countOld }}
                        </span>
                    </div>
                    @endif

                    <a href="{{ url('pf') }}" class="text-blue-600 hover:underline flex items-center gap-2">
                        <i data-lucide="calculator" class="w-4 h-4"></i> Price factor calculations
                    </a>
                </div>
            </fieldset>

        </div>
    </div>
</div>

{{-- Lucide icons --}}
<script>
    document.addEventListener("DOMContentLoaded", () => {
        if (window.lucide) {
            window.lucide.createIcons();
        }
    });
</script>