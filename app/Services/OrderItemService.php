<?php
namespace App\Services;

use Illuminate\Support\Facades\DB;

class OrderItemService
{
    public function baseOrderQuery()
    {
        return DB::table('order_items')
            ->join('order_statuses', 'order_statuses.master_id', '=', 'order_items.master_id')
            ->join('items', 'items.ItemID_DE', '=', 'order_items.ItemID_DE')
            ->join('supplier_items', 'supplier_items.item_id', '=', 'items.id')
            ->join('warehouse_items','warehouse_items.item_id','=','items.id')
            ->join('suppliers', 'suppliers.id', '=', 'supplier_items.supplier_id')
            ->join('supplier_types', 'suppliers.order_type_id', '=', 'supplier_types.id')
            ->join('orders', 'order_items.order_no', '=', 'orders.order_no')
            ->where('supplier_items.is_default', 'Y');

    }

    public function finalizeOrderQuery($query)
    {
        DB::statement('SET SESSION sql_mode = ""');

        return $query->select(
            'order_items.id AS ID',
            'suppliers.id AS SUPPID',
            'suppliers.name',
            'supplier_types.type_name',
            'orders.comment',
            'suppliers.order_type_id',
            'order_statuses.status',
            DB::raw('SUM(order_items.qty) as QTY'),
            DB::raw('COUNT(order_statuses.ItemID_DE) as countItems'),
        )->groupBy('suppliers.id');
    }

    public function rawOrderQuery($query)
    {
        return $query->select(
            'order_items.id AS ID',
            'order_items.ItemID_DE',
            'order_items.order_no',
            'order_items.qty',
            'order_items.remark_de',
            'order_items.master_id',
            'order_statuses.remarks_cn',
            'order_statuses.cargo_id',
            'order_statuses.status',
            'order_statuses.qty_split',
            'order_statuses.qty_label',
            'order_statuses.id as sqrID',
            'items.length',
            'items.width',
            'items.height',
            'items.weight',
            'items.remark',
            'items.ean',
            'items.cat_id',
            'items.photo',
            'items.id AS item_id',
            'items.is_rmb_special',
            'items.item_name',
            'items.taric_id',
            'items.item_name_cn',
            'supplier_items.supplier_id',
            'supplier_items.price_rmb',
            'supplier_items.is_po',
            'supplier_items.url',
            'suppliers.id AS SUPPID',
            'supplier_items.note_cn',
            'suppliers.name',
            
            'suppliers.website',
            'suppliers.order_type_id',
            'supplier_types.type_name',
            'orders.comment',
            'warehouse_items.item_no_de',
            'order_statuses.supplier_order_id',
            'order_statuses.rmb_special_price'
        );
    }

    public function getItemOrdersQuery($supplierId, $terms = null)
    {
        $query = $this->rawOrderQuery(clone $this->baseOrderQuery())
            ->where('supplier_items.supplier_id', $supplierId);

        if ($terms === 'Express') {
            $query->where('orders.comment', 'LIKE', '%expres%');
        }

        if ($terms === 'Normal') {
            $query->where('orders.comment', 'NOT LIKE', '%expres%');
        }

        return $query->orderBy('orders.order_no', 'DESC');
    }

    public function getItemOrders($supplierId)
    {
        $query = $this->rawOrderQuery(clone $this->baseOrderQuery())
            ->where('order_statuses.supplier_order_id', $supplierId);
        return $query->orderBy('items.item_name');
    }

    public function getItemsData()
    {
        $query = $this->rawOrderQuery(clone $this->baseOrderQuery());
        return $query->orderBy('items.id', 'desc');
    }
}
