<div class="p-4">
    @if(session()->has('success'))
    <flux:callout variant="success" heading="{{ session('success') }}" class="mt-3 mb-3" />
    @endif

    <div class="mb-4 flex items-center justify-between">
        <div class="flex items-center space-x-2">
            <flux:input autofocus label="Type a parent name:" wire:model="search" wire:keyup="searchResult"
                placeholder="Start parent no with v" />
        </div>
        <flux:heading size='lg'>New Item Processing fill the required fields properly</flux:heading>
        <flux:button icon="backspace" onclick="history.back()" class="bg-blue-800! text-white! hover:bg-blue-700!">
            Back
        </flux:button>
    </div>

    @if($showresult && !empty($records))
    <div class="bg-white border rounded w-72 shadow-md">
        @foreach($records as $record)
        <li wire:click="fetchParentDetail({{ $record->id }})" class="p-2 text-dark hover:!bg-gray-100 cursor-pointer">
            {{ $record->de_no }}
        </li>
        @endforeach
    </div>
    @endif
    @foreach ($eans as $ean)
    <?php $e=$ean->ean;?>
    @endforeach
    @if(!empty($parentDetails))
    <div class="mt-6 space-y-6">
        <div class="overflow-x-auto">
            <table class="table-auto w-full border border-gray-300 text-sm">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="border px-2 py-1">Parent Item No:</th>
                        <th class="border px-2 py-1">Parent Name DE</th>
                        <th class="border px-2 py-1">Parent Name EN</th>
                        <th class="border px-2 py-1">Parent Name CN</th>
                        <th class="border px-2 py-1">Parent var Name DE</th>
                        <th class="border px-2 py-1">Parent var Name EN</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="border px-2 py-1">{{ $parentDetails->de_no }}</td>
                        <td class="border px-2 py-1">{{ $parentDetails->name_de }}</td>
                        <td class="border px-2 py-1">{{ $parentDetails->name_en }}</td>
                        <td class="border px-2 py-1">{{ $parentDetails->name_cn }}</td>
                        <td class="border px-2 py-1">
                            {{ $parentDetails->var_de_1 }}<br>
                            {{ $parentDetails->var_de_2 }}<br>
                            {{ $parentDetails->var_de_3 }}
                        </td>
                        <td class="border px-2 py-1">
                            {{ $parentDetails->var_en_1 }}<br>
                            {{ $parentDetails->var_en_2 }}<br>
                            {{ $parentDetails->var_en_3 }}
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="border-t pt-4">
            <h2 class="text-lg font-bold mb-4">New item process</h2>
            @foreach ($parentDetails->items as $myItem)
            @php
            $myString = substr($parentDetails->de_no, -3);
            $item_no_de = $myString . "-" . $ean->ean;
            @endphp
            <form wire:submit.prevent="save({{ $parentDetails->id }}, {{ $ean->ean }}, '{{ $item_no_de }}')"
                class="space-y-4">
                @endforeach
                <fieldset class="border border-gray-300 p-4 rounded">
                    <legend class="font-semibold text-gray-700 px-2">Item Basic Info</legend>
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-2">
                        <flux:input label="Parent Item No" wire:model="de_no" readonly />
                        <flux:input label="EAN" value="{{ $ean->ean }}" readonly />
                        <flux:input label="Item No. DE" value="{{ $item_no_de }}" readonly />

                        @if($myString == 'ONE')
                        <flux:select label="Select Supplier Category" wire:model="supp_cat">
                            <option value="">Select Category</option>
                            <option value="STD">STD</option>
                            <option value="PRO">PRO</option>
                            <option value="GTR">GTR</option>
                            <option value="GBL">GBL</option>
                        </flux:select>
                        @else
                        @php $supp_cat = $myString; @endphp
                        @endif
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                        <flux:input label="Item Name DE" wire:model="item_name_de" autofocus />
                        <flux:input label="Item Name EN" wire:model="item_name_en" />
                        <flux:input label="Item Name CN" wire:model="item_name_cn" />
                        <flux:input label="Item Name Global" wire:model="item_name" />
                    </div>
                </fieldset>

                <fieldset class="border border-gray-300 p-4 rounded mb-6">
                    <legend class="font-semibold text-gray-800 px-2">Var Val (DE & EN)</legend>

                    <div class="grid grid-cols-1 md:grid-cols-6 gap-4">
                        {{-- DE Fields --}}
                        @if (!empty($parentDetails->var_de_1))
                        <flux:input label="{{ $parentDetails->var_de_1 }}" wire:model="de_v1" />
                        @else
                        <flux:input label="Var DE 1" wire:model="de_v1" readonly />
                        @endif

                        @if (!empty($parentDetails->var_de_2))
                        <flux:input label="{{ $parentDetails->var_en_2 }}" wire:model="value_de_2" />
                        @else
                        <flux:input label="Var DE 2" wire:model="value_de_2" readonly />
                        @endif

                        @if (!empty($parentDetails->var_en_3))
                        <flux:input label="{{ $parentDetails->var_en_3 }}" wire:model="value_de_3" />
                        @else
                        <flux:input label="Var DE 3" wire:model="value_de_3" readonly />
                        @endif

                        {{-- EN Fields --}}
                        @if (!empty($parentDetails->var_en_1))
                        <flux:input label="{{ $parentDetails->var_en_1 }}" wire:model="en_v1" />
                        @else
                        <flux:input label="Var EN 1" wire:model="en_v1" readonly />
                        @endif

                        @if (!empty($parentDetails->var_en_2))
                        <flux:input label="{{ $parentDetails->var_en_2 }}" wire:model="value_en_2" />
                        @else
                        <flux:input label="Var EN 2" wire:model="value_en_2" readonly />
                        @endif

                        @if (!empty($parentDetails->var_en_3))
                        <flux:input label="{{ $parentDetails->var_en_3 }}" wire:model="value_en_3" />
                        @else
                        <flux:input label="Var EN 3" wire:model="value_en_3" readonly />
                        @endif
                    </div>
                </fieldset>

                <fieldset class="border border-gray-300 p-4 rounded">
                    <legend class="font-semibold text-gray-800 px-2">Item Details</legend>

                    <div class="grid grid-cols-1 md:grid-cols-6 gap-2">

                        {{-- Dimensions & Weight --}}
                        <flux:select label="Weight Estimated" wire:model="isbn">
                            <option value="0">No</option>
                            <option value="1">Yes</option>
                        </flux:select>
                        <flux:input label="Weight (kg)" wire:model="weight"  />
                        <flux:input label="Height (cm)" wire:model="height" />
                        <flux:input label="Width (cm)" wire:model="width"  />
                        <flux:input label="Length (cm)" wire:model="length" />

                        <flux:input label="Purchase Price (RMB)" wire:model="RMB_Price" />

                        {{-- Item Rating --}}
                        <flux:select label="Many Component" wire:model="many_components">
                            <option value="" disabled>Component</option>
                            <option value="1">1</option>
                            <option value="2">2</option>
                            <option value="3">3</option>
                        </flux:select>

                        <flux:select label="Effort Rating" wire:model="effort_rating">
                            <option value="" disabled>Effort rating</option>
                            <option value="1">1</option>
                            <option value="2">2</option>
                            <option value="3">3</option>
                            <option value="4">4</option>
                            <option value="5">5</option>
                        </flux:select>

                        <flux:select label="Special Price" wire:model="rmb_special_price">
                            <option value="0">No</option>
                            <option value="1">Yes</option>
                        </flux:select>

                        {{-- Item Properties --}}
                        <flux:select label="Is Dividable" wire:model="is_qty_dividable">
                            <option value="N">No</option>
                            <option value="Y">Yes</option>
                        </flux:select>

                        <flux:select label="Is Stock Item" wire:model="is_stock_item">
                            <option value="0">No</option>
                            <option value="1">Yes</option>
                        </flux:select>

                        <flux:select label="Is PU Item" wire:model="is_pu_item">
                            <option value="0">No</option>
                            <option value="1">Yes</option>
                        </flux:select>

                        <flux:select label="Is Meter Item" wire:model="is_meter_item">
                            <option value="0">No</option>
                            <option value="1">Yes</option>
                        </flux:select>
                        <flux:select label="Is PO Item?" wire:model="is_po">
                            <option value="" disabled>Select</option>
                            <option value="N">No</option>
                            <option value="Y">Yes</option>
                        </flux:select>
                        <flux:input label="First Order Qty (FOQ)" wire:model="foq" type="text" />
                        <flux:input label="First Sending Qty (FSQ)" wire:model="fsq" type="text" />
                        <flux:input label="Item Picture Name" wire:model="photo" type="text"
                            helper="No extension is required" class="col-span-2" />
                    </div>
                </fieldset>


                <fieldset class="border border-gray-300 p-4 rounded">
                    <legend class="font-semibold text-gray-700 px-2">Other Detials</legend>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <flux:select label="Select Taric" wire:model="taric_id">
                            @foreach ($tariffs as $tariff)
                            @if(optional($myItem)->taric_id === null)
                            {{ $myItem->taric_id = '00000' }}
                            @endif
                            <option value="{{ $tariff->id }}" @selected($tariff->id == $myItem->taric_id)>
                                {{ $tariff->id }} - {{ $tariff->code }} - {{ Str::substr($tariff->description_en , 0,
                                80) }}
                            </option>
                            @endforeach
                        </flux:select>
                        @error('tariff_code') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror

                        <flux:select label="Select Supplier" wire:model="supplier_id">
                            @foreach ($suppliers as $supplier)
                            <option value="{{ $supplier->id }}">
                                {{ $supplier->id }} - {{ $supplier->name }} - {{ $supplier->name_cn }}
                            </option>
                            @endforeach
                        </flux:select>
                        @error('supplier_id') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror
                        <flux:textarea label="Item URL" wire:model="url" rows="2" class="col-span-2 md:col-span-2" />
                        <flux:textarea label="Item Remark" wire:model="remark" rows="2"
                            class="col-span-2 md:col-span-2" />
                    </div>
                </fieldset>

                <flux:button type="submit" variant='primary' icon="plus-circle">Add new item</flux:button>
            </form>
        </div>
        <fieldset class="border border-gray-300 p-4 rounded">
            <legend class="font-semibold text-gray-700 px-2">Items List for {{ $parentDetails->de_no }}</legend>
            @if(session()->has('success'))
            <flux:callout variant="success" heading="{{ session('success') }}" class="mb-3" />
            @endif
            <div class="overflow-x-auto">
                <table class="min-w-full table-auto border border-gray-300 text-sm">
                    <thead class="bg-gray-100">
                        <tr class="table-highlighted">
                            <th class="border px-2 py-1">ID</th>
                            <th class="border px-2 py-1">EAN</th>
                            <th class="border px-2 py-1">Item Name</th>
                            <th class="border px-2 py-1">Var Val DE 1</th>
                            <th class="border px-2 py-1">Var Val DE 2</th>
                            <th class="border px-2 py-1">Var Val DE 3</th>
                            <th class="border px-2 py-1">Var Val EN 1</th>
                            <th class="border px-2 py-1">Var Val EN 2</th>
                            <th class="border px-2 py-1">Var Val EN 3</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                        $items = $parentDetails->items->isEmpty()
                        ? collect([(object) [
                        'id' => 0,
                        'ean' => '0000000000000',
                        'item_name' => 'Default Item Name',
                        'is_new' => false,
                        'values' => collect([]),
                        ]])
                        : $parentDetails->items;
                        @endphp

                        @foreach ($items->sortByDesc('id') as $item)
                        <tr>
                            <td class="border px-2 py-1">{{ $item->id }}</td>
                            <td class="border px-2 py-1">{{ $item->ean }}</td>
                            <td class="border px-2 py-1 flex items-center gap-2">
                                {{ $item->item_name }}
                                @if ($item->is_new === 'Y')
                                <span
                                    class="inline-flex items-center rounded-full bg-yellow-100 px-2 py-0.5 text-xs font-medium text-yellow-800 border border-yellow-300 ml-2">NEW</span>
                                @endif
                            </td>

                            @php $value = optional($item->values)->first(); @endphp
                            <td class="border px-2 py-1">{{ $value->value_de ?? '' }}</td>
                            <td class="border px-2 py-1">{{ $value->value_de_2 ?? '' }}</td>
                            <td class="border px-2 py-1">{{ $value->value_de_3 ?? '' }}</td>
                            <td class="border px-2 py-1">{{ $value->value_en ?? '' }}</td>
                            <td class="border px-2 py-1">{{ $value->value_en_2 ?? '' }}</td>
                            <td class="border px-2 py-1">{{ $value->value_en_3 ?? '' }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </fieldset>
    </div>
    @endif
</div>