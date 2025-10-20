<!DOCTYPE html>
<html lang="en">

<head>
    <title>Purchase Order #{{ $po->id }}</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

    <style>
    @font-face {
        font-family: 'NotoSansSC';
        font-weight: 400;
        src: url('{{ public_path('fonts/NotoSansSC-Regular.ttf') }}') format('truetype');
    }

    * {
        font-family: NotoSansSC, DejaVu Sans, sans-serif;
        -webkit-box-sizing: border-box;
        -moz-box-sizing: border-box;
        box-sizing: border-box;
    }

    .header {
        text-align: center;
        margin-bottom: 20px;
    }

    .company-info {
        font-size: 10pt;
        color: #666;
    }

    .po-details {
        margin-bottom: 20px;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 20px;
        table-layout: auto;
    }

    th,
    td {
        border: 1px solid #ddd;
        padding: 8px;
        text-align: left;
        word-break: break-all;
        overflow-wrap: break-word;
        white-space: normal;
        max-width: 200px; /* Adjust as needed */
    }

    th {
        background-color: #f2f2f2;
    }

    .section-title {
        font-weight: bold;
        margin-top: 20px;
    }

    .footer {
        position: fixed;
        bottom: 0;
        width: 100%;
        text-align: center;
        font-size: 10pt;
        color: #666;
        border-top: 1px solid #ddd;
        padding-top: 10px;
        z-index: 1000;
    }

    .footer .page-number::after {
        content: "Page " counter(page);
    }

    .footer-table {
        width: 100%;
        margin: 0 auto;
        border: none;
    }

    .footer-table td {
        border: none;
        padding: 5px;
    }

    .footer-table td:first-child {
        text-align: left;
    }

    .footer-table td:nth-child(2) {
        text-align: center;
    }

    .footer-table td:last-child {
        text-align: right;
    }

    .signatures {
        margin-top: 40px;
        display: flex;
        justify-content: space-between;
    }

    img {
        max-width: 100%;
        height: auto;
        margin: 10px 0;
    }

    .bilingual {
        margin-bottom: 10px;
        word-break: break-all;
        overflow-wrap: break-word;
        white-space: normal;
        max-width: 100%;
    }

    body {
        margin-bottom: 60px;
    }

    @page {
        margin-bottom: 60px;
    }
</style>
</head>

<body>
    <!-- Company Header -->
    <img src="{{ public_path('images/Header.jpg') }}" class="img header" />
    <!-- PO Details -->
    <h2 align='center'>Purchase Order</h2>
    <div class="po-details">
        <table>
            <tr>
                <th>To:</th>
                <td colspan="3">{{ $po->supplier->name_cn ?? 'N/A' }}{{ $po->supplier->name ?? 'N/A' }} </td>
            </tr>
            <tr>
                <th>Contact person:</th>
                <td>{{ $po->supplier->contact_person }}</td>
                <td colspan="2">Address: {{ $po->supplier->full_address }}</td>
            </tr>
            <tr>
                <th>Description:</th>
                <td colspan="3">{{ $po->desc }}</td>
            </tr>
            <tr>
                <th>PO No.:</th>
                <td>GTO{{ now()->year }}{{ str_pad($po->id, 4, '0', STR_PAD_LEFT) }}</td>
                <th>Date:</th>
                <td>{{ $po->created_at->format('d/m/Y') }}</td>
            </tr>
        </table>
    </div>

    <!-- Items Table -->

    <table>
        <thead>
            <tr>
                <th>Image</th>
                <th>Description</th>
                <th>Model</th>
                <th>QTY (pcs)</th>
                <th>RMB/pc</th>
                <th>Total (RMB)</th>
            </tr>
        </thead>
        <tbody>
            @php
            $grandTotal = 0;
            // Group orders by item photo
            $groupedOrders = $po->purchaseOrders->groupBy('item.photo');
            @endphp
            @foreach ($groupedOrders as $photo => $orders)
            @php
            $rowspan = $orders->count(); // Number of items with the same photo
            $firstOrder = $orders->first(); // Get the first order for the photo
            @endphp
            <tr>
                <!-- Display the photo only once with rowspan -->
                <td rowspan="{{ $rowspan }}">
                    <img src="{{ storage_path('app/public/'.$photo) }}" alt="{{ $photo }}"
                        style="width: 64px; height:64px" />
                </td>
                <!-- Display the first item's details -->
                @php
                $total = $firstOrder->qty * $firstOrder->price;
                $grandTotal += $total;
                @endphp
                <td>{{ $firstOrder->item->item_name ?? 'N/A' }}</td>
                <td>{{ $firstOrder->model ?? 'N/A' }}</td>
                <td>{{ $firstOrder->qty }}</td>
                <td>{{ number_format($firstOrder->price, 2) }}</td>
                <td>{{ number_format($total, 2) }}</td>
            </tr>
            <!-- Display remaining items with the same photo -->
            @foreach ($orders->slice(1) as $order)
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
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <th>Grand Total</th>
                <th></th>
                <th></th>
                <th>{{ $po->purchaseOrders->sum('qty') }}</th>
                <th></th>
                <th>{{ number_format($grandTotal, 2) }}</th>
            </tr>
        </tfoot>
    </table>

    <!-- Comment below item table -->
    @if ($po->comment1)
    <div class="mb-4">
        {{-- <h3 class="section-title">Comment Below Item Table</h3> --}}
        <p class="bilingual">{{ $po->comment1 }}</p>
    </div>
    @endif

    <!-- Comment below item table -->
    @if ($po->comment2)
    <div class="mb-4">
        {{-- <h3 class="section-title">Comment Below Item Table</h3> --}}
        <p class="bilingual">{{ $po->comment2 }}</p>
    </div>
    @endif

    <!-- Quality Criteria Comment -->
    @if ($po->comment3 || $po->purchaseOrders->pluck('item.itemQualities')->flatten()->isNotEmpty())
    <div class="mb-4">
        {{-- <h3 class="section-title">Comment Below Quality Criteria</h3> --}}
        @if ($po->comment3)
        <p class="bilingual">{{ $po->comment3 }}</p>
        @endif
        @php
        $itemQualities = $po->purchaseOrders->pluck('item.itemQualities')->flatten()->unique('id');
        @endphp
        @if ($itemQualities->isNotEmpty())
        {{-- <h4>Quality Criteria</h4> --}}
        @foreach ($itemQualities as $quality)
        <img src="{{ storage_path('app/public/pictures/'. $quality->picture) }}" alt="{{ $quality->picture }}">
        <p><strong>Name:</strong> {{ $quality->name ?? 'N/A' }}</p>
        <p><strong>Description:</strong> {{ $quality->description ?? 'N/A' }}</p>
        @endforeach
        @endif
    </div>
    @endif

    <!-- Delivery Comments -->
    @if ($po->comment4 || $po->comment5)
    <div class="mb-4">
        {{-- <h3 class="section-title">Comment Below Delivery</h3> --}}
        @if ($po->comment4)
        <p class="bilingual">{{ $po->comment4 }}</p>
        @endif
        @if ($po->comment5)
        <p class="bilingual">{{ $po->comment5 }}</p>
        @endif
    </div>
    @endif

    <!-- General Comment -->
    @if ($po->comment6)
    <div class="mb-4">
        {{-- <h3 class="section-title">General Comment</h3> --}}
        <p class="bilingual">{{ $po->comment6 }}</p>
    </div>
    @endif

    <!-- Terms of payment -->
    @if ($po->supplier->deposit)
    <div class="mb-4">
        <h3 class="section-title">Payment Terms</h3>
        <p>Total Payment: {{ number_format($grandTotal, 2) }}</p>
        @if (is_numeric($po->supplier->percentage))
        <p class="bilingual">
            {{ $po->supplier->percentage }}% RMB =
            {{ number_format($grandTotal * (floatval($po->supplier->percentage) / 100), 2) }} - Deposit
        </p>
        @endif
        @if (is_numeric($po->supplier->percentage2))
        <p class="bilingual">
            {{ $po->supplier->percentage2 }}% RMB =
            {{ number_format($grandTotal * (floatval($po->supplier->percentage2) / 100), 2) }} - balance before goods
            delivery
        </p>
        @endif
        @if (is_numeric($po->supplier->percentage3))
        <p class="bilingual">
            {{ $po->supplier->percentage3 }}% RMB =
            {{ number_format($grandTotal * (floatval($po->supplier->percentage3) / 100), 2) }} - balance after goods
            delivery
        </p>
        @endif
    </div>
    @endif
    <!-- Bank Info -->
    @if ($po->supplier->bank_name)
    <div class="mb-4">
        <h3 class="section-title">Bank Information</h3>
        <p class="bilingual">Bank: {{ $po->supplier->bank_name }}</p>
        <p class="bilingual">Account: {{ $po->supplier->account_number }}</p>
        <p class="bilingual">Name: {{ $po->supplier->beneficiary }}</p>
        <p>&nbsp;</p>
        <p>&emsp;</p>
        <p></p>
        <p></p>
        <p></p>
        <p></p>
        <p></p>
    </div>
    @endif
    <!-- Signatures -->

    <img src="{{ public_path('images/signatures.jpg') }}" />
    @php
    $attachments = $po->purchaseOrders->pluck('item.attachments')->flatten()->unique('id');
    @endphp
    <ol>
        @foreach ($attachments as $attach)
        <li>
            {{ $attach->filename }}
        </li>
        @endforeach
    </ol>
    <!-- Footer -->
    <div class="footer">
        <table class="footer-table">
            <tr>
                <td>{{ $po->created_at->format('d/m/Y') }}</td>
                <td>{{ $po->desc }}</td>
                <td style="text-align: right;" class="page-number">
                    <!-- The text appears here via CSS ::after -->
                </td>
            </tr>
        </table>
        <flux:text size='sm'>This is a GTech system-generated Purchase Order. Please contact us for any queries.
        </flux:text>
    </div>

    <!-- Fallback to ensure footer space on first page -->
    <div style="height: 60px;"></div>
</body>

</html>