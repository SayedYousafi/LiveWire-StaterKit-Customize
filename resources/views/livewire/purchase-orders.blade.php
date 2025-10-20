<div>
    <div class="flex justify-between">
        <flux:button icon='arrow-left' size='sm' variant='filled' onclick="history.back()">Back</flux:button>
        <h2 class="text-xl font-bold mb-4">Purchase Order Items (Order ID: {{ $supplierOrderId }})</h2>
    </div>
    @if (session('success'))
        <flux:callout icon="check-circle" variant="success" heading="{{ session('success') }}" class="mx-125 mb-2" />
    @endif
    <table class="table-default">
        <thead>
            <tr>
                <th>#</th>
                <th>ID Supplier name</th>
                <th>ID - Item name</th>
                <th>Model</th>
                <th>QTY</th>
                <th>Price (RMB)</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            @php
                $gt = 0;
            @endphp
            @forelse ($orderedItems as $index => $item)
                <tr>
                    @php
                        $q = $item->qty;
                        $p = $item->price_rmb;
                        $t = $p * $q;
                        $gt += $t;
                    @endphp
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $desiredSupplier = $item->SUPPID }} - {{ $desiredSupplierName =$item->supplierName }}</td>
                    <td>{{ $item->item_id }} - {{ $item->item_name }}</td>
                    <td>
                        <flux:input wire:model='model.{{ $index }}' size='sm' autofocus />
                    </td>
                    <td>{{ $q }}</td>
                    <td>{{ number_format($p, 2) }}</td>
                    <td>{{ number_format($t, 2) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="border px-4 py-2 text-center">No ordered items found for this supplier order
                        <strong>({{ $supplierOrderId }})</strong>.
                    </td>
                </tr>
            @endforelse
            <tr>
                <th colspan="6">Grand total</th>
                <td>{{ number_format($gt, 2) }}</td>
            </tr>
        </tbody>
    </table>

    <div class="grid grid-cols-3 md:grid-cols-3 lg:grid-cols-3 gap-3 mt-4">
        <flux:textarea wire:model='desc' label="Description" size='sm' autofocus />
        <flux:textarea wire:model='comment1' label="Comment below item table" size='sm' autofocus />
        <flux:textarea wire:model='comment2' label="Comment below attachments" size='sm' autofocus />
        <flux:textarea wire:model='comment3' label="Comment below quality criteria" size='sm' autofocus />
        <flux:textarea wire:model='comment4' label="Comment below delivery" size='sm' autofocus />
        <flux:textarea wire:model='comment5' label="Comment below delivery" size='sm' autofocus />
    </div>
    <div class="flex justify-center mt-4">
        <flux:button icon='plus-circle' size='sm' variant='primary' wire:click='save'>
            {{ $editor ? 'Update Purchase Order' : 'Create Purchase Order' }}
        </flux:button>
    </div>

    <table class='table-default mt-4'>
        <caption>Previously created POs for supplier {{ $desiredSupplierName }}</caption>
        <thead>
            <tr>
                <th>#</th>
                <th>ID</th>
                <th>PO No.</th>
                <th>Supplier</th>
                <th>Description</th>
                <th>Total RMB</th>
                <th>Date created</th>
                <th colspan="5">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($pos as $po)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $po->id }}</td>
                    <td>GTO{{ now()->year }}{{ str_pad($po->id, 4, '0', STR_PAD_LEFT) }}</td>
                    <td>{{ $po->supplier->name }} - {{ $po->supplier->name_cn }}</td>
                    <td>{{ $po->desc }}</td>
                    <td>
                        {{ number_format($po->purchaseOrders->sum(fn($order) => $order->qty * $order->price), 2) }}
                    </td>
                    <td>{{ formatGeneralDate($po->created_at) }}</td>
                    @if ($po->status === 0)
                    <td>
                        <flux:button class=" !bg-blue-800 !text-white hover:!bg-blue-700 rounded-md" size='sm' variant='filled' wire:click='edit({{ $po->id }})'>Edit</flux:button>
                    </td>
                    @else
                    <td>Closed</td> 
                    @endif
                    
                    <td>
                        <flux:button size='sm' variant='filled' wire:click='view({{ $po->id }})'>View</flux:button>
                    </td>
                    <td>
                        <flux:button size='sm' variant='primary' href="{{ route('po', $po->id) }}">Download</flux:button>
                    </td>
                    <td>
                        <flux:button size='sm'
                            class=" !bg-black !text-white hover:!bg-gray-700 rounded-md"
                            wire:confirm="Are you sure closing this PO ?"
                            wire:click='close({{ $po->id }})'>Close</flux:button>
                    </td>
                </tr>
            @empty
                <tr>
                    <th colspan="5">No records found</th>
                </tr>
            @endforelse
        </tbody>
    </table>

    @if ($viewingPo)
        <div class="fixed inset-0 bg-gray-600 bg-opacity-50 flex items-center justify-center z-50">
            <div class="bg-white p-8 rounded-lg shadow-lg max-w-4xl w-full max-h-[90vh] overflow-y-auto">
                <div class="flex justify-between mb-4">
                    <h2 class="text-2xl font-bold">Purchase Order #{{ $viewingPo->id }}</h2>
                    <flux:button wire:click='closeView' variant='subtle' size='sm'>Close</flux:button>
                </div>

                <div class="border border-gray-300 p-4 rounded">
                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <div>
                            <p><strong>PO Number:</strong> {{ $viewingPo->id }}</p>
                            <p><strong>Date:</strong> {{ $viewingPo->created_at->format('Y-m-d') }}</p>
                            <p><strong>Description:</strong> {{ $viewingPo->desc }}</p>
                        </div>
                        <div>
                            <p><strong>Supplier ID:</strong> {{ $viewingPo->supplier_id }}</p>
                            <p><strong>Supplier Name:</strong> {{ $viewingPo->supplier->name ?? 'N/A' }}</p>
                        </div>
                    </div>

                    <h3 class="text-lg font-semibold mb-2">Items</h3>
                    <table class="table-default w-full mb-4">
                        <thead>
                            <tr>
                                <th>Item Name</th>
                                <th>Model</th>
                                <th>Quantity</th>
                                <th>Price (RMB)</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $grandTotal = 0;
                            @endphp
                            @foreach ($viewingPo->purchaseOrders as $order)
                                @php
                                    $total = $order->qty * $order->price;
                                    $grandTotal += $total;
                                @endphp
                                <tr>
                                    <td>{{ $order->item->item_name ?? 'N/A' }}</td>
                                    <td>{{ $order->model ?? 'N/A' }}</td>
                                    <td>{{ $order->qty }}</td>
                                    <td>{{ number_format($order->price, 2) }}</td>
                                    <td>{{ number_format($total, 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <th colspan="4">Grand Total</th>
                                <td>{{ number_format($grandTotal, 2) }}</td>
                            </tr>
                        </tfoot>
                    </table>

                    @if ($viewingPo->comment1)
                        <div class="mb-4">
                            <h4 class="font-semibold">Comment Below Item Table</h4>
                            <p>{{ $viewingPo->comment1 }}</p>
                        </div>
                    @endif

                    @if ($viewingPo->comment2 || $viewingPo->purchaseOrders->pluck('item.attachments')->flatten()->isNotEmpty())
                        <div class="mb-4">
                            <h4 class="font-semibold">Comment Below Attachments</h4>
                            @if ($viewingPo->comment2)
                                <p>{{ $viewingPo->comment2 }}</p>
                            @endif
                            @php
                                $attachments = $viewingPo->purchaseOrders->pluck('item.attachments')->flatten()->unique('id');
                            @endphp
                            @if ($attachments->isNotEmpty())
                                <h5 class="font-medium mt-2">Attachments</h5>
                                <table class="table-default w-full mb-2">
                                    <thead>
                                        <tr>
                                            <th>Filename</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($attachments as $attachment)
                                            <tr>
                                                <td>{{ $attachment->filename }}</td>
                                                <td>
                                                    <a href="{{ asset($attachment->path) }}" target="_blank" class="text-blue-600 hover:underline">Open PDF</a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            @endif
                        </div>
                    @endif

                    @if ($viewingPo->comment3 || $viewingPo->purchaseOrders->pluck('item.itemQualities')->flatten()->isNotEmpty())
                        <div class="mb-4">
                            <h4 class="font-semibold">Comment Below Quality Criteria</h4>
                            @if ($viewingPo->comment3)
                                <p>{{ $viewingPo->comment3 }}</p>
                            @endif
                            @php
                                $itemQualities = $viewingPo->purchaseOrders->pluck('item.itemQualities')->flatten()->unique('id');
                            @endphp
                            @if ($itemQualities->isNotEmpty())
                                <h5 class="font-medium mt-2">Quality Criteria</h5>
                                <table class="table-default w-full mb-2">
                                    <thead>
                                        <tr>
                                            <th>Picture</th>
                                            <th>Name</th>
                                            <th>Description</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($itemQualities as $quality)
                                            <tr>
                                                <td>{{ $quality->picture }}</td>
                                                <td>{{ $quality->name ?? 'N/A' }}</td>
                                                <td>{{ $quality->description ?? 'N/A' }}</td>
                                                <td>
                                                    <a href="{{ asset('pictures/' . $quality->picture) }}" target="_blank" class="text-blue-600 hover:underline">Open Image</a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            @endif
                        </div>
                    @endif

                    @if ($viewingPo->comment4 || $viewingPo->comment5)
                        <div class="mb-4">
                            <h4 class="font-semibold">Comment Below Delivery</h4>
                            @if ($viewingPo->comment4)
                                <p>{{ $viewingPo->comment4 }}</p>
                            @endif
                            @if ($viewingPo->comment5)
                                <p>{{ $viewingPo->comment5 }}</p>
                            @endif
                        </div>
                    @endif

                    <div class="mt-8 border-t pt-4 text-center">
                        <flux:text size='sm'>This is a GTech system-generated Purchase Order. Please contact us for any queries.</flux:text>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>