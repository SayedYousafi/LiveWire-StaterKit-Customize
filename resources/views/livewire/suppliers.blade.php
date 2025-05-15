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
                        class="w-full" autofocus :disabled="$isUpdate && !$enableEdit"/>
                </div>
                <div class="col-span-2">
                    <flux:input size="sm" wire:model="name_cn" label="Supplier Name CN" placeholder="Chinese name"
                        class="w-full" :disabled="$isUpdate && !$enableEdit"/>
                </div>
                <div class="col-span-2">
                    <flux:input size="sm" wire:model="company_name" label="Company Name" placeholder="Company name"
                        class="w-full" :disabled="$isUpdate && !$enableEdit"/>
                </div>
                <div class="col-span-2">
                    <flux:input size="sm" wire:model="contact_person" label="Contact Person"
                        placeholder="Supplier contact" class="w-full" :disabled="$isUpdate && !$enableEdit"/>
                </div>
                <div class="col-span-2">
                    <flux:input size="sm" wire:model="phone" label="Phone" placeholder="Phone" class="w-full" :disabled="$isUpdate && !$enableEdit"/>
                </div>
                <div class="col-span-2">
                    <flux:input size="sm" wire:model="mobile" label="QQ - WeChat" placeholder="QQ - WeChat"
                        class="w-full" :disabled="$isUpdate && !$enableEdit"/>
                </div>
                <div class="col-span-2">
                    <flux:input size="sm" wire:model="city" label="City" placeholder="City" class="w-full" />
                </div>
                <div class="col-span-2">
                    <flux:input size="sm" wire:model="province" label="Province" placeholder="Province"
                        class="w-full" :disabled="$isUpdate && !$enableEdit"/>
                </div>
                <div class="col-span-2">
                    <flux:input size="sm" wire:model="email" label="Email" placeholder="Email" class="w-full" :disabled="$isUpdate && !$enableEdit"/>
                </div>

                <div class="col-span-2">
                    <flux:input size="sm" wire:model="extra_note" label="Supplier of item (Product name)"
                        placeholder="Which item supplying?" class="w-full" :disabled="$isUpdate && !$enableEdit"/>
                </div>

                <div class="flex justify-between col-span-4">
                    <div class="col-span-1">
                        <flux:switch wire:model="is_fully_prepared" label="Is Prepared?" :disabled="$isUpdate && !$enableEdit"/>
                    </div>
                    <div class="col-span-1">
                        <flux:switch wire:model="is_tax_included" label="Tax Included?" :disabled="$isUpdate && !$enableEdit"/>
                    </div>
                    <div class="col-span-1">
                        <flux:switch wire:model="is_freight_included" label="Freight Included?" :disabled="$isUpdate && !$enableEdit"/>
                    </div>
                    <div class="col-span-1">
                        <flux:dropdown>
                            <flux:button icon:trailing="chevron-down">
                                {{ $order_type_id ? "Order Type: $order_type_id" : "Order Type" }}
                            </flux:button>
                            <flux:menu>
                                @foreach ($order_types as $type)
                                <flux:menu.item wire:click="$set('order_type_id', {{ $type->id }})" :disabled="$isUpdate && !$enableEdit">{{ $type->type_name
                                    }}</flux:menu.item>
                                <flux:menu.separator />
                                @endforeach
                            </flux:menu>
                        </flux:dropdown>
                    </div>
                </div>
                <div class="col-span-4">
                    <flux:input size="sm" wire:model="website" label="Website" placeholder="Website" class="w-full" :disabled="$isUpdate && !$enableEdit"/>
                </div>
                <div class="col-span-4">
                    <flux:input size="sm" wire:model="full_address" label="Address" placeholder="Supplier address"
                        class="w-full" :disabled="$isUpdate && !$enableEdit"/>
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
                    <td class="px-2 py-1">{{ $supplier->id }}</td>
                    <td class="px-2 py-1">{{ $supplier->name }} - {{ $supplier->name_cn }}</td>
                    <td class="px-2 py-1">{{ $supplier->orderType?->type_name }}</td>
                    <td class="px-2 py-1">{{ $supplier->contact_person }}</td>
                    <td class="px-2 py-1">{{ $supplier->phone }} - {{ $supplier->mobile }}</td>
                    <td class="px-2 py-1">{{ $supplier->province }}</td>
                    <td class="px-2 py-1">
                        <flux:button variant="primary" icon="pencil-square" wire:click="edit({{ $supplier->id }})"
                            size="sm">Edit</flux:button>
                    </td>
                    <td class="px-2 py-1">
                        <flux:button variant="danger" icon="minus-circle" wire:click="delete({{ $supplier->id }})"
                            wire:confirm="Are you sure deleting this record?" size="sm">Delete</flux:button>
                    </td>
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