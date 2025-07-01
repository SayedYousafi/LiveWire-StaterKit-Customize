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
            <div>
                <flux:text icon='euro-currency'></flux:text> EUR
            </div>
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
                <x-report-link href="{{ url('orderItems/noCargo') }}" label="Order items unassigned to cargo"
                    :count="$count_null_cargo" />
                <x-report-link href="{{ url('problems') }}" label="Orders with purchase problem"
                    :count="$purchaseProblem" />
                <x-report-link href="{{ url('problems') }}" label="Orders with Check Problem" :count="$checkProblem" />
                
                <x-report-link href="{{ url('orderItems/specialRMBnoValue') }}" label="RMB Special SET with no value"
                    :count="$specialRmb_noValue" />
                <x-report-link href="{{ url('orderItems/specialEUR_novalue') }}" label="EUR Special SET with no value"
                    :count="$specialEUR_novalue" />
                <x-report-link href="{{ url('orderItems/specialDim_novalue') }}" label="Dimention Special SET with no value"
                    :count="$specialDim_novalue" />
            </div>
        </fieldset>


        {{-- Items --}}
        <fieldset class="border border-gray-300 p-4 rounded-xl">
            <legend class="text-xl font-semibold text-gray-700 px-2 flex items-center gap-1">
                <i data-lucide="package-search" class="w-5 h-5"></i> Items
            </legend>
            <div class="space-y-2 mt-4">

                <x-report-link href="{{ url('items/varval') }}" label="Missing Var Values EN"
                    :count="$countNoEngVarValue" />
                <x-report-link href="{{ url('items/noTarics') }}" label="Items with No Taric Code"
                    :count="count($nullTaric)" />
                <x-report-link href="{{ url('/mismatch') }}" label="Items with mismatched tarics"
                    :count="$parents->count()" />
                <x-report-link href="{{ url('items/noCategory') }}" label="Items with null category"
                    :count="$nullCat" />
                <x-report-link href="{{ url('items/naShipping') }}" label="Items with wrong shipping class (Na)"
                    :count="$naClass->count()" color="red" />
            </div>
        </fieldset>

        
        {{-- Suppliers --}}
        <fieldset class="border border-gray-300 p-4 rounded-xl">
            <legend class="text-xl font-semibold text-gray-700 px-2 flex items-center gap-1">
                <i data-lucide="truck" class="w-5 h-5"></i> Suppliers
            </legend>
            <div class="space-y-2 mt-4">
                <x-report-link href="{{ url('items/noSupplier') }}" label="Items without suppliers"
                    :count="$count_supp" />
                <x-report-link href="{{ url('items/zero') }}" label="Items without RMB Price" :count="$zeroRmb" />
            </div>
        </fieldset>

        {{-- Pictures --}}
        <fieldset class="border border-gray-300 p-4 rounded-xl">
            <legend class="text-xl font-semibold text-gray-700 px-2 flex items-center gap-1">
                <i data-lucide="image" class="w-5 h-5"></i> Pictures
            </legend>
            <div class="space-y-2 mt-4">
                <x-report-link href="{{ url('items/npr') }}" label="Is New Picture Required" :count="$countNpr" />
                <x-report-link href="{{ url('/missingImages') }}" label="List unused pictures"
                    :count="count($unusedImages)" />
                <x-report-link href="{{ url('items/noPics') }}" label="Items without picture"
                    :count="$nullPixs->count()" />
                <x-report-link href="{{ url('items/multipleParentsPics') }}" label="Picture with multiple parents"
                    :count="$parents_results->count()" />
                {{--
                <x-report-link href="{{ url('pics/check') }}" label="Picture with multiple parents" :count="null" />
                --}}
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