<flux:modal name="edit-so" class="md:w-96">
    <div class="space-y-6">
        <div>
            <flux:heading size="lg">Update Supplier order</flux:heading>
            <flux:text class="mt-2">Make changes to this supplier order details.</flux:text>
        </div>
        <div class="flex">
            <div>
                <input type="hidden" wire:model="supplierId" id="supplierId" />
                <flux:dropdown>
                    <flux:button icon:trailing="chevron-down">
                        {{ $supplierId ? "Select Supplier: $supplierId" : 'Select Supplier' }}
                    </flux:button>
                    <flux:menu>
                        @foreach ($suppliers as $supplier)
                        <flux:menu.item wire:click="$set('supplierId', {{ $supplier->id }})">
                            {{ $supplier->id }} - {{ $supplier->name }} - {{ $supplier->name_cn }}
                        </flux:menu.item>
                        <flux:menu.separator />
                        @endforeach
                    </flux:menu>
                </flux:dropdown>
            </div>

            <div class="col-span-1">
                <flux:dropdown>
                    <flux:button icon:trailing="chevron-down">
                        {{ $order_type_id ? "Select Order Type: $order_type_id" : "Order Type" }}
                    </flux:button>
                    <flux:menu>
                        @foreach ($order_types as $type)
                        <flux:menu.item wire:click="$set('order_type_id', {{ $type->id }})">
                            {{ $type->type_name }}
                        </flux:menu.item>
                        <flux:menu.separator />
                        @endforeach
                    </flux:menu>
                </flux:dropdown>
            </div>
        </div>

        <flux:input wire:model="ref_no" label="Reference No:" placeholder="Reference No" autofocus />

        <flux:textarea wire:model='remark' label='Supplier order remark:' placeholder='Enter supplier order remark' />
        <div class="flex">
            <flux:spacer />
            <flux:button type="submit" wire:click='updateSO' variant="primary">Save changes</flux:button>
        </div>
    </div>
</flux:modal>