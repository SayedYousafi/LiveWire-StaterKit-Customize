<div class="p-6 bg-white dark:bg-gray-900 rounded shadow">
    @if(session('success'))
    <flux:callout variant='success' heading="{{ session('success') }}"/>
    
    @endif

    @php
    $disabled = ($itemDetail->parent_no_de == 'NONE') ? 'disabled' : '';
    @endphp
    <div class="text-center text-lg font-bold mb-4">
        Editing item info: {{ $itemDetail->item_id }} - {{ $itemDetail->item_name }}
        <flux:button class="ml-4 px-3 py-1 bg-blue-600 text-white rounded hover:bg-blue-700"
            onclick="history.back()">Back</flux:button>
    </div>

    <!-- Item Section -->
    <fieldset class="border border-gray-300 dark:border-gray-600 p-4 rounded">
        <legend class="text-lg font-semibold px-2 bg-white dark:bg-gray-900 text-black dark:text-white">Item</legend>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-2">
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">EAN</label>
                <div class="mt-1 text-gray-900 dark:text-gray-100">{{ $itemDetail->ean }}</div>
            </div>
            <div>
                <flux:input wire:model="item_name" label="Item Name" placeholder="Item Name" class="w-full" />
            </div>
            <div>
                <flux:input wire:model="item_name_cn" label="Item Name CN" placeholder="Item Name CN" class="w-full" />
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">Category</label>
                <flux:dropdown>
                    <flux:button icon:trailing="chevron-down">
                        current category: {{ $categories[$cat_id] ?? 'None' }}
                    </flux:button>
                    <flux:menu>
                        @foreach ($categories as $id => $name)
                        <flux:separator />
                        <flux:menu.item icon="plus" wire:click="$set('cat_id', '{{ $id }}')">
                            {{ $id }} - {{ $name }}
                        </flux:menu.item>
                        @endforeach
                    </flux:menu>
                </flux:dropdown>
            </div>
            <div>
                <flux:input wire:model="model" label="Model" placeholder="Model" class="w-full" />
            </div>
            <div>
                <flux:input wire:model="remark" label="Remarks" placeholder="Remarks" class="w-full" />
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">Is Active?</label>
                <flux:dropdown>
                    <flux:button icon:trailing="chevron-down">
                        {{ $isActive === 'Y' ? 'Yes' : ($isActive === 'N' ? 'No' : 'Select status') }}
                    </flux:button>
                    <flux:menu>
                        <flux:menu.item wire:click="$set('isActive', 'Y')">Yes</flux:menu.item>
                        <flux:menu.item wire:click="$set('isActive', 'N')">No</flux:menu.item>
                    </flux:menu>
                </flux:dropdown>
            </div>
        </div>
        <div class="text-right mt-4">
            <flux:button wire:click='editNames({{ $itemDetail->item_id }})'
                class="px-4 py-2 bg-gray-800! text-white! rounded! hover:bg-gray-700!">Save</flux:button>
        </div>
        @if($successMessage === 'Items value saved successfully.')
        <flux:callout variant="success" heading="{{ $successMessage }}" class="mt-2" />
        @endif
    </fieldset>

    <!-- Parent Section -->
    <fieldset class="border border-gray-300 dark:border-gray-600 p-4 mt-6 rounded">
        <legend class="text-lg font-semibold px-2 bg-white dark:bg-gray-900 text-black dark:text-white">Parent</legend>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">Parent No DE</label>
                <div class="mt-1 text-gray-900 dark:text-gray-100">{{ $itemDetail->parent_no_de }}</div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">Parent Name DE</label>
                <div class="mt-1 text-gray-900 dark:text-gray-100">{{ $itemDetail->de_name }}</div>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">Parent Name EN</label>
                <div class="mt-1 text-gray-900 dark:text-gray-100">{{ $itemDetail->en_name }}</div>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">Is Active</label>
                <div class="mt-1 text-gray-900 dark:text-gray-100">{{ $itemDetail->is_active }}</div>
            </div>
        </div>
    </fieldset>
    <div class="flex justify-between gap-2">
        <!-- Variation & Values Section -->
        
        <fieldset class="border border-gray-300 dark:border-gray-600 p-4 mt-6 rounded">
            @if($successMessage === 'Varation values updated successfully !!!')
            <flux:callout variant="success" heading="{{ $successMessage }}" class="mb-3" />
        @endif 
            <legend class="text-lg font-semibold px-2 bg-white dark:bg-gray-900 text-black dark:text-white">
                Variation & Values DE
            </legend>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-2">
                <div class="col-span-1">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">Variation</label>
                    <div class="text-gray-900 dark:text-gray-100">
                        {{ $itemDetail->var_de_1 }}<br>
                        {{ $itemDetail->var_de_2 }}<br>
                        {{ $itemDetail->var_de_3 }}
                    </div>
                </div>
                <div class="col-span-2">
                    <flux:input wire:model="value_de" label="Value DE" placeholder="Enter value" {{ $disabled }} />
                    @if($itemDetail->value_de_2 != '')
                    <flux:input wire:model="value_de_2" label="Value DE 2" placeholder="Enter value" {{ $disabled }} />
                    @endif
                    @if($itemDetail->value_de_3 != '')
                    <flux:input wire:model="value_de_3" label="Value DE 3" placeholder="Enter value" {{ $disabled }} />
                    @endif
                </div>
            </div>
            <div class="text-right mt-4">
                <flux:button wire:click="editValues('{{ $itemDetail->item_id}}')"
                    class="px-4 py-2 bg-gray-800! text-white! rounded! hover:bg-gray-700!">Save</flux:button>
            </div>
        </fieldset>

        <fieldset class="border border-gray-300 dark:border-gray-600 p-4 mt-6 rounded">

            <legend class="text-lg font-semibold px-2 bg-white dark:bg-gray-900 text-black dark:text-white">Variation &
                Values EN
            </legend>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-2">
                <div class="col-span-2">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">Variation</label>
                    <div class="text-gray-900 dark:text-gray-100">
                        {{ $itemDetail->var_en_1 }}<br>
                        {{ $itemDetail->var_en_2 }}<br>
                        {{ $itemDetail->var_en_3 }}
                    </div>
                </div>
                <div class="col-span-2">
                    <flux:input wire:model="value_en" label="Value EN" placeholder="Enter value" {{ $disabled }} />
                    @if($itemDetail->value_en_2 != '')
                    <flux:input wire:model="value_en_2" label="Value EN 2" placeholder="Enter value" {{ $disabled }} />
                    @endif
                    @if($itemDetail->value_en_3 != '')
                    <flux:input wire:model="value_en_3" label="Value EN 3" placeholder="Enter value" {{ $disabled }} />
                    @endif
                </div>
            </div>
            <div class="text-right mt-4">
                <flux:button wire:click='editValues({{ $itemDetail->item_id}})'
                    class="px-4 py-2 bg-gray-800! text-white! rounded! hover:bg-gray-700!" >Save</flux:button>
            </div>
        </fieldset>

        <!-- Special Item Section -->
        <fieldset class="border border-gray-300 dark:border-gray-600 p-4 mt-6 rounded">
        @if($successMessage === 'Special price set successfully !!!')
            <flux:callout variant="success" heading="{{ $successMessage }}" class="mb-3" />
        @endif 
            <legend class="text-lg font-semibold px-2 bg-white dark:bg-gray-900 text-black dark:text-white">Is Special Item?
            </legend>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-2">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">EUR Special</label>
                    <div class="text-gray-900 dark:text-gray-100">{{ $itemDetail->is_eur_special }}</div>
                    <flux:dropdown>
                        <flux:button icon:trailing="chevron-down">Change to:
                            {{-- {{ $is_eur_special === 'Y' ? 'Yes' : ($is_eur_special === 'N' ? 'No' : 'Select') }}  --}}
                        </flux:button>
                        <flux:menu>
                            <flux:menu.item wire:click="$set('is_eur_special', 'Y')">Yes</flux:menu.item>
                            <flux:menu.item wire:click="$set('is_eur_special', 'N')">No</flux:menu.item>
                        </flux:menu>
                    </flux:dropdown>
                    {{ $is_eur_special }}
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">RMB Special</label>
                    <div class="text-gray-900 dark:text-gray-100">{{ $itemDetail->is_rmb_special }}</div>
                    <flux:dropdown>
                        <flux:button icon:trailing="chevron-down">Change to:
                            {{-- {{ $is_rmb_special === 'Y' ? 'Yes' : ($is_rmb_special === 'N' ? 'No' : 'Select') }}  --}}
                        </flux:button>
                        <flux:menu>
                            <flux:menu.item wire:click="$set('is_rmb_special', 'Y')">Yes</flux:menu.item>
                            <flux:menu.item wire:click="$set('is_rmb_special', 'N')">No</flux:menu.item>
                        </flux:menu>
                    </flux:dropdown>
                    {{ $is_rmb_special }}
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">EK Net</label>
                    <div class="text-gray-900 dark:text-gray-100">
                        @php $EK_net = EK_net($itemDetail->price_rmb, $itemDetail->cat_id); @endphp
                        {{ $EK_net }}
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">Price RMB</label>
                    <div class="text-gray-900 dark:text-gray-100">{{ $itemDetail->price_rmb }}</div>
                </div>
            </div>
            <div class="text-right mt-4">
                <flux:button wire:click="setPrice({{ $itemDetail->item_id }})"
                    class="px-4 py-2 bg-gray-800! text-white! rounded! hover:bg-gray-700!">Save</flux:button>
            </div>
        </fieldset>
    </div>
    <!-- Dimensions / Others Section -->
<fieldset class="border border-gray-300 dark:border-gray-600 p-4 mt-6 rounded">
            @if($successMessage === 'Item Dimentions Updated Successfully !!!')
            <flux:callout variant="success" heading="{{ $successMessage }}" class="mb-3" />
        @endif 
    <legend class="text-lg font-semibold px-2 bg-white dark:bg-gray-900 text-black dark:text-white">
        Dimensions / Others
    </legend>

    <div class="grid gap-2 grid-cols-[repeat(auto-fit,minmax(6rem,1fr))]">
        <flux:input class="w-24!" wire:model="weight" label="Weight" placeholder="Weight" />
        <flux:input class="w-24!" wire:model="length" label="Length" placeholder="Length" />
        <flux:input class="w-24!" wire:model="width" label="Width" placeholder="Width" />
        <flux:input class="w-24!" wire:model="height" label="Height" placeholder="Height" />
        <flux:input class="w-24!" wire:model="is_qty_dividable" label="Is QTY Dividable" placeholder="Yes/No" />
        <flux:input class="w-24!" wire:model="isbn" label="ISBN" placeholder="ISBN" />
        <flux:input class="w-24!" wire:model="many_components" label="MC" placeholder="Many Components" />
        <flux:input class="w-24!" wire:model="effort_rating" label="ER" placeholder="Effort Rating" />
        <flux:input class="w-24!" wire:model="is_pu_item" label="Is PU" placeholder="Yes/No" />
        <flux:input class="w-24!" wire:model="is_meter_item" label="Is Meter" placeholder="Yes/No" />
        <flux:input class="w-24!" wire:model="foq" label="FOQ" placeholder="FOQ" />

        <div class="w-24!">
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">Is New?</label>
            <flux:dropdown>
                <flux:button icon:trailing="chevron-down">
                    {{ $is_new === 'Y' ? 'Yes' : ($is_new === 'N' ? 'No' : '---') }}
                </flux:button>
                <flux:menu>
                    <flux:menu.item wire:click="$set('is_new', 'Y')">Yes</flux:menu.item>
                    <flux:menu.item wire:click="$set('is_new', 'N')">No</flux:menu.item>
                </flux:menu>
            </flux:dropdown>
        </div>
    </div>

    <div class="mt-4">
        <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">Taric</label>
        <flux:dropdown>
            <flux:button icon:trailing="chevron-down">
                {{ $tarics->firstWhere('id', $taric_id)?->code ?? 'Select Taric' }}
            </flux:button>
            <flux:menu>
                @foreach ($tarics as $taric)
                    <flux:menu.item wire:click="$set('taric_id', {{ $taric->id }})">
                        {{ $taric->id }} - {{ $taric->code }} - {{ $taric->name_en }}
                    </flux:menu.item>
                @endforeach
            </flux:menu>
        </flux:dropdown>
    </div>

    <div class="text-right mt-4">
        <flux:button wire:click='editDimentions({{ $itemDetail->item_id }})'
                class="px-4 py-2 bg-gray-800! text-white! rounded! hover:bg-gray-700!">Save</flux:button>
    </div>
</fieldset>

    <!-- Warehouse Item Section -->
<fieldset class="border border-gray-300 dark:border-gray-600 p-4 mt-6 rounded">
        @if($successMessage === 'Warehouse Items Updated Successfully !!!')
            <flux:callout variant="success" heading="{{ $successMessage }}" class="mb-3" />
        @endif 
    <legend class="text-lg font-semibold px-2 bg-white dark:bg-gray-900 text-black dark:text-white">Warehouse Item</legend>

    <div class="grid gap-1 grid-cols-[repeat(auto-fit,minmax(6rem,1fr))]">
        <flux:input class="w-24!" wire:model="ItemID_DE" label="ID DE" placeholder="ID DE" disabled />
        <flux:input class="w-24!" wire:model="item_no_de" label="NO DE" placeholder="NO DE" />
        <flux:input  wire:model="item_name_de" label="Name DE" placeholder="Name DE" />
        <flux:input  wire:model="item_name_en" label="Name EN" placeholder="Name EN" />
        <flux:input class="w-24!" wire:model="is_stock_item" label="isStock" placeholder="isStock" />
        <flux:input class="w-24!" wire:model="stock_qty" label="Qty" placeholder="Qty" disabled />
        <flux:input class="w-24!" wire:model="is_active" label="isActive" placeholder="isActive" />
        <flux:input class="w-24!" wire:model="msq" label="MSQ" placeholder="MSQ" />
        <flux:input class="w-24!" wire:model="is_no_auto_order" label="isNAOi" placeholder="isNAOi" />
        <flux:input class="w-24!" wire:model="buffer" label="Buffer" placeholder="Buffer" />

        <div class="w-24!">
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">isSnS</label>
            <flux:dropdown>
                <flux:button icon:trailing="chevron-down">
                    {{ $is_SnSI === 'Y' ? 'Yes' : ($is_SnSI === 'N' ? 'No' : 'Select') }}
                </flux:button>
                <flux:menu>
                    <flux:menu.item wire:click="$set('is_SnSI', 'Y')">Yes</flux:menu.item>
                    <flux:menu.item wire:click="$set('is_SnSI', 'N')">No</flux:menu.item>
                </flux:menu>
            </flux:dropdown>
        </div>
    </div>

    <div class="text-right mt-4">
        <flux:button wire:click='editWareHouse({{ $itemDetail->ean }})'
            class="px-4 py-2 bg-gray-800! text-white! rounded! hover:bg-gray-700!">Save</flux:button>
    </div>
</fieldset>

<!-- Default Supplier Section -->
<fieldset class="border border-gray-300 dark:border-gray-600 p-4 mt-6 rounded">
        @if($successMessage === 'Supplier Updated Successfully !!!')
            <flux:callout variant="success" heading="{{ $successMessage }}" class="mb-3" />
        @endif 
    <legend class="text-lg font-semibold px-2 bg-white dark:bg-gray-900 text-black dark:text-white">Default Supplier</legend>

     <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-2">
        <flux:input  wire:model="name" label="Supplier" placeholder="Supplier name" />
        <flux:input  wire:model="province" label="Province" placeholder="Province" />
        <flux:input  wire:model="full_address" label="Address" placeholder="Full address" />
        <flux:input  wire:model="contact_person" label="Contact person" placeholder="Contact person" />
    </div>

    <div class="text-right mt-4">
        <flux:button class="px-4 py-2 bg-gray-800! text-white! rounded! hover:bg-gray-700!"
            wire:click='editSupplier({{ $itemDetail->supplier_id }})'>Save</flux:button>
    </div>
</fieldset>

    <!-- Supplier Item Section -->
    <fieldset class="border border-gray-300 dark:border-gray-600 p-4 mt-6 rounded">
        @if($successMessage === 'Default Supplier item Updated Successfully !!!')
            <flux:callout variant="success" heading="{{ $successMessage }}" class="mb-3" />
        @endif 
        <legend class="text-lg font-semibold px-2 bg-white dark:bg-gray-900 text-black dark:text-white">Supplier Item
        </legend>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-2">
            <flux:input wire:model="price_rmb" label="Price RMB" placeholder="Price RMB" />
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">isPO - </label>
                <flux:dropdown>
                    <flux:button icon:trailing="chevron-down">
                        {{ $is_po === 'Yes' ? 'Yes' : ($is_po === 'No' ? 'No' : 'Select') }}
                    </flux:button>
                    <flux:menu>
                        <flux:menu.item wire:click="$set('is_po', 'Yes')">Yes</flux:menu.item>
                        <flux:menu.item wire:click="$set('is_po', 'No')">No</flux:menu.item>
                    </flux:menu>
                </flux:dropdown>
            </div>
            
            <flux:input wire:model="moq" label="MOQ" placeholder="Minimum order quantity" />
            <flux:input wire:model="oi" label="Interval" placeholder="Interval" />
            <flux:input wire:model="lead_time" label="Lead time" placeholder="Lead time" />
            <flux:textarea wire:model="note_cn" label="Note CN" placeholder="Chinese note or description" rows="auto" />
            <flux:textarea wire:model="url" label="Item URL" placeholder="Enter supplier item URL" rows="auto" />
        </div>
        <div class="text-right mt-4">
            <flux:button class="px-4 py-2 bg-gray-800! text-white! rounded! hover:bg-gray-700!"
                wire:click='editSuppItem({{ $itemDetail->supp_id }})'>Save</flux:button>
        </div>
    </fieldset>

    <!-- Picture Item Section -->
    <fieldset class="border border-gray-300 dark:border-gray-600 p-4 mt-6 rounded">
        @if($successMessage === 'Pictures applied on Parent successfully !!!')
            <flux:callout variant="success" heading="{{ $successMessage }}" class="mb-3" />
        @endif 
        <legend class="text-lg font-semibold px-2 bg-white dark:bg-gray-900 text-black dark:text-white">Picture Item
        </legend>
        <div class="flex justify-center mb-4">
            <img src="{{ asset('storage/'.$itemDetail->photo) }}" class="max-w-xl rounded shadow" />
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-2">
            <div>
                <livewire:image-selector :itemId="$itemDetail->item_id"/>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Apply same pictures for these Var
                    Values:</p>
                <div class="space-y-2">
                    <div class="flex items-center justify-between">
                        <flux:input wire:model="value_de" label="Value DE" disabled />
                        <input type="radio" wire:model.live='var_values' value="{{ $value_de }}" />
                    </div>
                    @if($itemDetail->value_de_2 != '')
                    <div class="flex items-center justify-between">
                        <flux:input wire:model="value_de_2" label="Value DE 2" disabled />
                        <input type="radio" wire:model.live='var_values' value="{{ $value_de_2 }}" />
                    </div>
                    @endif
                    @if($itemDetail->value_de_3 != '')
                    <div class="flex items-center justify-between">
                        <flux:input wire:model="value_de_3" label="Value DE 3" disabled />
                        <input type="radio" wire:model.live='var_values' value="{{ $value_de_3 }}" />
                    </div>
                    @endif
                </div>
                <div class="mt-2 text-sm text-gray-600 dark:text-gray-400">Values selected: <span
                        class="font-semibold">{{ $var_values }}</span></div>
                <div class="text-right mt-2">
                    <flux:button class="px-4 py-1 bg-gray-800! text-white! rounded! hover:bg-gray-700!"
                        wire:click="getValues('{{ $itemDetail->par_id }}')">Apply</flux:button>
                </div>
            </div>

            <div>
                <p class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Apply this picture to this parent
                    of all child</p>
                <flux:input value="{{ $itemDetail->parent_no_de }}" label="Parent No DE" disabled />
                <div class="mt-1 text-sm text-gray-400">id: {{ $itemDetail->par_id }}</div>
                <div class="text-right mt-2">
                    <flux:button class="px-4 py-1 bg-gray-800! text-white! rounded! hover:bg-gray-700!"
                        wire:click="applyPicParent({{$itemDetail->par_id }})">Apply</flux:button>
                </div>
            </div>
        </div>
    </fieldset>

    <!-- Paths and isNPR Section -->
    <fieldset class="border border-gray-300 dark:border-gray-600 p-4 mt-6 rounded">
        @if($successMessage === 'Item pix paths updated successfully !!!')
            <flux:callout variant="success" heading="{{ $successMessage }}" class="mb-3" />
        @endif 
        <legend class="text-lg font-semibold px-2 bg-white dark:bg-gray-900 text-black dark:text-white">Pictures paths & NPR
        </legend>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-2">
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">isNPR</label>
                <flux:dropdown>
                    <flux:button icon:trailing="chevron-down">
                        {{ $is_npr === 'Y' ? 'Yes' : ($is_npr === 'N' ? 'No' : 'Select') }}
                    </flux:button>
                    <flux:menu>
                        <flux:menu.item wire:click="$set('is_npr', 'Y')">Yes</flux:menu.item>
                        <flux:menu.item wire:click="$set('is_npr', 'N')">No</flux:menu.item>
                    </flux:menu>
                </flux:dropdown>
            </div>
            <div>
                <flux:input wire:model="npr_remark" label="NPR remark" placeholder="Enter remarks here" />
            </div>
            <div>
                <flux:input wire:model="pix_path" label="Shop picture path" placeholder="Edit shop picture path" />
            </div>
            <div>
                <flux:input wire:model="pix_path_eBay" label="eBay picture path" placeholder="Edit eBay picture path" />
            </div>

        </div>
        <div class="text-right mt-4">
            <flux:button class="px-4 py-2 bg-gray-800! text-white! rounded! hover:bg-gray-700!"
                wire:click='editPixPath({{ $itemDetail->supp_id }})'>Save</flux:button>
        </div>

    </fieldset>
</div>