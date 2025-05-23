<div class="max-w-7xl mx-auto p-6 space-y-6">
    <div class="flex justify-between items-center mb-6">
        <flux:heading size="lg">Reports & Control</flux:heading>
        <a href="{{ url()->previous() }}">
            <flux:button color="primary">Back</flux:button>
        </a>
    </div>

    {{-- Currency Rates --}}
    <fieldset class="border border-gray-300 p-4 rounded-xl">
        <legend class="text-xl font-bold text-gray-700 px-2">
            <i data-lucide="dollar-sign" class="inline w-5 h-5 mr-1"></i>
            Today {{ date('Y-m-d') }} Currency Rates
        </legend>
        <div class="grid grid-cols-3 text-center mt-4 font-semibold">
            <div>EUR</div>
            <div>USD</div>
            <div>RMB</div>
        </div>
        <div class="grid grid-cols-3 text-center mt-2">
            @foreach($rates as $rate)
                <div>{{ $rate }}</div>
            @endforeach
        </div>
    </fieldset>

    {{-- Report Sections --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        
        {{-- Orders --}}
        <fieldset class="border border-gray-300 p-4 rounded-xl">
            <legend class="text-xl font-semibold text-gray-700 px-2 flex items-center gap-1">
                <i data-lucide="shopping-bag" class="w-5 h-5"></i> Orders
            </legend>
            <div class="space-y-2 mt-4">
                <x-report-link href="{{ url('orderItems') }}" label="Order items unassigned to cargo" :count="$count_null_cargo" />
                <x-report-link href="{{ url('problems') }}" label="Orders with purchase problem" :count="$purchaseProblem" />
                <x-report-link href="{{ url('problems') }}" label="Orders with Check Problem" :count="$checkProblem" />
                <x-report-link href="{{ url('confirms/date') }}" label="Confirmed items older than 3 months" :count="$oldCount" />
            </div>
        </fieldset>

        {{-- Suppliers --}}
        <fieldset class="border border-gray-300 p-4 rounded-xl">
            <legend class="text-xl font-semibold text-gray-700 px-2 flex items-center gap-1">
                <i data-lucide="truck" class="w-5 h-5"></i> Suppliers
            </legend>
            <div class="space-y-2 mt-4">
                <x-report-link href="{{ url('/supplierItems/noSupplier') }}" label="Items without suppliers" :count="$count_supp" />
                <x-report-link href="{{ url('/supplierItems/zero') }}" label="Items without RMB Price" :count="$zeroRmb" />
            </div>
        </fieldset>

        {{-- Items --}}
        <fieldset class="border border-gray-300 p-4 rounded-xl">
            <legend class="text-xl font-semibold text-gray-700 px-2 flex items-center gap-1">
                <i data-lucide="package-search" class="w-5 h-5"></i> Items
            </legend>
            <div class="space-y-2 mt-4">
                <x-report-link href="{{ url('admin.duplicate') }}" label="Duplicate EAN" :count="0" />
                <x-report-link href="{{ url('noEngVarValue') }}" label="Missing Var Values EN" :count="$countNoEngVarValue" />
                <x-report-link href="{{ url('/noTarics') }}" label="Items with No Taric Code" :count="count($nullTaric)" />
                <x-report-link href="{{ url('/mismatch') }}" label="Items with mismatched tarics" :count="$parents->count()" />
                <x-report-link href="{{ url('/nullCategory') }}" label="Items with null category" :count="$nullCat" />
                <x-report-link href="{{ url('shipping-classNa') }}" label="Items with wrong shipping class (Na)" :count="null" color="red" />
            </div>
        </fieldset>

        {{-- Pictures --}}
        <fieldset class="border border-gray-300 p-4 rounded-xl">
            <legend class="text-xl font-semibold text-gray-700 px-2 flex items-center gap-1">
                <i data-lucide="image" class="w-5 h-5"></i> Pictures
            </legend>
            <div class="space-y-2 mt-4">
                <x-report-link href="{{ url('/npr') }}" label="Is New Picture Required" :count="$countNpr" />
                <x-report-link href="{{ url('/listpix') }}" label="Unused pictures" :count="count($unusedImages)" />
                <x-report-link href="{{ url('itemNoPix') }}" label="Items without picture" :count="$nullPixs->count()" />
                <x-report-link href="{{ url('pics') }}" label="Pictures with multiple cargos" :count="$parents_results->count()" />
                <x-report-link href="{{ url('pics/check') }}" label="Pictures with multiple items" :count="null" />
            </div>
        </fieldset>
    </div>
</div>

<script src="https://unpkg.com/lucide@latest"></script>
<script>
    document.addEventListener("DOMContentLoaded", () => {
        lucide.createIcons();
    });
</script>
