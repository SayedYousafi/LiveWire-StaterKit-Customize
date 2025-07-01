<div>
    <x-sub-menu :current="$title" />
    @if (session('success'))
    <div class=" mt-2 text-center mb-2">
        <flux:callout variant="success" icon="check-circle" heading="{{ session('success') }}" />
    </div>
    @endif
    @if (session('error'))
    <div class=" mt-2 text-center mb-2">
        <flux:callout variant="danger" icon="x-circle" heading="{{ session('error') }}" />
    </div>
    @endif
    <table class="table-default">
        <thead>
            <tr>
                <th class="">ID
                    <flux:button variant="danger" size='sm' wire:click='cancel' icon='x-circle'></flux:button>
                </th>
                <th>Customer</th>
                <th>Cargo No.</th>
                <th>Closed Date</th>
                <th>Item Count</th>
                <th>Total Qty</th>
                <th>Total Price</th>
                <th colspan="3" class="text text-center">Actions</th>
            </tr>
        </thead>
        <tbody>
            @if (!empty($data))

            @foreach ($data as $ci )
            <tr wire:key={{ $ci->id }}>
                <td>
                    <flux:button wire:click="showCiItem({{$ci->customer_id}}, {{$ci->invSerialNo }})"
                        class=" bg-gray-500! hover:bg-gray-400! text-white!" size='sm'
                        icon:trailing='arrow-right-circle'>
                        {{ $ci->id }}
                    </flux:button>

                </td>

                <td>{{ $ci->customer_company_name }}</td>
                <td>{{ $ci->id }} - {{ $ci->cargo_no }}</td>
                <td>{{ myDate($ci->InvoiceDate, 'd-m-Y' )}}</td>
                <td><a wire:click.prevent="showItems({{$ci->customer_id}}, {{$ci->invSerialNo }})" href="#">{{
                        $ci->item_count}}</a></td>
                <td>{{ $ci->total_qty }}</td>
                <td>{{ $ci->total_price}}</td>
                <td class="text text-center">

                    <flux:button wire:click='checkPrice({{ $ci->id }},{{$ci->invSerialNo}})'>
                        {{-- class="bg-blue-600! text-white! hover:bg-blue-500!" icon='document-duplicate' size='sm'> --}}
                        <img src="{{ asset('img/icon_download_PDF.svg') }}" alt="MySVG" class="w-10 h-10">
                    </flux:button>
                </td>
                <td>

                    <flux:button wire:click='getData({{ $ci->id }}, {{$ci->invSerialNo}})' variant="primary" size='sm'
                        icon='pencil-square'>
                        Edit
                    </flux:button>
                </td>
                </td>
                <td class="text text-center fw-bold">
                    @if ($ci->cargo_status=='Shipped')
                        <flux:button wire:confirm='Are you sure? shipment will happen?'
                            wire:navigate href="{{ route('packingList', $ci->cargo_id) }}" icon='numbered-list' size='sm'>
                            Pack List
                        </flux:button> 
                    @else
                    <flux:button wire:confirm='Ready to Make Packing List?'
                        wire:click="shipCI({{ $ci->cargo_id }})" variant='danger' icon='x-circle' size='sm'>
                        Ship
                    </flux:button>
                    @endif
                </td>
            </tr>

            @if($invNo == $ci->id && $ci->invSerialNo == $sn)
            <tr>
                <td colspan="10" class="bg-yellow-50 dark:bg-yellow-900/20 px-4 py-6">
                    <div class="mx-auto w-1/2">
                        <div
                            class="bg-white dark:bg-gray-800 shadow rounded-2xl p-6 border border-gray-200 dark:border-gray-700">
                            <div class="space-y-4">
                                <div>
                                    <flux:input label="Description *" type="text" wire:model="description"
                                        placeholder="Enter Description" class="w-full" />
                                </div>
                                <div>
                                    <flux:input label="Freight Cost *" type="text" wire:model="total_price"
                                        placeholder="Enter Freight Cost" class="w-full" />
                                </div>
                                <div>
                                    <flux:textarea label="Remark" wire:model="remark" rows="3"
                                        placeholder="Enter extra info" class="w-full" />
                                </div>
                                <div class="text-center pt-4 space-x-4">
                                    <flux:button wire:click="cancel" variant="outline" size="sm" icon="x-circle">
                                        Cancel
                                    </flux:button>
                                    <flux:button wire:click="editData" variant="primary" size="sm" icon="plus-circle">
                                        Save
                                    </flux:button>

                                </div>
                            </div>
                        </div>
                    </div>
                </td>
            </tr>
            @endif

            @if ($ci->customer_id==$ciNo && $ci->invSerialNo==$sn)
            <tr>
                <td colspan="11" class="text-center">
                    Items to be shown in invoice based on Taric
                    <table class="table-nested">
                        <thead>
                            <tr class="table-highlighted">
                                <th>Position</th>
                                <th>Taric Name EN</th>
                                <th>Taric Code</th>
                                <th>Total Qty</th>
                                <th>Unit Price</th>
                                <th>Total Price</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if(!empty($items))
                            @php
                            $g_total=0;
                            $g_qty = 0;
                            @endphp
                            @foreach ($items as $item )
                            <tr>
                                @php
                                $q=$item->total_qty;
                                $p=$item->total_price;
                                $u = $q == 0 ? 0 : ($p / $q); // Handle division by zero
                                //$u=$p/$q;
                                $g_total=$g_total+$p;
                                $g_qty = $g_qty+$q;
                                @endphp

                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $item->TarifName }}</td>
                                <td>{{ $item->code }}</td>
                                <td>{{ $q }}</td>
                                <td>{{ number_format($u, 3, '.', '') }}</td>
                                <td>{{ $p}}</td>
                            </tr>
                            @endforeach
                            <tr>
                                <th colspan="3">Grand total</th>
                                <th>{{ $g_qty }}</th>
                                <th></th>
                                <th align="right">€{{ number_format($g_total, 2, '.', '') }}</th>
                            </tr>
                            @endif
                        </tbody>
                    </table>
                </td>
            </tr>
            @endif
            @if ($taricNo==$ci->customer_id && $ci->invSerialNo==$sn)
            <tr>
                <td colspan="11" class="text-center">
                    <table class="table-nested">
                        <thead>
                            <tr class="table-highlighted">
                                <th>EAN</th>
                                <th>Item Name</th>
                                <th>Taric code</th>
                                <th>QTY</th>
                                <th>RMB</th>
                                <th>EK</th>
                            </tr>
                        </thead>
                        @if(!empty($results))
                        @foreach ($results as $item)
                        <tr>
                            <td>{{ $item->ean}}</td>
                            <td>{{ $item->item_name }}</td>
                            <td>{{ $item->tariff_code }}</td>
                            <td>{{ $item->qty }}</td>
                            <td>{{ $ek=$item->rmb }}</td>
                            <td>{{ $item->eur}}</td>
                            @endforeach
                            @endif
                    </table>
                </td>
            </tr>
            @endif
            @endforeach
            <tr>
                <td colspan="7">
                    @else
                    <span>No closed invoice found</span>
                    @endif
                </td>
            </tr>
        </tbody>
    </table>
</div>