<div class="container mx-auto">
    <div class="flex justify-between mt-3">
        <div>
            <flux:modal.trigger name="myModal">
                <flux:button wire:click="cancel" icon="plus-circle" class="bg-blue-800! text-white! hover:bg-blue-700!">
                    New {{ Str::before($title, 's') }}
                </flux:button>
            </flux:modal.trigger>
        </div>

        <flux:text color="blue" class="text-base">{{ $title }}</flux:text>

        <div>
            <flux:input class="md:w-50" wire:model.live="search" icon="magnifying-glass"
                placeholder="Search {{ $title }}" />
        </div>
    </div>

    <flux:modal name="myModal" class="!w-[70rem] max-w-none">
        <div class="space-y-6">
            <flux:heading size="lg">Supplier Details</flux:heading>
            @if ($isUpdate)
            <div class="flex items-center gap-10 mt-2.5">
                <flux:switch wire:click="$toggle('enableEdit')" label="Enable edit" />
            </div>
            @endif
            <div class="grid grid-cols-4 gap-4">
                <div class="col-span-2">
                    <flux:input size="sm" wire:model="name" label="Supplier Name" placeholder="Supplier Name"
                        class="w-full" autofocus :disabled="$isUpdate && !$enableEdit" />
                </div>
                <div class="col-span-2">
                    <flux:input size="sm" wire:model="name_cn" label="Supplier Name CN" placeholder="Chinese name"
                        class="w-full" :disabled="$isUpdate && !$enableEdit" />
                </div>
                <div class="col-span-2">
                    <flux:input size="sm" wire:model="company_name" label="Company Name" placeholder="Company name"
                        class="w-full" :disabled="$isUpdate && !$enableEdit" />
                </div>
                <div class="col-span-2">
                    <flux:input size="sm" wire:model="contact_person" label="Contact Person"
                        placeholder="Supplier contact" class="w-full" :disabled="$isUpdate && !$enableEdit" />
                </div>
                <div class="col-span-2">
                    <flux:input size="sm" wire:model="phone" label="Phone" placeholder="Phone" class="w-full"
                        :disabled="$isUpdate && !$enableEdit" />
                </div>
                <div class="col-span-2">
                    <flux:input size="sm" wire:model="mobile" label="QQ - WeChat" placeholder="QQ - WeChat"
                        class="w-full" :disabled="$isUpdate && !$enableEdit" />
                </div>
                <div class="col-span-2">
                    <flux:input size="sm" wire:model="city" label="City" placeholder="City" class="w-full"
                        :disabled="$isUpdate && !$enableEdit" />
                </div>
                <div class="col-span-2">
                    <flux:input size="sm" wire:model="province" label="Province" placeholder="Province" class="w-full"
                        :disabled="$isUpdate && !$enableEdit" />
                </div>
                <div class="col-span-2">
                    <flux:input size="sm" wire:model="email" label="Email" placeholder="Email" class="w-full"
                        :disabled="$isUpdate && !$enableEdit" />
                </div>
                <div class="col-span-2">
                    <flux:input size="sm" wire:model="extra_note" label="Supplier of item (Product name)"
                        placeholder="Which item supplying?" class="w-full" :disabled="$isUpdate && !$enableEdit" />
                </div>
                <fieldset class="border border-gray-300 dark:border-gray-600 p-6">
                    <legend class="text-lg font-bold px-2 -mt-4 bg-white dark:bg-gray-900 text-black dark:text-white">
                        Bank Information
                    </legend>


                    <div class="col-span-2">
                        <flux:input size="sm" wire:model="bank_name" label="Bank Name" placeholder="Bank name"
                            class="w-full" :disabled="$isUpdate && !$enableEdit" />
                    </div>
                    <div class="col-span-2">
                        <flux:input size="sm" wire:model="account_number" label="Account Number"
                            placeholder="Account number" class="w-full" :disabled="$isUpdate && !$enableEdit" />
                    </div>
                    <div class="col-span-2">
                        <flux:input size="sm" wire:model="beneficiary" label="Beneficiary" placeholder="Beneficiary"
                            class="w-full" :disabled="$isUpdate && !$enableEdit" />
                    </div>
                </fieldset>

<fieldset class="border border-gray-300 dark:border-gray-600 p-6 col-span-2">
    <legend class="text-lg font-bold px-2 -mt-4 bg-white dark:bg-gray-900 text-black dark:text-white">
        Terms of Payment
    </legend>
    <div class="space-y-4">
        <div class="col-span-2 flex items-center gap-4">
            <flux:switch wire:model="deposit" label="Deposit" class="whitespace-nowrap" :disabled="$isUpdate && !$enableEdit" />
            <flux:input size="sm" wire:model="percentage" placeholder="%" class="!w-16"
                :disabled="$isUpdate && !$enableEdit" />
        </div>
        <div class="col-span-2 flex items-center gap-4">
            <flux:switch wire:model="bbgd" label="Balance Before Delivery" class="whitespace-nowrap"
                :disabled="$isUpdate && !$enableEdit" />
            <flux:input size="sm" wire:model="percentage2" placeholder="%" class="!w-16"
                :disabled="$isUpdate && !$enableEdit" />
        </div>
        <div class="col-span-2 flex items-center gap-4">
            <flux:switch wire:model="bagd" label="Balance After Delivery" class="whitespace-nowrap"
                :disabled="$isUpdate && !$enableEdit" />
            <flux:input size="sm" wire:model="percentage3" placeholder="%" class="!w-16"
                :disabled="$isUpdate && !$enableEdit" />
        </div>
    </div>
</fieldset>
                <fieldset class="border border-gray-300 dark:border-gray-600 p-6">
    <legend class="text-lg font-bold px-2 -mt-4 bg-white dark:bg-gray-900 text-black dark:text-white">
        Other Info
    </legend>
    <div class="space-y-4">
        <div class="col-span-2">
            <flux:switch wire:model="is_fully_prepared" label="Is Prepared?" class="whitespace-nowrap"
                :disabled="$isUpdate && !$enableEdit" />
        </div>
        <div class="col-span-2">
            <flux:switch wire:model="is_tax_included" label="Tax Included?" class="whitespace-nowrap"
                :disabled="$isUpdate && !$enableEdit" />
        </div>
        <div class="col-span-2">
            <flux:switch wire:model="is_freight_included" label="Freight Included?" class="whitespace-nowrap"
                :disabled="$isUpdate && !$enableEdit" />
        </div>
        <div class="col-span-2">Select supplier order type:
            <flux:select size="sm" wire:model="order_type_id" placeholder="Select supplier order type:"
                :disabled="$isUpdate && !$enableEdit">
                @foreach ($order_types as $type)
                <flux:select.option value="{{ $type->id }}">{{ $type->type_name }}</flux:select.option>
                @endforeach
            </flux:select>
        </div>
    </div>
</fieldset>
                <div class="col-span-4">
                    <flux:input size="sm" wire:model="website" label="Website" placeholder="Website" class="w-full"
                        :disabled="$isUpdate && !$enableEdit" />
                </div>
                <div class="col-span-4">
                    <flux:input size="sm" wire:model="full_address" label="Company Full Address:" placeholder="Supplier address"
                        class="w-full" :disabled="$isUpdate && !$enableEdit" />
                </div>
            </div>

            <div class="flex">
                <flux:spacer />
                <flux:button type="button" variant="ghost" icon="x-circle" wire:click="cancel"
                    x-on:click="Flux.modal('myModal').close()">Cancel
                </flux:button>
                <flux:button type="submit" wire:click="{{ $isUpdate ? 'update' : 'save' }}" icon="plus-circle"
                    variant="primary">
                    {{ $isUpdate ? 'Save changes' : 'Save' }}
                </flux:button>
            </div>
        </div>
    </flux:modal>


    @if (session('success'))
    <div class="mt-2 text-center">
        <flux:callout variant="success" icon="check-circle" heading="{{ session('success') }}" />
    </div>
    @endif

    <div class="relative overflow-x-auto">
        <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400 mt-2.5">
            <thead class="sticky top-0 bg-gray-100 text-xs text-gray-700 uppercase dark:bg-gray-700 dark:text-gray-400">
                <tr>
                    <th class="px-6 py-3">ID</th>
                    <th class="px-6 py-3">Name - CN name</th>
                    <th class="px-6 py-3">Order Type</th>
                    <th class="px-6 py-3">Contact Person</th>
                    <th class="px-6 py-3">QQ - WeChat</th>
                    <th class="px-6 py-3">Province</th>
                    <th colspan="2" class="px-6 py-3">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($suppliers as $supplier)
                <tr wire:key="{{ $supplier->id }}"
                    class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 border-gray-200">
                    <td class="px-2 py-1">{{ $supplier->id }}
                        <a href="{{ route('items') }}/supplierID-{{ $supplier->id }}"
                            class="text-bold text-blue-700">({{ $supplier->items_count }})</a>
                    </td>
                    <td class="px-2 py-1">{{ $supplier->name }} - {{ $supplier->name_cn }}</td>
                    <td class="px-2 py-1">{{ $supplier->orderType?->type_name }}</td>
                    <td class="px-2 py-1">{{ $supplier->contact_person }}</td>
                    <td class="px-2 py-1">{{ $supplier->phone }} - {{ $supplier->mobile }}</td>
                    <td class="px-2 py-1">{{ $supplier->province }}</td>
                    <td class="px-2 py-1">
                        <flux:button variant="primary" icon="pencil-square" wire:click="edit({{ $supplier->id }})"
                            size="sm">Edit</flux:button>
                    </td>
                    {{-- <td class="px-2 py-1">
                        <flux:button variant="danger" icon="minus-circle" wire:click="delete({{ $supplier->id }})"
                            wire:confirm="Are you sure deleting this record?" size="sm">Delete</flux:button>
                    </td> --}}
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="px-2 py-1 text-center font-medium text-gray-900 dark:text-white">
                        No records found
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>

        <div class="container mx-auto w-100">{{ $suppliers->links() }}</div>
    </div>
</div>