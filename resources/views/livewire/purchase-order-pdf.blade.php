<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Purchase Order #{{ $po->id }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12pt;
            margin: 20px;
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
        }

        th,
        td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
        }

        .section-title {
            font-weight: bold;
            margin-top: 20px;
        }

        .footer {
            text-align: center;
            font-size: 10pt;
            color: #666;
            margin-top: 40px;
            border-top: 1px solid #ddd;
            padding-top: 10px;
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
        }
    </style>
</head>

<body>
    <!-- Company Header -->

    <!-- PO Details -->
    <h2 align='center'>Purchase Order</h2>
    <div class="po-details">
        <table>
            <tr>
                <th>To:</th>
                <td colspan="3">{{ $po->supplier->name ?? 'N/A' }} (Supplier ID: {{ $po->supplier_id }})</td>
            </tr>
            <tr>
                <th>Description of Product:</th>
                <td colspan="3">{{ $po->desc }}</td>
            </tr>
            <tr>
                <th>Purchase Order No.:</th>
                <td>GTO{{ now()->year }}{{ str_pad($po->id, 6, '0', STR_PAD_LEFT) }}</td>
                <th>Date:</th>
                <td>{{ $po->created_at->format('d/m/Y') }}</td>
            </tr>
        </table>
    </div>

    <!-- Items Table -->
    <table>
        <thead>
            <tr>
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
            @endphp
            @foreach ($po->purchaseOrders as $order)
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
                <th>Grand Total</th>
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
        <h3 class="section-title">Comment Below Item Table</h3>
        <p class="bilingual">{{ $po->comment1 }}</p>
    </div>
    @endif

    <!-- Attachments Comment -->
    @if ($po->comment2 || $po->purchaseOrders->pluck('item.attachments')->flatten()->isNotEmpty())
    <div class="mb-4">
        <h3 class="section-title">Comment Below Attachments</h3>
        @if ($po->comment2)
        <p class="bilingual">{{ $po->comment2 }}</p>
        @endif
        @php
        $attachments = $po->purchaseOrders->pluck('item.attachments')->flatten()->unique('id');
        @endphp
        @if ($attachments->isNotEmpty())
        <h4>Attachments</h4>
        @foreach ($attachments as $attachment)
        <p><strong>Filename:</strong> {{ $attachment->filename }}</p>
        @if (isset($attachmentPages[$attachment->id]))
        @foreach ($attachmentPages[$attachment->id] as $pagePath)
        <img src="{{ $pagePath }}" alt="Attachment Page">
        @endforeach
        @endif
        @endforeach
        @endif
    </div>
    @endif

    <!-- Quality Criteria Comment -->
    @if ($po->comment3 || $po->purchaseOrders->pluck('item.itemQualities')->flatten()->isNotEmpty())
    <div class="mb-4">
        <h3 class="section-title">Comment Below Quality Criteria</h3>
        @if ($po->comment3)
        <p class="bilingual">{{ $po->comment3 }}</p>
        @endif
        @php
        $itemQualities = $po->purchaseOrders->pluck('item.itemQualities')->flatten()->unique('id');
        @endphp
        @if ($itemQualities->isNotEmpty())
        <h4>Quality Criteria</h4>
        @foreach ($itemQualities as $quality)
        {{-- <img src="{{ public_path('pictures/' . $quality->picture) }}" alt="{{ $quality->picture }}"> --}}
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
        <h3 class="section-title">Comment Below Delivery</h3>
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
        <h3 class="section-title">General Comment</h3>
        <p class="bilingual">{{ $po->comment6 }}</p>
    </div>
    @endif

    <!-- Footer -->
    <div class="footer">
        <p>This is a GTech system-generated Purchase Order. Please contact us for any queries.</p>
    </div>
</body>

</html>