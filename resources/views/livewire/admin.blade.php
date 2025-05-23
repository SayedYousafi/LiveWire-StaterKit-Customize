@php use Illuminate\Support\Facades\Auth; @endphp

<div class="max-w-7xl mx-auto p-6 space-y-6">

    @if(session()->has('success'))
        <div class="bg-green-100 text-green-800 text-center py-3 px-4 rounded-lg font-semibold">
            {{ session()->get('success') }}
        </div>
    @endif

    @if(session()->has('error'))
        <div class="bg-red-100 text-red-800 text-center py-3 px-4 rounded-lg font-semibold">
            {{ session()->get('error') }}
        </div>
    @endif

    <div class="bg-white rounded-xl shadow p-6">
        <div class="text-center text-xl font-bold mb-6">
            Welcome <a href="{{ url('profile') }}" class="text-blue-600 hover:underline">{{ Auth::user()->name }}</a>, you logged in as an Admin
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

            {{-- Administration --}}
            <fieldset class="border border-gray-300 rounded-xl p-4">
                <legend class="text-lg font-semibold text-gray-700 px-2">Administration</legend>
                <div class="space-y-2 mt-2">
                    <a href="{{ url('users') }}" class="text-blue-600 hover:underline flex items-center gap-2">
                        <i data-lucide="user-cog" class="w-4 h-4"></i> Administrators and users
                    </a>
                    <a href="{{ url('employees') }}" class="text-blue-600 hover:underline flex items-center gap-2">
                        <i data-lucide="users" class="w-4 h-4"></i> GTech Team members list
                    </a>
                    <a href="{{ url('reg') }}" class="text-blue-600 hover:underline flex items-center gap-2">
                        <i data-lucide="user-plus" class="w-4 h-4"></i> Register a new MIS user
                    </a>
                </div>
            </fieldset>

            {{-- Reports --}}
            <fieldset class="border border-gray-300 rounded-xl p-4">
                <legend class="text-lg font-semibold text-gray-700 px-2">Reports</legend>
                <div class="space-y-2 mt-2">
                    <a href="{{ url('orders.confirms') }}" class="text-blue-600 hover:underline flex items-center gap-2">
                        <i data-lucide="list-checks" class="w-4 h-4"></i> Confirm items list
                    </a>
                    <a href="{{ url('shortdesc/3') }}" class="text-blue-600 hover:underline flex items-center gap-2">
                        <i data-lucide="file-text" class="w-4 h-4"></i> Short Descriptions
                    </a>
                    <a href="{{ url('WarehouseValue') }}" class="text-blue-600 hover:underline flex items-center gap-2">
                        <i data-lucide="warehouse" class="w-4 h-4"></i> DE Warehouse StockQTY
                    </a>
                    <a href="{{ url('pf') }}" class="text-blue-600 hover:underline flex items-center gap-2">
                        <i data-lucide="calculator" class="w-4 h-4"></i> Price factor calculations
                    </a>
                    <a href="{{ url('values') }}" class="text-blue-600 hover:underline flex items-center gap-2">
                        <i data-lucide="settings" class="w-4 h-4"></i> Set value for Taric setting
                    </a>
                </div>
            </fieldset>

            {{-- Downloads --}}
            <fieldset class="border border-gray-300 rounded-xl p-4">
                <legend class="text-lg font-semibold text-gray-700 px-2">Downloads</legend>
                <div class="space-y-2 mt-2">
                    <a href="{{ url('downloadStockQty') }}" class="text-blue-600 hover:underline flex items-center gap-2">
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
