<!DOCTYPE html>
<html>

<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  <style>
    @font-face {
      font-family: 'Firefly Sung';
      font-style: normal;
      font-weight: 400;
      
      src: url("{{ storage_path('fonts/fireflysung.ttf') }}") format('truetype');
    }
    * {
      font-family: Firefly Sung, DejaVu Sans, sans-serif;
    }
  </style>
</head>

<body>
     <div>(忠烈祠)</div>
    <div>Packing List (测试中文字符)</div>

    <table>
        <thead>
            <tr>
                <th>Description</th>
                <th>QTY</th>
                <th>Chinese Description</th>
                <th>Normal CN text</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($items as $item)
            <tr>
                <td>{{ $item->item_description }}</td>
                <td>{{ $item->item_qty }}</td>
                <td>{{ $item->cn_description }}</td>
                <td>测试中文字符</td>

            </tr>
            @endforeach
        </tbody>
    </table>
</body>

</html>