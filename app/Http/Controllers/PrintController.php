<?php

namespace App\Http\Controllers;

use App\Models\Order_status;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;
use Picqer\Barcode\BarcodeGeneratorPNG;

class PrintController extends Controller
{
    public $item_ID, $sid;

    public function print($id)
    {
        //dd($id);
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
        //dd($data, $id);
        $ean = $data->ean;
        $this->item_ID = $data->item_ID;

        $this->sid = $data->sid;

        $generator = new BarcodeGeneratorPNG();
        $barcode = base64_encode($generator->getBarcode($ean, $generator::TYPE_EAN_13));
        
        $pdf = Pdf::loadView('partials.print', compact('barcode', 'data'));
        $pdf->setPaper([0, 0, 300, 110]);
        // display on screen later i will change to download
        $this->editStatus($this->sid);
        //return $pdf->stream();
        $file_name = "Label_" . $ean . ".pdf";
        return $pdf->download("$file_name");
        //finally update the status to printed
    }

    public function editStatus()
    {
        //dd($this->sid, 'Success in print');
        Order_status::where('id', $this->sid)->update(
            [
                'status' => 'Printed',
                'printed' => 'Y',
            ]);
        //$this->trackPrint();
        session()->flash('success', 'wow -- Printed successfully !, check out your Label Printer now');
        //return redirect()->to('so');
    }

     public function newInvoice($id, $sn)
    {
        \DB::statement('SET SESSION sql_mode = ""');
        //dd($id, $sn);
        $data = DB::table('cci_invoices')
            ->join('cci_customers', 'cci_invoices.cci_customer_id', '=', 'cci_customers.id')
            //->join('tcustomers', 'tcustomers.id', '=', 'cci_customers.customer_id')
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
                'cci_invoices.taric_code' //cci_invoices.taric_code
            )
            ->orderBy('cci_invoices.id', 'ASC')
            ->get();
        //dd($data);
        // Create the PDF
        $pdf = Pdf::loadView('partials.invoice', compact('data'));
        $file_name = 'invoice.pdf';
        return $pdf->stream();
    }
}
