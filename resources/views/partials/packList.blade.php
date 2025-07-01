<!DOCTYPE html>
<html>

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <title>Packing list</title>
  <style>
    body {
      font-family: 'Arial Unicode MS', sans-serif;
    }

    table {
      width: 100%;
      border-collapse: collapse;
      border: 1px solid black;
    }

    th,
    td {
      border: 1px solid black;
      padding: 4px;
      font-size: 12px;
    }

    th {
      background-color: #f0f0f0;
    }
  </style>
</head>

<body>
  @php
  $customer = $packList->first();
  $gtechTotal = 0;
  $k01Total = 0;
  $k02Total = 0;
  $grandTotal = 0;
  $grandTotalQty = 0;
  $gtechVolumTotal = 0;
  $k01VolumTotal = 0;
  $k02VolumTotal = 0;
  $grandVolum = 0;
  $countZeroQty = 0;
  @endphp

  <table>
    <tr>
      <th colspan="9" style="text-align: center;">GTech Industries Limited</th>
    </tr>
    <tr>
      <td colspan="9" style="text-align: center;">Yongxin Road, Bowang Town, Bowang, Maanshan, Anhui, China</td>
    </tr>
    <tr>
      <th colspan="9" style="text-align: center;">PACKING LIST</th>
    </tr>
    <tr>
      <td colspan="8">{{ $customer->customer_company_name }}</td>
      <td>Invoice No.: CIG{{ date('Ymd') }}{{ $customer->id }}</td>
    </tr>
    <tr>
      <td colspan="8">{{ $customer->address_line1 }} {{ $customer->postal_code }} {{ $customer->city }} {{
        $customer->country }}, Tel: {{ $customer->contact_phone }}</td>
      <td>Date: {{ \Carbon\Carbon::parse($customer->created_at)->format('Y-m-d') }}</td>
    </tr>
    <tr>
      <td colspan="8">Mr. {{ $customer->contact_first_name }} {{ $customer->contact_name }}</td>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <th rowspan="2">Description of goods</th>
      <th rowspan="2">QTY</th>
      <th rowspan="2">Clients</th>
      <th rowspan="2">Pallet/Tray</th>
      <th rowspan="2">G.W. (KG)</th>
      <th colspan="3">Measurement (CM)</th>
      <th rowspan="2">Total Volume (CBM)</th>
    </tr>
    <tr>
      <td align="center">L</td>
      <td align="center">B</td>
      <td align="center">H</td>
    </tr>

    @foreach ($packList as $pack)
    @php
    $volum = ($pack->length * $pack->width * $pack->height) / 1000000;
    $grandTotal += $pack->weight;
    $grandTotalQty += $pack->item_qty;
    $grandVolum += $volum;

    if ($pack->item_qty == 0) $countZeroQty++;

    if ($pack->client1 == 'GTECH-GT') {
    $gtechTotal += $pack->weight;
    $gtechVolumTotal += $volum;
    }
    if ($pack->client1 == 'K011111') {
    $k01Total += $pack->weight;
    $k01VolumTotal += $volum;
    }
    if ($pack->client1 == 'K022222') {
    $k02Total += $pack->weight;
    $k02VolumTotal += $volum;
    }
    @endphp
    <tr>
      <td style="{{ $pack->item_qty == 0 ? 'border:none;' : '' }}">
        {{ $pack->item_description }}
      </td>
      <td align="center" style="{{ $pack->item_qty == 0 ? 'border:none;' : '' }}">
        {{ $pack->item_qty != 0 ? $pack->item_qty : '' }}
      </td>
      <td align="center" style="white-space:nowrap; {{ $pack->item_qty == 0 ? 'border:none;' : '' }}">
        {{ $pack->client1 !== 'GTECH-GT' ? $pack->client1 : '' }}
      </td>
      <td align="center" style="{{ $pack->item_qty == 0 ? 'border:none;' : '' }}">
        {{ $pack->pallet }}
      </td>
      <td align="center" style="{{ $pack->item_qty == 0 ? 'border:none;' : '' }}">
        {{ $pack->weight != 0 ? number_format($pack->weight) : '' }}
      </td>
      <td align="center" style="{{ $pack->item_qty == 0 ? 'border:none;' : '' }}">
        {{ $pack->length != 0 ? number_format($pack->length) : '' }}
      </td>
      <td align="center" style="{{ $pack->item_qty == 0 ? 'border:none;' : '' }}">
        {{ $pack->width != 0 ? number_format($pack->width) : '' }}
      </td>
      <td align="center" style="{{ $pack->item_qty == 0 ? 'border:none;' : '' }}">
        {{ $pack->height != 0 ? number_format($pack->height) : '' }}
      </td>
      <td align="center" style="{{ $pack->item_qty == 0 ? 'border:none;' : '' }}">
        {{ $volum != 0 ? number_format($volum, 2) : '' }}
      </td>
    </tr>

    @endforeach

    <tr>
      <td colspan="9">&nbsp;</td>
    </tr>

    <tr>
      <th rowspan="4">Total</th>
      <th rowspan="4">{{ $grandTotalQty }}</th>
      <th>GTECH</th>
      <th rowspan="4"></th>
      <th>{{ number_format($gtechTotal, 2) }}</th>
      <th colspan="3" rowspan="4"></th>
      <th>{{ number_format($gtechVolumTotal, 2) }}</th>
    </tr>
    <tr>
      <th>K011111</th>
      <th>{{ number_format($k01Total, 2) }}</th>
      <th>{{ number_format($k01VolumTotal, 2) }}</th>
    </tr>
    <tr>
      <th>K022222</th>
      <th>{{ number_format($k02Total, 2) }}</th>
      <th>{{ number_format($k02VolumTotal, 2) }}</th>
    </tr>
    <tr>
      <th>Total</th>
      <th>{{ number_format($grandTotal, 2) }}</th>
      <th>{{ number_format($grandVolum, 2) }}</th>
    </tr>

    <tr>
      <td colspan="9">&nbsp;</td>
    </tr>

    <tr>
      <td colspan="9">No. of packages: {{ $packList->count() - $countZeroQty }} PACKAGES</td>
    </tr>
    <tr>
      @php $uniqueClients = $packList->pluck('client1')->filter()->unique(); @endphp
      <td colspan="9">Shipping Marks: {{ $uniqueClients->implode(', ') }}</td>
    </tr>
    <tr>
      <td colspan="9">Country of origin: CHINA</td>
    </tr>
  </table>
</body>

</html>