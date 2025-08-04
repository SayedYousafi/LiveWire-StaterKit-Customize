<!DOCTYPE html>
<html>

<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  <style>
    body,
    table,
    th,
    td {
      font-family: sans-serif;
    }

    table {

      width: 100%;
      border-collapse: collapse;
      border: 1px solid black;
    }

    th,
    td {
      border: 1px solid black;
      padding: 2px;
      font-size: 11px;
      white-space: nowrap;
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
  $grouped = $packList->groupBy('item_description');
  @endphp

  <table>
    <tr>
      <th colspan="9" style="text-align: center;">GTech Industries Limited</th>
    </tr>
    <tr>
      <td colspan="9" style="text-align: center;">Yongxin Road, Bowang Town, Bowang, Maanshan, Anhui, China</td>
    </tr>
    <tr>
      <th colspan="9" style="text-align: center;"> Packing List</th>
    </tr>
    <tr>
      <td colspan="8"><strong>Buyer: </strong> <br> {{ $customer->customer_company_name }}</td>
      <td style="white-space:wrap;">Invoice No.: {{ $customer->invoice_no }} <br></td>
    </tr>
    <tr>
      <td colspan="8">{{ $customer->address_line1 }} {{ $customer->postal_code }} {{ $customer->city }} {{
        $customer->country }}, Tel: {{ $customer->contact_phone }}</td>
      <td style="white-space:wrap;">Cargo No. {{ $customer->cargo_no }} </td>
    </tr>
    <tr>
      <td colspan="8">Mr. {{ $customer->contact_first_name }} {{ $customer->contact_name }}</td>
      <td>Date: {{ \Carbon\Carbon::parse($customer->created_at)->format('Y-m-d') }}</td>
    </tr>
    <tr>
      <td colspan="9">
        <flux:spacer />
      </td>
    </tr>
    <tr>
      <th rowspan="2">Description of goods</th>
      <th rowspan="2">QTY</th>
      <th rowspan="2">Clients</th>
      <th rowspan="2">Packages</th>
      <th rowspan="2">Weight (kg)</th>
      <th colspan="3">Measure (cm)</th>
      <th rowspan="2" style="white-space:wrap;">Total Volume (cbm)</th>
    </tr>
    <tr>
      <td align="center">L</td>
      <td align="center">B</td>
      <td align="center">H</td>
    </tr>
    @php
    $descriptionGroups = $packList->groupBy('item_description');
    @endphp

    @foreach ($descriptionGroups as $desc => $group)
    @php

    $rowCount = $group->count();
    $totalQty = $group->sum('item_qty');
    $first = true;

    @endphp

    @foreach ($group as $row)
    <tr>
      @if ($first)
      <td rowspan="{{ $rowCount }}" style="white-space: wrap;">{{ $desc }}</td>
      <td rowspan="{{ $rowCount }}" align="center">{{ $totalQty != 0 ? $totalQty : '' }}</td>
      @php $first = false; @endphp
      @endif

      <td align="center" style="white-space:nowrap;">{{ $row->client1 !== 'GTECH-GT' ? $row->client1 : '' }}</td>
      <td align="center">{{ $row->pallet }}</td>
      <td align="center">{{ $row->weight != 0 ? number_format($row->weight) : '' }}</td>
      <td align="center">{{ $row->length != 0 ? number_format($row->length) : '' }}</td>
      <td align="center">{{ $row->width != 0 ? number_format($row->width) : '' }}</td>
      <td align="center">{{ $row->height != 0 ? number_format($row->height) : '' }}</td>
      <td align="center">
        @php
        $volum = ($row->length * $row->width * $row->height) / 1000000;
        if($row->client1 =='GTECH-GT')
        {
        $gtechTotal += $row->weight;
        $gtechVolumTotal += $volum;
        }
        if($row->client1 =='K011111')
        {
        $k01Total += $row->weight;
        $k01VolumTotal += $volum;
        }
        if($row->client1 =='K022222')
        {
        $k02Total += $row->weight;
        $k02VolumTotal += $volum;
        }

        $grandVolum += $volum;
        $grandTotal += $row->weight;
        $grandTotalQty += $row->item_qty;
        @endphp
        {{ $volum != 0 ? number_format($volum, 2) : '' }}
      </td>
    </tr>
    @endforeach
    @endforeach
    <tr>
      <td colspan="9"></td>
    </tr>
    @php
    // Group and order rows by client1
    $clientOrder = ['GTECH-GT', 'K011111', 'K022222'];

    $clientGroups = $packList
    ->filter(fn($row) => $row->client1 !== null)
    ->sortBy(function ($row) use ($clientOrder) {
    return array_search($row->client1, $clientOrder) !== false
    ? array_search($row->client1, $clientOrder)
    : 999; // fallback for unknown clients
    })
    ->groupBy('client1');

    // Build client subtotal rows
    $clientRows = [];
    foreach ($clientGroups as $client => $rows) {
    $totalWeight = $rows->sum('weight');
    $totalVol = $rows->reduce(function ($carry, $row) {
    return $carry + ($row->length * $row->width * $row->height) / 1000000;
    }, 0);
    $clientRows[] = [
    'client' => $client,
    'weight' => number_format($totalWeight, 2),
    'volume' => number_format($totalVol, 2),
    ];
    }

    $rowspan = count($clientRows) + 1; // +1 for final 'Total' row
    @endphp

    <tr>
      <th rowspan="{{ $rowspan }}">Total</th>
      <th rowspan="{{ $rowspan }}">{{ $grandTotalQty }}</th>

      @if (!empty($clientRows))
      <th>{{ $clientRows[0]['client'] }}</th>
      <th rowspan="{{ $rowspan }}"></th>
      <th>{{ $clientRows[0]['weight'] }} kg</th>
      <th colspan="3" rowspan="{{ $rowspan }}"></th>
      <th>{{ $clientRows[0]['volume'] }} m<sup>3</sup></th>
      @endif
    </tr>

    @foreach ($clientRows as $index => $row)
    @if ($index === 0) @continue @endif
    <tr>
      <th style="white-space:nowrap;">{{ $row['client'] }}</th>
      <th>{{ $row['weight'] }} kg</th>
      <th>{{ $row['volume'] }} m<sup>3</sup></th>
    </tr>
    @endforeach

    <tr>
      <th>Total</th>
      <th>{{ number_format($grandTotal, 2) }} kg</th>
      <th>{{ round($grandVolum, 2) }} m<sup>3</sup></th>
    </tr>

    <tr>
      <td colspan="9"></td>
    </tr>
    @php
    $countPs = $packList->pluck('pallet')->unique()->count();

    // Use the correct key: 'ptype'
    $countTray = $packList->where('ptype', 'Tray')->count();
    $countWooden = $packList->where('ptype', 'Wooden Crate')->count();
    $countCarton = $packList->where('ptype', 'Carton Parcel')->count();

    $types = [];

    if ($countTray > 0) {
    $types[] = "{$countTray} Tray";
    }
    if ($countWooden > 0) {
    $types[] = "{$countWooden} Wooden Crate";
    }
    if ($countCarton > 0) {
    $types[] = "{$countCarton} Carton Parcel";
    }

    $typesText = implode(' + ', $types);
    @endphp

    <tr>
      <td colspan="9">
        No. of packages: <strong>{{ $countPs }}</strong>
        @if ($typesText)
        ({{ $typesText }})
        @endif
      </td>
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