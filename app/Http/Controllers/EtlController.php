<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Order;
use App\Models\Order_item;
use App\Models\Order_status;
use App\Models\Warehouse;

class EtlController extends Controller
{
    public $csvFilePath;

    public $targetDate;

    public $fileContents;

    public function convertToMySQLDateFormat($dateStr)
    {
        // Define an array of month names and their corresponding numeric representation
        $monthNames = [
            'Jan' => '01', 'Feb' => '02', 'Mär' => '03', 'Apr' => '04',
            'Mai' => '05', 'Jun' => '06', 'Jul' => '07', 'Aug' => '08',
            'Sep' => '09', 'Okt' => '10', 'Nov' => '11', 'Dez' => '12',
        ];

        // Regular expression to match the different date formats in the CSV file
        preg_match('/(\w+|\p{L}+)\s+(\d{1,2})\s+(\d{4})/u', $dateStr, $matches);

        if (count($matches) === 4) {
            $month = $monthNames[$matches[1]];
            $day = str_pad($matches[2], 2, '0', STR_PAD_LEFT);
            $year = $matches[3];

            return "$year-$month-$day";
        }

        return null; // Return null if the date format does not match
    }

    public function findTargetDate()
    {
        // dd(' I have been called');
        $this->csvFilePath = public_path('orders.csv');
        $this->targetDate = date('Y-m-d');

        $foundOrders = [];

        if (($handle = fopen($this->csvFilePath, 'r')) !== false) {
            // Assuming the first row contains headers, we skip it
            fgetcsv($handle);

            while (($data = fgetcsv($handle)) !== false) {
                $d = strtotime($data[4]); // the "date_order_created" column is at index 4
                $dateCreated = date('Y-m-d', $d);
                // dd($dateCreated, $this->targetDate);
                // Convert the date format to MySQL date format (only Y-m-d)
                // $mysqlDate = $this->convertToMySQLDateFormat($dateCreated);
                if ($dateCreated === $this->targetDate) {

                    // dd('Match found');
                    // Create Order model instance and save data
                    Order::create([
                        'order_no' => $data[0],
                        'category_id' => $data[1],
                        'status' => $data[2],
                        'comment' => $data[3],
                        'date_created' => $data[4],
                        'date_emailed' => $data[5],
                        'date_delivery' => $data[6],

                    ]);

                    $foundOrders[] = $data;
                }
            }
            fclose($handle);

        }
        // Process order_items.csv
        $orderItemsFilePath = public_path('order_items.csv');
        if (($orderItemsHandle = fopen($orderItemsFilePath, 'r')) !== false) {
            // Assuming the first row contains headers, we skip it
            fgetcsv($orderItemsHandle);

            while (($itemData = fgetcsv($orderItemsHandle)) !== false) {
                $orderNo = $itemData[2]; // the order number column is at index 1

                // Check if the order number matches any found order
                foreach ($foundOrders as $order) {
                    if ($order[0] === $orderNo) {
                        // Create Itemorder model instance and save data
                        Order_item::create([
                            'order_no' => $orderNo,
                            'master_id' => $itemData[0],
                            'ItemID_DE' => $itemData[1],
                            'qty' => $itemData[3],
                            'remark_de' => $itemData[4],
                            'qty_delivered' => $itemData[5],
                        ]);
                    }
                }
            }
            fclose($orderItemsHandle);
        }
        $this->statusRemark();

        return redirect()->back()->with('success', 'Orders and associated items synchronized successfully.');
    }

    public function removeUnmatchedOrders()
    {
        // Path to the orders.csv file
        $ordersFilePath = public_path('orders.csv');

        // Array to store the order_no values found in the orders.csv file
        $csvOrderNos = [];

        // Read orders.csv file
        if (($handle = fopen($ordersFilePath, 'r')) !== false) {
            // Assuming the first row contains headers, we skip it
            fgetcsv($handle);

            while (($data = fgetcsv($handle)) !== false) {
                $csvOrderNos[] = $data[0]; // Store order_no
            }
            fclose($handle);
        }

        // Get the order_no values from the Order model excluding those containing "DENI" becuase they are generated from MIS
        $existingOrderNos = Order::where('order_no', 'Not Like', '%DENI%')->pluck('order_no')->toArray();

        // Find unmatched order_no values
        $unmatchedOrderNos = array_diff($existingOrderNos, $csvOrderNos);

        // Delete records from Order model for unmatched order_no values
        if (! empty($unmatchedOrderNos)) {
            Order::whereIn('order_no', $unmatchedOrderNos)->delete();
        }

    }

    public function statusRemark()
    {
        $orderItemsNotInStatusRemarkQty = Order_item::whereNotIn('master_id', function ($query) {
            $query->select('master_id')->from('order_statuses');
        })->get();

        // Then, insert the selected records into status_remark_qty // order_statuses
        foreach ($orderItemsNotInStatusRemarkQty as $orderItem) {
            Order_status::create([
                'master_id' => $orderItem->master_id,
                'ItemID_DE' => $orderItem->ItemID_DE,
                'qty_label' => $orderItem->qty,
                'qty_split' => $orderItem->qty,
            ]);

        }
        // remove processed orders;
        $this->removeUnmatchedOrders();
        // remove processed ordered items
        $this->removeUnmatchedItems();
        // update qty in order_item
        $this->updateOrderItemQty();
        return redirect('/orders');
    }

    public function removeUnmatchedItems()
    {
        // Path to the order_items.csv file
        $orderItemsFilePath = public_path('order_items.csv');

        // Array to store the master_ids found in the order_items.csv file
        $orderItemMasterIds = [];

        // Read order_items.csv file
        if (($orderItemsHandle = fopen($orderItemsFilePath, 'r')) !== false) {
            // Assuming the first row contains headers, we skip it
            fgetcsv($orderItemsHandle);

            while (($itemData = fgetcsv($orderItemsHandle)) !== false) {
                $orderItemMasterIds[] = $itemData[0]; // Store master_id
            }
            fclose($orderItemsHandle);
        }

        // Get the master_id values from the Itemorder model excluding those containing "MIS"
        $existingItemOrderMasterIds = Order_item::where('master_id', 'Not Like', '%MIS%')->pluck('master_id')->toArray();

        // Find unmatched master_ids
        $unmatchedMasterIds = array_diff($existingItemOrderMasterIds, $orderItemMasterIds);

        // Delete records from Itemorder and Status models for unmatched master_ids
        if (! empty($unmatchedMasterIds)) {
            Order_item::whereIn('master_id', $unmatchedMasterIds)->delete();
            Order_status::whereIn('master_id', $unmatchedMasterIds)->delete();
        }

    }

    public function updateOrderItemQty()
    {
        // master_id,ItemID_DE,order_no,qty,remark_de,qty_delivered
        // Read the CSV file
        $orderItemsFilePath = public_path('order_items.csv');
        if (($handle = fopen($orderItemsFilePath, 'r')) !== false) {
            // Assuming the first row contains headers,  skip it
            fgetcsv($handle);

            while (($data = fgetcsv($handle)) !== false) {
                $master_id = $data[0];
                $qty = $data[3];
                $comments = $data[4];
                $qtyDelivered = $data[5];

                // Update the qty and qty_delivered in the order_items table for the matching order_no
                Order_item::where('master_id', $master_id)->update(
                    [
                        'qty' => $qty,
                        'remark_de' => $comments,
                        'qty_delivered' => $qtyDelivered,
                    ]);
            }
            fclose($handle);
        }
    }

    public function MisIds()
    {
        $itemIDs = public_path('MISIDs.csv');

        // Create an associative array to store the CSV data
        $csvData = [];

        if (($handle = fopen($itemIDs, 'r')) !== false) {
            // Skip the first row which is assumed to be the header
            fgetcsv($handle);

            // Read each row in the CSV file
            while (($data = fgetcsv($handle)) !== false) {
                $ItemID_DE = $data[0];
                $ean = $data[1];

                // Store the CSV data in an associative array with EAN as key
                $csvData[$ean] = $ItemID_DE;
            }

            fclose($handle);
        }

        // Fetch all items from the Item model
        $items = Item::all(['ItemID_DE', 'EAN']);

        foreach ($items as $item) {
            $currentItemID_DE = $item->ItemID_DE;
            $ean = $item->EAN;

            // Check if the EAN exists in the CSV data and if the ItemID_DE needs to be updated
            if (isset($csvData[$ean]) && (string) $csvData[$ean] !== (string) $currentItemID_DE) {
                // Perform the direct update in the database
                Item::where('EAN', $ean)->update(['ItemID_DE' => $csvData[$ean]]);
                Warehouse::where('EAN', $ean)->update(['ItemID_DE' => $csvData[$ean]]);
                Order_item::where('ItemID_DE', $currentItemID_DE)->update(['ItemID_DE' => $csvData[$ean]]);
            }
        }

        return redirect('/orders')->with('success', 'ItemID_DEs are synched successfully !');
    }

    public function synchIDs()
    {
        $itemIDs = public_path('ItemIDs.csv');

        // Create an associative array to store the CSV data
        $csvData = [];

        if (($handle = fopen($itemIDs, 'r')) !== false) {
            // Skip the first row which is assumed to be the header
            fgetcsv($handle);

            // Read each row in the CSV file
            while (($data = fgetcsv($handle)) !== false) {
                $ItemID_DE = $data[0];
                $ean = $data[1];

                // Store the CSV data in an associative array with EAN as key
                $csvData[$ean] = $ItemID_DE;
            }

            fclose($handle);
        }

        // Fetch all items from the Item model
        $items = Item::all(['ItemID_DE', 'EAN']);

        foreach ($items as $item) {
            $currentItemID_DE = $item->ItemID_DE;
            $ean = $item->EAN;

            // Check if the EAN exists in the CSV data and if the ItemID_DE needs to be updated
            if (isset($csvData[$ean]) && (string) $csvData[$ean] !== (string) $currentItemID_DE) {
                // Perform the direct update in the database
                Item::where('EAN', $ean)->update(['ItemID_DE' => $csvData[$ean]]);
                Warehouse::where('EAN', $ean)->update(['ItemID_DE' => $csvData[$ean]]);
                Order_item::where('ItemID_DE', $currentItemID_DE)->update(['ItemID_DE' => $csvData[$ean]]);
            }
        }

    }

    public function whareHouseSync()
    {
        $items = public_path('wareHouseItems.csv');

        if (($handle = fopen($items, 'r')) !== false) {
            // Skip the first row which is assumed to be the header
            // fgetcsv($handle);

            // Read each row in the CSV file
            while (($data = fgetcsv($handle)) !== false) {

                $ItemID_DE = $data[0];
                $is_active = $data[1];
                $msq = $data[2];
                $ship_class = $data[3];
                $qty = $data[4];

                // Update the qty and qty_delivered in the order_items table for the matching order_no
                Warehouse::where('ItemID_DE', $ItemID_DE)->update(
                    [
                        'is_active' => $is_active,
                        'msq' => $msq,
                        'ship_class' => $ship_class,
                        'stock_qty' => $qty,
                    ]);
            }
            fclose($handle);
        }

        return redirect('/orders')->with('success', 'WareHouse items synched successfully !');
    }
}
