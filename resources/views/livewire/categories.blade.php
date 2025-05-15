<div>
    <flux:modal.trigger name="myModal" class=" mb-2">
        <flux:button wire:click='cancel' class=" bg-blue-800! text-white! hover:bg-blue-700!" icon='plus-circle'>New
            category</flux:button>
    </flux:modal.trigger>

    <flux:modal name="myModal" class="md:w-96">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">Category detials</flux:heading>
                @if ($isUpdate)
                <div class="flex items-center gap-10 mt-2.5">
                    <flux:switch wire:click="$toggle('enableEdit')" label="Enable edit" />
                </div>
                @endif
            </div>

            <flux:input wire:model='name' label="Category name" placeholder="Category name"
                :disabled="$isUpdate && !$enableEdit" />

            <flux:radio.group wire:model="is_ignored_value" label="Is ignored value?">
                <flux:radio value="1" label="Yes" :disabled="$isUpdate && !$enableEdit" />
                <flux:radio value="0" label="No" :disabled="$isUpdate && !$enableEdit" />
            </flux:radio.group>

            <div class="flex">
                <flux:spacer />

                <flux:button type="button" variant="ghost" icon="x-circle" wire:click="cancel"
                    @click="Flux.modal('myModal').close()">Cancel
                </flux:button>

                <flux:button wire:click='{{ $isUpdate ? "update" : "save" }}' type="submit" variant="primary">
                    {{ $isUpdate ? "Update" : "Add" }}
                </flux:button>

            </div>
        </div>
    </flux:modal>
    <div class=" mt-2 text-center">
        @if (session('success'))
        <flux:callout variant="success" icon="check-circle" heading="{{ session('success') }}" />
        @endif
    </div>
    <div class="relative overflow-x-auto">
        <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400 mt-2.5">
            <thead
                class="sticky top-0  bg-gray-100 text-xs text-gray-700 uppercase dark:bg-gray-700 dark:text-gray-400">
                <tr>
                    <th scope="col" class="px-6 py-3">
                        ID
                    </th>
                    <th scope="col" class="px-6 py-3">
                        Name
                    </th>

                    <th scope="col" class="px-6 py-3">
                        Is ignored value?
                    </th>
                    <th colspan="2" scope="col" class="px-6 py-3">
                        Action
                    </th>
                </tr>
            </thead>
            <tbody>
                @forelse ($categories as $category )
                <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 border-gray-200">

                    <td class="px-2 py-1">
                        {{ $category->id }}
                    </td>
                    <td class="px-2 py-1">
                        {{ $category->name }}
                    </td>
                    <td class="px-2 py-1">
                        {{ $category->is_ignored_value ==1 ? 'Yes' :'No' }}
                    </td>

                    <td class="px-2 py-1">
                        <flux:button variant='primary' icon='pencil-square' wire:click='edit({{ $category->id }})'
                            size='sm'>Edit</flux:button>
                    </td>
                    <td class="px-2 py-1">
                        <flux:button variant='danger' icon='minus-circle' wire:click='delete({{ $category->id }})'
                            wire:confirm='Are you sure deleting this record?' size='sm'>Delete</flux:button>
                    </td>
                </tr>
                @empty
                <tr>
                    <th colspan="5" scope="row"
                        class="px-2 py-1 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                        No records found
                    </th>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

</div>