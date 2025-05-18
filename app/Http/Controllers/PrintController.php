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
}
