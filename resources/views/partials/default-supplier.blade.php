<flux:modal name="defaultModal" class="md:w-[800px]">
    <div class="space-y-6">
        <div>
            <flux:heading size="lg">Supplier Details</flux:heading>
            @if ($isUpdate)
            <div class="flex items-center gap-10 mt-2.5">
                <flux:switch wire:click="$toggle('enableEdit')" label="Enable edit" />
            </div>
            @endif
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            {{-- Supplier Name --}}
            <div>
                <flux:text class="text-sm font-medium mb-1 block">Supplier Name *</flux:text>
                <flux:select wire:model="supplier_id" :disabled="$isUpdate && !$enableEdit">
                    @foreach ($suppliers as $supplier)
                        <option value="{{ $supplier->id }}">{{ $supplier->id }} - {{ $supplier->name }} - {{ $supplier->name_cn }}</option>
                    @endforeach
                </flux:select>
            </div>

            {{-- Item Name --}}
            <div>
                <flux:text class="text-sm font-medium mb-1 block">Item Name * ({{ $item_id }}) - {{ $id }}</flux:text>
                <flux:input wire:model="item_name" readonly />
            </div>

            {{-- Is Default --}}
            <div>
                <flux:text class="text-sm font-medium mb-1 block">Is Default?</flux:text>
                <flux:select wire:model="is_default" :disabled="$isUpdate && !$enableEdit">
                    <option value="Yes">Yes</option>
                    <option value="No">No</option>
                </flux:select>
            </div>

            {{-- MOQ --}}
            <div>
                <flux:text class="text-sm font-medium mb-1 block">MOQ</flux:text>
                <flux:input wire:model="moq" :disabled="$isUpdate && !$enableEdit" />
            </div>

            {{-- Is PO --}}
            <div>
                <flux:text class="text-sm font-medium mb-1 block">Is PO?</flux:text>
                <flux:select wire:model="is_po" :disabled="$isUpdate && !$enableEdit">
                    <option>Select</option>
                    <option value="No">No</option>
                    <option value="Yes">Yes</option>
                </flux:select>
            </div>

            {{-- Interval --}}
            <div>
                <flux:text class="text-sm font-medium mb-1 block">Interval</flux:text>
                <flux:input wire:model="oi" :disabled="$isUpdate && !$enableEdit" />
            </div>

            {{-- Price RMB --}}
            <div>
                <flux:text class="text-sm font-medium mb-1 block">Price RMB</flux:text>
                <flux:input wire:model="price_rmb" :disabled="$isUpdate && !$enableEdit" />
            </div>

            {{-- Lead Time --}}
            <div>
                <flux:text class="text-sm font-medium mb-1 block">Lead Time</flux:text>
                <flux:input wire:model="lead_time" :disabled="$isUpdate && !$enableEdit" />
            </div>

            {{-- Note CN --}}
            <div class="md:col-span-3">
                <flux:text class="text-sm font-medium mb-1 block">Item Note CN</flux:text>
                <flux:input wire:model="note_cn" :disabled="$isUpdate && !$enableEdit" />
            </div>

            {{-- Item URL --}}
            <div class="md:col-span-3">
                <flux:text class="text-sm font-medium mb-1 block">Item URL</flux:text>
                <flux:input wire:model="url" :disabled="$isUpdate && !$enableEdit" />
            </div>
        </div>

        <div class="flex">
            <flux:spacer />

            <flux:button type="button" variant="ghost" icon="x-circle" wire:click="cancel"
                         @click="Flux.modal('defaultModal').close()">Cancel
            </flux:button>

            <flux:button wire:click='{{ $isUpdate ? "editSuppItem" : "store($item_id)" }}' type="submit" icon='plus-circle' variant="primary">
                {{ $isUpdate ? "Update supplier" : "Insert supplier" }}
            </flux:button>
        </div>
    </div>
</flux:modal>
