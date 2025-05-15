<div class="container mx-auto">
    <div class="flex justify-between mt-3">
        <div>
            <flux:modal.trigger name="myModal">
                <flux:button wire:click="cancel" icon="plus-circle" class="bg-blue-800! text-white! hover:bg-blue-700!">
                    New {{ Str::before($title, 's') }}
                </flux:button>
            </flux:modal.trigger>
        </div>
        <div class="flex justify-end items-center gap-4 my-2">
            <flux:text color="blue" class="text-base">{{ $title }}</flux:text>
            <flux:switch wire:click="$toggle('showInactive')" label="Active / InActive" />
        </div>

        <div>
            <flux:input class="md:w-50" wire:model.live="search" icon="magnifying-glass"
                placeholder="Search {{ $title }}" />
        </div>
    </div>

    <flux:modal name="myModal" class="!w-[70rem] max-w-none">
        <div class="space-y-6">
            <flux:heading size="lg">Parent Details</flux:heading>
            @if ($isUpdate)
            <div class="flex items-center gap-10 mt-2.5">
                <flux:switch wire:click="$toggle('enableEdit')" label="Enable edit" />
            </div>
            @endif
            <div class="grid grid-cols-4 gap-4">
                <div class="col-span-1">
                    <flux:input size="sm" wire:model="de_no" label="DE No." placeholder="DE Number" class="w-full"
                        autofocus :disabled="$isUpdate && !$enableEdit" />
                </div>
                <div class="col-span-1">
                    <flux:input size="sm" wire:model="rank" label="Rank" placeholder="Rank value" class="w-full" :disabled="$isUpdate && !$enableEdit" />
                </div>
                <div class="col-span-1">
                    <flux:input size="sm" wire:model="taric_id" label="Taric ID" placeholder="Linked Taric ID"
                        class="w-full" :disabled="$isUpdate && !$enableEdit"/>
                </div>
                <div class="col-span-2">
                    <flux:input size="sm" wire:model="name_en" label="Name (EN)" placeholder="Name in English"
                        class="w-full" :disabled="$isUpdate && !$enableEdit" />
                </div>

                <div class="col-span-2">
                    <flux:input size="sm" wire:model="name_de" label="Name (DE)" placeholder="Name in German"
                        class="w-full" :disabled="$isUpdate && !$enableEdit" />
                </div>
 
                <div class="col-span-1">
                    <flux:input size="sm" wire:model="var_en_1" label="Var EN 1" placeholder="Variant EN 1"
                        class="w-full" :disabled="$isUpdate && !$enableEdit" />
                </div>
                <div class="col-span-1">
                    <flux:input size="sm" wire:model="var_en_2" label="Var EN 2" placeholder="Variant EN 2"
                        class="w-full" :disabled="$isUpdate && !$enableEdit"/>
                </div>
                <div class="col-span-1">
                    <flux:input size="sm" wire:model="var_en_3" label="Var EN 3" placeholder="Variant EN 3"
                        class="w-full" :disabled="$isUpdate && !$enableEdit" />
                </div>

                <div class="col-span-1">
                    <flux:input size="sm" wire:model="var_de_1" label="Var DE 1" placeholder="Variant DE 1"
                        class="w-full" :disabled="$isUpdate && !$enableEdit"/>
                </div>
                <div class="col-span-1">
                    <flux:input size="sm" wire:model="var_de_2" label="Var DE 2" placeholder="Variant DE 2"
                        class="w-full" :disabled="$isUpdate && !$enableEdit"/>
                </div>
                <div class="col-span-1">
                    <flux:input size="sm" wire:model="var_de_3" label="Var DE 3" placeholder="Variant DE 3"
                        class="w-full" :disabled="$isUpdate && !$enableEdit"/>
                </div>

                <div class="flex justify-between col-span-4">
                    <div class="col-span-1">
                        <flux:input size="sm" wire:model="name_cn" label="Name (CN)" placeholder="Name in Chinese"
                            class="w-full" :disabled="$isUpdate && !$enableEdit"/>
                    </div>
                    <div class="col-span-1">
                        <flux:switch wire:model="is_active" label="Is Active?" :disabled="$isUpdate && !$enableEdit"/>
                    </div>
                    <div class="col-span-1">
                        <flux:switch wire:model="is_nwv" label="Is NWV?" :disabled="$isUpdate && !$enableEdit"/>
                    </div>
                    <div class="col-span-1">
                        <flux:switch wire:model="is_var_unilingual" label="Unilingual Var?" :disabled="$isUpdate && !$enableEdit"/>
                    </div>
                </div>
            </div>

            <div class="flex">
                <flux:spacer />
                <flux:button type="button" variant="ghost" icon="x-circle" wire:click="cancel"
                    x-on:click="Flux.modal('myModal').close()">
                    Cancel
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
                    <th class="px-6 py-3">Name EN</th>
                    <th class="px-6 py-3">Name DE</th>
                    <th class="px-6 py-3">Name CN</th>
                    <th class="px-6 py-3">active?</th>
                    <th class="px-6 py-3">Taric</th>

                    <th colspan="3" class="px-6 py-3">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($Parents as $parent)
                <tr wire:key="{{ $parent->id }}"
                    class="border-b dark:border-gray-700 border-gray-200 {{ $parent->is_active == 0 ? 'bg-red-50' : 'bg-white dark:bg-gray-800' }}">

                    {{--
                <tr wire:key="{{ $parent->id }}"
                    class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 border-gray-200"> --}}
                    <td class="px-2 py-1">{{ $parent->id }}</td>
                    <td class="px-2 py-1">{{ $parent->name_en }}</td>
                    <td class="px-2 py-1">{{ $parent->name_de }}</td>
                    <td class="px-2 py-1">{{ $parent->name_cn }}</td>
                    <td class="px-2 py-1">{{ $parent->is_active == 1 ? 'Yes' : 'No' }}</td>
                    <td class="px-2 py-1">{{ $parent->taric_id }}</td>
                    <td class="px-2 py-1">
                        <flux:button variant="primary" icon="pencil-square" wire:click="edit({{ $parent->id }})"
                            size="sm">Edit</flux:button>
                    </td>
                    <td class="px-2 py-1">
                        {{-- <flux:button variant="danger" icon="minus-circle" wire:click="delete({{ $parent->id }})"
                            wire:confirm="Are you sure deleting this record?" size="sm">Delete</flux:button>--}}
                    </td>
                    <td class="px-2 py-1">
                        <flux:button size='sm'>Description</flux:button>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-2 py-1 text-center font-medium text-gray-900 dark:text-white">
                        No records found
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>

        <div class="container mx-auto w-100">{{ $Parents->links() }}</div>
    </div>
</div>