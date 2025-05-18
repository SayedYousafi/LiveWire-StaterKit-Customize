<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Printing Labels</title>
    <style>
        .barcode {
            /* Adjust the height of the barcode image as needed */
            height: 45px;
            width: 125px;
        }

        html,
        body {
            width: 385px;
            /* Adjusted to accommodate the margins */
            height: 150px;
            /* Adjusted to accommodate the margins */
            margin-top: 0.5%;
            margin-bottom: 0;
            margin-right: 0.5%;
            margin-left: 0.5%;
        }

        .td,
        .th {
            font-family: Arial, "Arial Black" !important;
        }
    </style>
</head>

<body>

    <table cellpadding='0' align="center" cellspacing="0" border="0" width="385px" height="150px">
        <tr>
            <td class="td">ItemNoW:</td>
            <td align="center" valign="middle" nowrap="nowrap" class="td">Order No<sub><em>/Qty</em></sub> </td>
            <td align="right" valign="middle" class="td">&nbsp;</td>
            <td align="right" valign="middle" nowrap="nowrap"><span class="td">Qty:</span></td>
            <td rowspan="2" align="right" valign="middle" nowrap="nowrap">

                <span class="td">
                    @if(str_contains($data->item_name, 'K011111') || str_contains($data->remarks_cn, 'K011111'))
                    <img src="img/Sayed_K011111_logo.png" width="70">
                    @elseif(str_contains($data->item_name, 'K022222') || str_contains($data->remarks_cn, 'K022222'))
                    <img src="img/Sayed_blank_Logo.png" width="70">
                    @else
                    <img src="img/Sayed_GTech.png" width="70">
                    @endif
                </span>
            </td>
        </tr>
        <tr class="th">
            <th align="left" nowrap="nowrap">{{ $data->item_no_de }}</th>
            <th align="center" valign="middle" nowrap="nowrap">{{ $data->order_no }}<em><sub>/{{ $data->qty
                        }}</sub></em></th>
            <th align="right" valign="middle" nowrap="nowrap">&nbsp;</th>
            <th align="right" valign="middle" nowrap="nowrap"> {{ $data->qty_label }} </th>
        </tr>

        <tr>
            <td colspan="5" align="center" valign="middle"><em class="td">{{ Str::limit($data->item_name, 95) }}</em>
            </td>
        </tr>
        <tr>
            <td colspan="3" valign="middle"><span class="td">RemarkCN: {{ Str::limit($data->remarks_cn, 25) }}</span>
            </td>
            <td colspan="2" rowspan="2" align="right" valign="middle">
                <span class="td"><img class="barcode" src="data:image/png;base64, {{ $barcode }}"></span><br>
                <span style="align-content: center">{{ $data->ean }}</span>
            </td>
        </tr>
        <tr>
            <td colspan="3" valign="middle" nowrap="nowrap"><span class="td">RemarkW: {{ Str::limit($data->remark_de,
                    24) }}</span></td>
        </tr>
    </table>

</body>

</html>