<div>
    <x-sub-menu />
    <div class=" mt-2 text-center mb-2">
        @if (session('success'))
        <flux:callout variant="success" icon="check-circle" heading="{{ session('success') }}" />
        @endif
    </div>

    <div class=" mt-2 text-center mb-2">
        @if (session('error'))
        <flux:callout variant="danger" icon="x-circle" heading="{{ session('error') }}" />
        @endif
    </div>
    {{-- @include('partials.edit-so') --}}
    @include('partials.cargos')
    @include('partials.edit-qty')
    <div class="relative overflow-x-auto">
        <table class=" table-default">
            <thead class="">
                <tr class="">
                    <th class="">ID
                        <flux:button variant="danger" size='sm' wire:click='cancel' icon='x-circle'></flux:button>
                    </th>
                    <th class="">ID - Customer name</th>
                    <th class="">ID - Cargo No</th>
                    <th class="">Date created</th>
                    <th class="">Count Item </th>
                    <th class="">QTY</th>

                    <th class="">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($invoices as $invoice)
                <tr wire:key="{{ $invoice->cargoId }}"
                    class="border-b dark:border-gray-700 border-gray-200 bg-white dark:bg-gray-800">

                    <td class="">
                        <flux:button class=" bg-gray-500! hover:bg-gray-400! text-white!" size='sm'
                            icon:trailing='arrow-right-circle'
                            wire:click="groupByTaric({{ $invoice->cargoId }},'listByTarics')">
                            {{ $invoice->cargoId }}
                        </flux:button>

                    </td>

                    <td class="">{{ $invoice->customerId }} - {{ $invoice->Name }}</td>
                    <td class="">{{ $invoice->cargo_id }} - {{ $invoice->cargo_no }}</td>
                    <td class="">{{ $invoice->InvoiceDate }}</td>
                    <td class="">
                        <flux:text color='blue' size='lg'>
                            <flux:link href="#" wire:click="groupByItem({{ $invoice->cargoId }}, 'listByItem')">
                                {{ $invoice->CountItemOrder }}
                            </flux:link>
                        </flux:text>
                    </td>
                    <td class="">{{ $invoice->totalQty }}</td>
                    <td class="">
                        <flux:button variant="primary" icon="check-badge"
                            wire:click="verifyAll({{ $invoice->cargoId }})" size="sm">
                            Verify</flux:button>
                    </td>
                    @if (session('verifiedRow') == $invoice->cargoId)
                    <td>
                        <flux:button size='sm' icon='lock-closed' class="bg-blue-600! text-white! hover:bg-blue-500!"
                            wire:click='checkStatus({{ $invoice->cargoId }}, {{ $invoice->taric_id }} , {{ $invoice->customerId }})'>
                            Close Invoice</flux:button>
                    </td>
                    @endif
                </tr>
                @if($tariffNo == $invoice->cargoId)
                <tr>
                    <td colspan="8">@include('partials.taric-invoice')</td>
                </tr>
                @endif
                @if($itemNo == $invoice->cargoId)
                <tr>
                    <td colspan="8">@include('partials.item-invoice')</td>
                </tr>
                @endif
                {{-- Set EUR Price --}}
                @if ($itemToSetId == $invoice->master_id)
                <tr>
                    <td colspan="15" align="center">
                        <flux:heading class="my-4">Set EUR Special Price</flux:heading>

                        <div class="mb-4">
                            <flux:label for="eur_special_price">EUR Special Price:</flux:label>
                            <flux:input type="number" wire:model="eur_special_price" placeholder="Enter price in EUR" />

                            @error('eur_special_price')
                            <flux:text class="text-red-600 text-sm mt-1 block">{{ $message }}</flux:text>
                            @enderror
                        </div>

                        <div class="flex justify-center gap-4 mb-4">
                            <flux:button variant="secondary" icon="x-circle" wire:click="cancel">Cancel</flux:button>
                            <flux:button icon="currency-euro" wire:click="eurSpecialPrice">Set Price</flux:button>
                        </div>
                    </td>
                </tr>
                @endif

                @empty
                <tr>
                    <td colspan="8" class=" text-center font-medium text-gray-900 dark:text-white">
                        No records found
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>