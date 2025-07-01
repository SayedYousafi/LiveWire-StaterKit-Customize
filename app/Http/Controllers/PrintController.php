<?php
namespace App\Http\Controllers;

use App\Models\Cci_customer;
use App\Models\Order_status;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Picqer\Barcode\BarcodeGeneratorPNG;

class PrintController extends Controller
{
    public $item_ID;

    public $sid;

    public function print($id)
    {
        // dd($id);
        $data = DB::table('items')
            ->join('order_items', 'items.ItemID_DE', '=', 'order_items.ItemID_DE')
            ->join('order_statuses', 'order_statuses.master_id', '=', 'order_items.master_id')
            ->join('warehouse_items', 'warehouse_items.item_id', '=', 'items.id')

            ->where('order_items.id', $id)
            ->select('items.item_name as item_name', 'items.id as item_ID', 'items.ItemID_DE', 'warehouse_items.item_no_de',
                'items.ean', 'items.item_name_cn', 'order_items.qty', 'order_items.order_no', 'order_statuses.qty_label',
                'order_statuses.status', 'order_items.master_id', 'order_items.remark_de', 'order_statuses.id as sid',
                'order_items.qty', 'order_statuses.remarks_cn', 'items.remark'
            )->first();
        // dd($data, $id);
        $ean           = $data->ean;
        $this->item_ID = $data->item_ID;

        $this->sid = $data->sid;

        $generator = new BarcodeGeneratorPNG;
        $barcode   = base64_encode($generator->getBarcode($ean, $generator::TYPE_EAN_13));

        $pdf = Pdf::loadView('partials.print', compact('barcode', 'data'));
        $pdf->setPaper([0, 0, 300, 110]);
        // display on screen later i will change to download
        $this->editStatus($this->sid);
        // return $pdf->stream();
        $file_name = 'Label_' . $ean . '.pdf';

        return $pdf->download("$file_name");
        // finally update the status to printed
    }

    public function editStatus()
    {
        // dd($this->sid, 'Success in print');
        Order_status::where('id', $this->sid)->update(
            [
                'status'  => 'Printed',
                'printed' => 'Y',
            ]);
        // $this->trackPrint();
        session()->flash('success', 'wow -- Printed successfully !, check out your Label Printer now');
        // return redirect()->to('so');
    }

    public function newInvoice($id, $sn)
    {
        \DB::statement('SET SESSION sql_mode = ""');
        // dd($id, $sn);
        $data = DB::table('cci_invoices')
            ->join('cci_customers', 'cci_invoices.cci_customer_id', '=', 'cci_customers.id')
        // ->join('tcustomers', 'tcustomers.id', '=', 'cci_customers.customer_id')
            ->where('cci_invoices.cci_customer_id', $id)
            ->where('cci_invoices.invSerialNo', $sn)
            ->select(
                'cci_customers.*',
                'cci_customers.country as Country_Name',
                'cci_invoices.*',
                'cci_invoices.remark as REMARK',
                \DB::raw('SUM(cci_invoices.taric_code) AS taric_code_count'),
                \DB::raw('SUM(cci_invoices.total_qty) AS total_qty'),
                \DB::raw('SUM(cci_invoices.total_price) AS total_price'),
            )
            ->groupBy(
                'cci_invoices.taric_code' // cci_invoices.taric_code
            )
            ->orderBy('cci_invoices.id', 'ASC')
            ->get();
        // dd($data);
        // Create the PDF
        $pdf       = Pdf::loadView('partials.invoice', compact('data'));
        $file_name = 'invoice.pdf';

        return $pdf->stream();
    }
    public function packList($date)
    {

        // Format the date to only keep the Y-m-d part
        //$formattedDate = Carbon::parse($date)->format('Y-m-d');

        $packList = DB::table('packing_lists as t1')
            ->join('cci_customers as c', 'c.customer_id', '=', 't1.customer_id')
            ->where('t1.created_at', $date)
            ->select(
                't1.id',
                't1.customer_id',
                DB::raw("CASE
                        WHEN t1.item_qty = 0 AND EXISTS (
                            SELECT 1
                            FROM packing_lists t2
                            WHERE t2.item_description = t1.item_description
                              AND t2.item_qty != 0
                        )
                        THEN NULL
                        ELSE t1.item_description
                    END AS item_description"),
                't1.item_qty',
                't1.client1',
                't1.pallet',
                't1.weight',
                't1.length',
                't1.width',
                't1.height',
                't1.created_at',
                'c.customer_company_name',
                'c.contact_name',
                'c.contact_first_name',
                'c.address_line1',
                'c.postal_code',
                'c.city',
                'c.country',
                'c.contact_phone'
            )
            ->distinct()
            ->get();
        //dd($packList);

        //$countZeroQty = $packList->where('packing_lists.item_qty', '=', 0)->count();
        $countZeroQty = $packList->where('item_qty', 0)->count();

        $pdf = Pdf::loadView('partials.packList', compact('packList', 'countZeroQty'))
            ->setPaper('a4', 'landscape');
        $file_name = 'PackingList.pdf';

        return $pdf->stream();
    }
}
