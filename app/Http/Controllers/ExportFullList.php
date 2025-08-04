<?php
namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Parents;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Response;

class ExportFullList extends Controller
{
    public function exportCSV(Request $request, $exportType = 'all')
    {
        $parentId = $request->input('parent_id');

        date_default_timezone_set('Europe/Berlin');

        $query = Parents::join('items', 'parents.id', '=', 'items.parent_id')
            ->join('variation_values', 'items.id', '=', 'variation_values.item_id')
            ->join('supplier_items', 'items.id', '=', 'supplier_items.item_id')
            ->join('warehouse_items', 'warehouse_items.item_id', '=', 'items.id')
            ->join('tarics', 'tarics.id', '=', 'items.taric_id')
            ->select([
                'parents.*', 'items.*', 'items.id as item_id', 'variation_values.*',
                'supplier_items.*', 'warehouse_items.*', 'tarics.*',
            ])
            ->where('supplier_items.is_default', '=', 'Y');

        // Export filters
        switch ($exportType) {
            case 'updated':
                $query->whereNotNull('items.updated_at')
                    ->whereColumn('items.synced_at', '<=', 'items.updated_at');
                break;
            case 'isNew':
                $query->where('items.is_new', '=', 'Y');
                break;
        }

        if ($parentId) {
            $query->where('parents.id', '=', $parentId);
        }

        // Apply filtering based on shipping class
        $result        = $this->filterValidShippingRecords(clone $query);
        $records       = collect($result['valid'])->sortBy('item_id');
        $excludedCount = $result['invalid_count'];

        if ($records->isEmpty()) {
            session()->flash('error', 'No valid records found for export.');
            return redirect()->back();
        }

        // Optional flash message (remove redirect so export proceeds)
        session()->flash('success', "Exported successfully. {$excludedCount} record(s) were excluded due to invalid shipping class.");
        // return redirect()->back(); <-- Commented out to allow export to proceed

        // Mark items as exported if needed
        if ($exportType === 'isNew') {
            $this->updateExportedStatus($records->pluck('item_id')->toArray());
        }

        if ($exportType === 'updated') {
            $this->synced_at($records->pluck('ean')->toArray());
        }

        $filenamePrefix = match ($exportType) {
            'updated' => 'updated_Item_List_',
            'isNew' => 'Export_NewItems_',
            default => 'Item_Full_List_',
        };

        $filename = $filenamePrefix . date('Y-m-d_H-i-s') . '.csv';

        $headers = [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
            'Pragma'              => 'no-cache',
            'Expires'             => '0',
        ];

        $columns = [

            'ID',
            'EAN',
            'Parent No DE',
            'Item No DE',
            'Sup_cat',
            'Item Name DE',
            'Variation DE 1',
            'Value DE',
            'Variation DE 2',
            'Value DE 2',
            'Variation DE 3',
            'Value DE 3',
            'Item Name EN',
            'Item Name',
            'Variation EN 1',
            'Value EN',
            'Variation EN 2',
            'Value EN 2',
            'Variation EN 3',
            'Value EN 3',
            'Code',
            'ISBN',
            'Width',
            'Height',
            'Length',
            'Weight',
            'Shipping Weight',
            'Shipping Class',
            'Is Qty Dividable',
            'Is Stock Item',
            'FOQ',
            'FSQ',
            'MSQ',
            'MOQ Result',
            'Interval',
            'Buffer Result',
            'Price RMB',
            'Y/N',
            'Many Components',
            'Effort Rating',
            'EK Net',
            'Item Volume (dm³)',
            'Freight Costs Volume',
            'Freight Costs Weight',
            'Freight Costs',
            'Import Duty Charge (EUR)',
            'SP_eBay',
            'SP_DE_NET_1',
            'SP_DE_NET_2',
            'SP_DE_NET_5',
            'SP_DE_NET_10',
            'SP_DE_NET_25',
            'SP_DE_NET_50',
            'SP_DE_NET_100',
            'SP_DE_NET_200',
            'SP_DE_NET_500',
            'SP_DE_NET_1000',
            'SP_DE_NET_2000',
            'BulkQty_2',
            'BulkQty_5',
            'BulkQty_10',
            'BulkQty_25',
            'BulkQty_50',
            'BulkQty_100',
            'BulkQty_200',
            'BulkQty_500',
            'BulkQty_1000',
            'BulkQty_2000',
            'USt %',
            'Dummy-Bild01',
            'Image Path EAN',
            'Image Path eBay',
            'Max Quantity',
        ];

        $callback = function () use ($records, $columns) {
            $file = fopen('php://output', 'w');
            fprintf($file, "\xEF\xBB\xBF"); // Add UTF-8 BOM
            fputcsv($file, $columns, ';');  // Use semicolon delimiter for header row

            foreach ($records as $record) {
                // Constants
                $SW = config('constants.SW');

                $supplier = ''; // Default value to prevent undefined variable error
                $supp1    = $record->supp_cat ?? null;
                if ($supp1 !== null) {
                    $supplier = in_array($supp1, ['TES', 'TEW', 'TLE', 'TMS', 'TMT', 'TOP', 'TSC', 'TTI', 'ONE']) ? 'STD' : $supp1;
                }
                $Width  = $record->width;
                $Height = $record->height;
                $Length = $record->length;
                $Weight = $record->weight;

                $PP_RMB      = $record->price_rmb;
                $MSQ         = round($record->FSQ * 0.7);
                $IsPUItem    = $record->is_pu_item;
                $IsMeterItem = $record->is_meter_item;

                $buffer_result = Buffer($MSQ, $PP_RMB, $IsPUItem, $IsMeterItem);
                $moq_result    = SupplierMOQ($MSQ, $PP_RMB, $IsPUItem, $IsMeterItem);
                $interval      = SUPPLIERINTERVAL($moq_result, $PP_RMB, $IsPUItem, $IsMeterItem);
                $ShippingClass = ShippingClass($Weight, $Length, $Width, $Height);

                $EK_net                   = EK_net($PP_RMB, $record->cat_id);
                $ItemVolumeDM3            = ($Length * $Width * $Height) / 1000;
                $FreightCostsVolume       = FreightCostsVolume($Weight, $ItemVolumeDM3);
                $FreightCostsWeight       = FreightCostsWeight($Weight, $ItemVolumeDM3);
                $FreightCosts             = max($FreightCostsWeight, $FreightCostsVolume);
                $duty_rate                = $record->duty_rate;
                $ImportDutyChargeEUR      = $duty_rate / 100 * ($EK_net + $FreightCosts);
                $PurchasePriceEUR         = $EK_net;
                $OrderQty                 = 2;
                $ManyComponentsItemRating = $record->many_components;
                $ItemEffortRating         = $record->effort_rating;

                $SPdeNET = function ($qty) use ($PurchasePriceEUR, $FreightCosts, $ImportDutyChargeEUR, $ManyComponentsItemRating, $ItemEffortRating) {
                    return SPdeNET($PurchasePriceEUR, $qty, $FreightCosts, $ImportDutyChargeEUR, $ManyComponentsItemRating, $ItemEffortRating);
                };

                $SPebay = SPebay($SPdeNET(1));

                $SPdeNET1    = $SPdeNET(1);
                $SPdeNET2    = $SPdeNET(2);
                $SPdeNET5    = $SPdeNET(5);
                $SPdeNET10   = $SPdeNET(10);
                $SPdeNET25   = $SPdeNET(25);
                $SPdeNET50   = $SPdeNET(50);
                $SPdeNET100  = $SPdeNET(100);
                $SPdeNET200  = $SPdeNET(200);
                $SPdeNET500  = $SPdeNET(500);
                $SPdeNET1000 = $SPdeNET(1000);
                $SPdeNET2000 = $SPdeNET(2000);

                // Calculated Shipping Weight
                $shipping_weight = (($SW / 100) * $Weight) + $Weight;

                fputcsv($file, [
                    $record->item_id ?? '',                              // ID
                    $record->ean ?? '',                                  // EAN
                    $record->parent_no_de ?? '',                         // Parent No DE
                    $record->item_no_de ?? '',                           // Item No DE
                    $supplier ?? '',                                     // Supplier
                    $record->item_name_de ?? '',                         // Item Name DE
                    $record->variation_de_1 ?? '',                       // Variation DE 1
                    $record->value_de ?? '',                             // Value DE
                    $record->variation_de_2 ?? '',                       // Variation DE 2
                    $record->value_de_2 ?? '',                           // Value DE 2
                    $record->variation_de_3 ?? '',                       // Variation DE 3
                    $record->value_de_3 ?? '',                           // Value DE 3
                    $record->item_name_en ?? '',                         // Item Name EN
                    $record->item_name ?? '',                            // Item Name
                    $record->variation_en_1 ?? '',                       // Variation EN 1
                    $record->value_en ?? '',                             // Value EN
                    $record->variation_en_2 ?? '',                       // Variation EN 2
                    $record->value_en_2 ?? '',                           // Value EN 2
                    $record->variation_en_3 ?? '',                       // Variation EN 3
                    $record->value_en_3 ?? '',                           // Value EN 3
                    $record->code ?? '',                                 // Code
                    $record->ISBN ?? '',                                 // ISBN
                    $record->width ?? '',                                // Width
                    $record->height ?? '',                               // Height
                    $record->length ?? '',                               // Length
                    $record->weight ?? '',                               // Weight
                    $shipping_weight,                                    // Shipping Weight
                    $ShippingClass ?? '',                                // Shipping Class
                    $record->is_qty_dividable ?? '',                     // Is Qty Dividable
                    $record->is_stock_item ?? '',                        // Is Stock Item
                    $record->FOQ ?? '',                                  // FOQ
                    $record->FSQ ?? '',                                  // FSQ
                    $MSQ ?? '',                                          // MSQ
                    $moq_result ?? '',                                   // MOQ Result
                    $interval ?? '',                                     // Interval
                    $buffer_result ?? '',                                // Buffer Result
                    $record->price_rmb ?? '',                            // Price RMB
                    'Y',                                                 // Y/N (Constant)
                    $record->many_components ?? '',                      // Many Components
                    $record->effort_rating ?? '',                        // Effort Rating
                    $EK_net ?? '',                                       // EK Net
                    number_format($ItemVolumeDM3 ?? 0, 2, '.', ''),      // Item Volume (dm³)
                    number_format($FreightCostsVolume ?? 0, 2, '.', ''), // Freight Costs Volume
                    number_format($FreightCostsWeight ?? 0, 2, '.', ''), // Freight Costs Weight
                    number_format($FreightCosts ?? 0, 2, '.', ''),       // Freight Costs
                    number_format($ImportDutyChargeEUR ?? 0, 2),         // Import Duty Charge (EUR)
                    number_format($SPebay ?? 0, 2, '.', ''),             // SP eBay
                    number_format($SPdeNET1 ?? 0, 2, '.', ''),           // SP DE NET 1
                    number_format($SPdeNET2 ?? 0, 2, '.', ''),           // SP DE NET 2
                    number_format($SPdeNET5 ?? 0, 2, '.', ''),           // SP DE NET 5
                    number_format($SPdeNET10 ?? 0, 2, '.', ''),          // SP DE NET 10
                    number_format($SPdeNET25 ?? 0, 2, '.', ''),          // SP DE NET 25
                    number_format($SPdeNET50 ?? 0, 2, '.', ''),          // SP DE NET 50
                    number_format($SPdeNET100 ?? 0, 2, '.', ''),         // SP DE NET 100
                    number_format($SPdeNET200 ?? 0, 2, '.', ''),         // SP DE NET 200
                    number_format($SPdeNET500 ?? 0, 2, '.', ''),         // SP DE NET 500
                    number_format($SPdeNET1000 ?? 0, 2, '.', ''),        // SP DE NET 1000
                    number_format($SPdeNET2000 ?? 0, 2, '.', ''),        // SP DE NET 2000
                    2, 5, 10, 25, 50, 100, 200, 500, 1000, 2000, 0,      // Fixed Columns
                    $record->photo,
                    // Image Path EAN
                    'R:\\205.6_Pictures_&_Videos\\205.6.1_Product_pictures\\205.6.1.2_ChildPictures\\MIS_shop_by_EAN\\' . ($record->pix_path ?? ''),
                    // Image Path eBay
                    'R:\\205.6_Pictures_&_Videos\\205.6.1_Product_pictures\\205.6.1.2_ChildPictures\\MIS_ebay_by_ItemID_DE\\' . ($record->pix_path_eBay ?? ''),
                    10000,   // Max Quantity
                ], ';'); // Use semicolon delimiter for each data row
            }

            fclose($file);
        };

        session()->flash('success', "Exported successfully. {$excludedCount} record(s) were excluded due to invalid shipping class.");
        return Response::stream($callback, 200, $headers);
    }

    public function synced_at($itemIds)
    {
        date_default_timezone_set('Europe/Berlin');
        $currentTimestamp  = Carbon::now()->setTimezone('Europe/Berlin');
        $syncedAtTimestamp = $currentTimestamp->copy()->addSeconds(10);

        Item::whereIn('ean', $itemIds)->update([
            'updated_at' => $currentTimestamp,
            'synced_at'  => $syncedAtTimestamp,
        ]);
    }

    // Function to update 'exported' column after successful export
    public function updateExportedStatus($itemIds)
    {
        // dd($itemIds);
        Item::whereIn('id', $itemIds)->update(['is_new' => 'N']);
    }

    // function to do not export if shipting has na
    private function hasInvalidShippingClass($query)
    {
        // Clone query to avoid modifying original one
        $records = (clone $query)->get();

        foreach ($records as $record) {
            $ShippingClass = ShippingClass($record->weight, $record->length, $record->width, $record->height);
            if ($ShippingClass == 'Na') {
                return true; // Stop export if 'Na' is found
            }
        }

        return false; // Continue export if no issues
    }

    private function filterValidShippingRecords($query)
    {
        $records = (clone $query)->get();

        $validRecords = [];
        $invalidCount = 0;

        foreach ($records as $record) {
            $shippingClass = ShippingClass($record->weight, $record->length, $record->width, $record->height);

            if ($shippingClass === 'Na') {
                $invalidCount++;
                continue; // Skip this record
            }

            $validRecords[] = $record;
        }

        return [
            'valid'         => $validRecords,
            'invalid_count' => $invalidCount,
        ];
    }

    // function to export confirms items
    public function confirmed()
    {
        dd('coming soon');
        // $results = Confirm::join('supplier_items as si', 'si.id', '=', 'confirms.supp_items_id')
        // ->join('items as i', 'i.id', '=', 'si.item_id')
        // ->select('confirms.supp_items_id', 'i.ean', 'i.item_name', 'si.supplier_id', 'si.price_rmb', 'si.url', 'confirms.created_at', 'confirms.confirm_by')
        // ->where('si.is_default', 'Y')
        // ->orderByDesc('confirms.created_at')
        // ->get();
    }
}
