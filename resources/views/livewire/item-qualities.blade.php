<div>
    @session('success')
    <flux:callout variant="success" icon="check-circle" heading="{{session('success')}}" />
    @endsession
    @if (!$isSoRoute)
    <flux:modal.trigger name="edit-quality">
        <flux:button size='sm' class="!bg-blue-700 text-white! !rounded hover:!bg-blue-600 mb-2.5">New item quality
        </flux:button>
    </flux:modal.trigger>
    @endif

    <flux:modal name="edit-quality" class="md:w-96">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">Update Quality of this item</flux:heading>
                <flux:text class="mt-2">Make changes to item quality details.</flux:text>

                @if ($isUpdate)
                <div class="flex items-center gap-10 mt-2.5">
                    <flux:switch wire:click="$toggle('enableEdit')" label="Enable edit" />
                </div>
                @endif
            </div>

            <flux:input wire:model='name' label='Name' placeholder='Quality name'
                :disabled="$isUpdate && !$enableEdit" />

            <flux:input type="file" wire:model='picture' label="Photo:" placeholder="Photo quality pictures"
                :disabled="$isUpdate && !$enableEdit" />

            <flux:textarea wire:model='description' label="Description:" placeholder="Item Description"
                :disabled="$isUpdate && !$enableEdit" />

            <flux:textarea wire:model='full_description' label="Description CN:" placeholder="Item Description in CN"
                :disabled="$isUpdate && !$enableEdit" />

            <div class="flex">
                <flux:spacer />

                <flux:button type="button" variant="ghost" icon="x-circle" wire:click="cancel"
                    @click="Flux.modal('edit-quality').close()">Cancel
                </flux:button>

                <flux:button wire:click='{{ $isUpdate ? "update" : "save" }}' type="submit" variant="primary">
                    {{ $isUpdate ? "Update" : "Add" }}
                </flux:button>
            </div>
        </div>
    </flux:modal>

    <div style="display: flex; gap: 16px;">
        <div style="flex: 0 0 100%;">
            <table class="table-default">
                <thead>
                    <th>ID</th>
                    <th>Item_ID</th>
                    <th>Name</th>
                    <th>Picture</th>
                    <th>Description</th>
                    <th>Description CN</th>
                    <th colspan="2">Action</th>
                    @if (!$isSoRoute) <th>Apply to Parent</th> @endif
                </thead>
                <tbody>
                    @forelse ($qualities as $quality)
                    <tr>
                        <td>{{ $quality->id }}</td>

                        <td>{{ $quality->item_id }}</td>
                        <td>{{ $quality->name }}</td>
                        <td align="center">
                            <a href="{{ Storage::url('pictures/' . $quality->picture) }}" target="_blank">
                                <img src="{{ Storage::url('pictures/' . $quality->picture) }}" alt="Image"
                                    class="w-16 h-16 object-cover rounded border" />
                            </a>
                        </td>

                        <td>{{ $quality->description }}</td>
                        <td>{{ $quality->full_description }}</td>

                        @if ($isSoRoute)
                        <td>
                            @if($quality->confirmed !== 1)
                            <flux:button wire:click='confirm({{ $quality->id}}, {{ $masterId }})'
                                class="rounded !bg-fuchsia-700 text-white! hover:!bg-fuchsia-600" icon='check-circle'
                                size='sm'>Confirm</flux:button>
                            @else
                            <flux:text size='sm' class="flex justify-center items-center gap-4">
                                @php
                                $confirmed = App\Models\Confirm::where('quality_id', $quality->id)->first();
                                @endphp
                                @if ($confirmed->issues===1)
                                <flux:button size='sm' icon='x-mark' variant='danger'></flux:button>
                                <p class="text-center">
                                    Confirmed <br>
                                    {{ $quality->name }}-QTY => {{ $confirmed->poorQty }} <br>
                                    Remarks => {{ $confirmed->remarks }}
                                </p>                                
                                @elseif ($confirmed->issues===null && $confirmed->poorQty ===null)
                                <flux:button size='sm' icon='check' variant='primary'></flux:button>
                                @endif
                                <flux:button wire:click='confirm({{ $confirmed->quality_id}}, {{ $confirmed->m_id }})'
                                    size='sm' icon='pencil-square' variant='filled'>Edit</flux:button>
                            </flux:text>
                            @endif
                        </td>
                        @endif
                        @if (!$isSoRoute) <td>
                            <flux:button wire:click='edit({{ $quality->id }})' size='sm' variant='primary'
                                icon='pencil-square'>Edit</flux:button>
                        </td>
                        <td>
                            <flux:button wire:click='delete({{ $quality->id }})' wire:confirm='Are you sure deleting'
                                size='sm' variant='danger' icon='minus-circle'>Delete</flux:button>
                        </td>
                        <td>
                            <flux:button wire:click="applyToParent({{ $quality->id }}, {{ $quality->item_id }})"
                                wire:confirm='Are you sure, applying to Parent?' size='sm'
                                class="px-4 py-1 bg-gray-800! text-white! rounded! hover:bg-gray-700!">Apply
                            </flux:button>
                        </td> @endif
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7">No records found</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <flux:modal name="modalConfirm" class="!md:w-96">
            <div class="space-y-6">
                <div>
                    <flux:heading size="lg">Recording Confirm</flux:heading>
                    {{-- <flux:text class="mt-2">Make changes to QTY.</flux:text> --}}
                </div>
                @isset($selectedQyality)

                <div>
                    <a href="{{ Storage::url('pictures/' . $selectedQyality->picture) }}" target="_blank">
                        <img src="{{ Storage::url('pictures/' . $selectedQyality->picture) }}" alt="Image"
                            class="w-100 h-50 object-cover rounded border" />
                    </a>
                </div>
                <div>{{ $selectedQyality->id }}</div>
                <div>{{ $qlyName }}</div>
                <div>{{ $selectedQyality->description }}</div>
                @endisset

                <flux:input wire:model.live='poorQty' label="Enter  {{ $qlyName }} QTY:"
                    placeholder="Confirm {{ $qlyName }} Qty" autofocus/>

                <flux:textarea wire:model='txtProblem' rows="3" label='Short description of confirmation:' 
                    placeholder="Short description of confirm" />

                <div class="flex">
                    <flux:spacer />

                    <flux:button wire:click='createConfirm' icon='inbox' type="submit" variant="primary">Confirm
                    </flux:button>
                </div>
            </div>
        </flux:modal>

    </div>