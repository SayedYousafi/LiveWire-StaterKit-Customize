<style>
  html,
  body {
    margin: 15;
    padding: 0;
    height: 100%;
    font-family: Arial, Helvetica, sans-serif;
  }

  table {
    width: 95%;
    border-collapse: collapse;
    margin: 0 auto;
    /* Center the table */
  }

  table tr td {
    padding: 1;
  }

  img.header {
    position: fixed;
    top: 15px;
    left: 12px;
    right: 0;
    width: 95%;
    margin: 0 auto;
    /* Center the header */
    display: block;
  }

  img.footer {
    position: fixed;
    bottom: 15px;
    left: 12px;
    right: 0;
    width: 95%;
    margin: 0 auto;
    /* Center the footer */
    display: block;
  }

  .content {
    padding-top: 125px;
    /* Adjust based on the height of your header image */
    padding-bottom: 100px;
    /* Adjust based on the height of your footer image */
  }
.myFontSize{
  font-size: 0.9em;
}


  #items th,
  #items td {
    padding: 1px;
    word-wrap: normal;
    

  }

  pre {
    font-family: inherit;
    /* Use the same font as the rest of the document */
    /* Use the same font size as the surrounding content */
    white-space: pre-wrap;
    /* Allow text to wrap if too long */
    margin: 0;
    padding: 0;
   
  }

  p {
    margin: 15;
   
  }
  .dbUnderline {
    border-bottom:3px double;
  }
</style>

<body>

  <img src="./images/Header.jpg" alt="Header" class="img header">

  <div class="content">
    
    <table>
      <tr>
        @foreach ($data as $item)
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
      </tr>
      <tr>
        @if(!empty($item->delivery_address_line1))
        <td><strong>BILL TO:</strong></td>
        <td rowspan="5" valign="top">
          <table border="0" align="center">
            <tr>
              <td><strong>SHIP TO:</strong></td>
            </tr>
            <tr>
              <td>{{ $item->delivery_company_name  }}</td>
            </tr>
            <tr>
              <td>{{ $item->delivery_address_line1 }}</td>
            </tr>
            <tr>
              <td>{{ $item->delivery_address_line2 }} </td>
            </tr>
            <tr>
              <td>  {{ $item->delivery_postal_code }}, {{ $item->delivery_city }}</td>
            </tr>
            <tr>
              <td> {{  $item->delivery_country }} </td>
            </tr>
            <tr>
              <td>{{ $item->delivery_contact_phone }}</td>
            </tr>
          </table>
        </td>
        @endif
        <td align="right">&nbsp;</td>
      </tr>
      <tr>
        <td>{{ $item->customer_company_name }}</td>
        <td align="right">Customer No: <strong>{{ $item->id }}</strong></td>
      </tr>
      <tr>
        <td>{{ $item->address_line1 }}</td>
        <td align="right">Date: <strong>{{ myDate( $item->created_at, 'Y-m-d') }}</strong></td>
      </tr>
      <tr>
        <td>{{ $item->address_line2 }} {{ $item->postal_code }} {{ $item->city }} <br /> {{ $item->Country_Name }}</td>
        <td align="right">Invoice No: <strong>{{__("CI2500"). $item->id }}</strong></td>
        
      </tr>
      <tr>
        <td>{{ $item->phone }}</td>
        <td align="right">Cargo No.: <strong> {{ $item->cargo_no }} </strong></td>
      </tr>
      <tr>
        <td><strong>{{ $item->tax_no }}</strong> </td>
        <td align="center" valign="middle">&nbsp;</td>
        <td align="center">&nbsp;</td>
      </tr>
      @break
      @endforeach
      <tr>
        <td colspan="10" align="center" valign="middle"><strong>Commercial Invoice</strong></strong></td>
      </tr>
      <tr>
        <td colspan="10">
          <br>
          <hr style="border-top: 1px dashed red;">
        </td>
      </tr>

    </table>

    <table id="items">
      <thead>
        <tr class="myFontSize">
          <th nowrap>No &nbsp;</th>
          <th  align="left">&nbsp; Description</th>
          <th style="word-wrap: break-word;">Taric <br> (EU HS code)</th>
          <th >Qty <br> (pcs)</th>
          <th >Unit <br> (€)*</th>
          <th  align="right">Price <br> (€)</th>
        </tr>
        <tr>
          <td colspan="6">
            <hr>
          </td>
        </tr>
      </thead>
      <tbody>
        @php
        $tq = 0;
        $tp = 0;
        @endphp
        @foreach ($data as $item)
        <tr style="font-size: 0.85em;">
          @php
          $q = $item->total_qty;
          $p = $item->total_price;
          $u = $q == 0 ? 0 : ($p / $q); // Handle division by zero
          $tq = $tq + $q;
          $tp = $tp + $p;
          // check if the values of taric is n/a make qty and unit price n/a too
          if ($item->taric_code == 'n/a') {
          $u = 'n/a';
          $q = 'n/a';
          }
          @endphp
          <td>{{ $loop->iteration }}</td>
          <td style="word-wrap: break-word;">{{ $item->taric_nameEN }}</td>
          <td align="center" nowrap="nowrap">{{ $item->taric_code }}</td>
          <td align="center">{{ $q }}</td>
          @php
          if ($u != 'n/a')
          {
          $u=number_format($u, 3, '.', '');
          }
          @endphp

          <td align="center">{{ $u }}</td>

          <td align="right">{{ number_format($p,2, '.','') }}</td>
        </tr>
        @endforeach

        <tr>
        <tr>
          <td colspan="6">
            <hr style="border: 1px solid gray">
          </td>
        </tr>
        <tr style="font-size: 0.9em;">
        <td></td>
        <td></td>
        <th> <span class="dbUnderline">Total :</span> </th>
        <th> <span class="dbUnderline">{{ $tq }}</span></th>
        <th></th>
        <th align="right"> <span class="dbUnderline"> {{ number_format($tp, 2,'.','' ) }}&nbsp;€</span></th>
        </tr>
      </tbody>
    </table>
    <p class="myFontSize"> <strong> * Unit price is calculated and can have errors from rounding</strong></p>
    <table style="width: 100%; margin: 15">
      <tbody>
        <tr>
          <td width="5">Remark:</td>
          <td width="15">&nbsp;</td>
          <td nowrap="nowrap" style=" text-align: left;"> 
            {{ $data->filter(fn($item) =>
            !is_null($item->cargo_type))->first()->cargo_type ?? 'No cargo type available' }}, 
            {{ $data->filter(fn($item) => 
            !is_null($item->cargo_no))->first()->cargo_no ?? 'No cargo assigned' }}
          </td>
        <tr>
          <td></td>
          <td></td>
          <td nowrap="nowrap">
            <pre>{{ $data->filter(fn($item) => !is_null($item->REMARK))->first()->REMARK ?? 'No remark available' }}
          </pre>
          </td>
        </tr>
        <tr>
          <td></td><td></td>
          <td colspan="5">
            We hereby confirm that no raw material from Russia were used <br> in the production of the goods mentioned in this invoice.
          </td>
        </tr>
      </tbody>
    </table>

  </div>
 
  <img src="./images/footer.png" alt="Footer" class="img footer">
</body>