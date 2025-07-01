<div class="container">

  <div class="flex justify-between">
    <div>
      {{-- <a href="{{ url('import') }}" class="btn btn-primary">Stock value</a> --}}
    </div>
    <h5>WareHouse</h5>
    <div>
      {{-- <a href="{{ url('wareHouseExport') }}" class="btn btn-danger">Export</a> --}}
    </div>
  </div>
  <table class="table table-default">
    <thead>
      <tr>
        <th> id </th>
        <th>ItemID</th>
        <th>ID_DE</th>
        <th> EAN </th>
        <th>Category</th>
        <th> item_name_en </th>

        <th> Qty </th>
        <th> RMB </th>
        <th>Total RMB</th>
        <th>EUR</th>
        <th>Total EUR</th>
      </tr>
    </thead>
    <tbody>
      @foreach ($wareHouses as $item )
      @php $eur= EK_net($item->supplierItem->price_rmb , $item->category_id )@endphp
      <tr>
        <td>{{ $item->id }}</td>
        <td>{{ $item->item_id }}</td>
        <td>{{ $item->ItemID_DE }}</td>
        <td><a href="{{ url('items.detailed', $item->item_id) }}">{{ $item->ean }}</a></td>
        <td>{{ $item->category->cat_name }}</td>
        <td>{{ $item->item_name_en }}</td>

        <td>{{ $q = $item->stock_qty }}</td>
        <td>{{ $p = $item->supplierItem->price_rmb }}</td>
        <td>{{ $q*$p }}</td>
        <td>{{ $eur }}</td>
        <td>{{ $q*$eur }}</td>
      </tr>
      @endforeach
    </tbody>
  </table>
  <div>{{ $wareHouses->links() }}</div>
</div>
</div>