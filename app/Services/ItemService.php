<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

class ItemService
{
    public function baseOrderQuery()
    {
        return DB::table('items')
            ->join('supplier_items', 'supplier_items.item_id', '=', 'items.id')
            ->join('warehouse_items', 'warehouse_items.item_id', '=', 'items.id')
            ->join('suppliers', 'suppliers.id', '=', 'supplier_items.supplier_id')
            ->join('supplier_types', 'suppliers.order_type_id', '=', 'supplier_types.id')
            ->join('variation_values', 'variation_values.item_id', '=', 'items.id')
            ->join('parents', 'parents.id', '=', 'items.parent_id')
            ->join('tarics', 'tarics.id', '=', 'items.taric_id')
            ->where('supplier_items.is_default', 'Y');
    }

    public function rawOrderQuery($query)
    {
        return $query->select(
            'items.id AS itemId',
            'items.RMB_Price',
            'items.length',
            'items.width',
            'items.height',
            'items.weight',
            'items.remark',
            'items.ean',
            'items.cat_id',
            'items.photo',
            'items.parent_no_de',
            'items.is_rmb_special',
            'items.item_name',
            'items.taric_id',
            'items.item_name_cn',
            'items.remark',
            'items.isActive',
            'supplier_items.supplier_id',
            'supplier_items.price_rmb',
            'supplier_items.is_po',
            'supplier_items.url',
            'suppliers.id AS supplierId',
            'supplier_items.note_cn',
            'suppliers.name',
            'suppliers.website',
            'suppliers.order_type_id',
            'supplier_types.type_name',

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

    public function getItemsData($search = null)
    {
        $query = $this->rawOrderQuery(clone $this->baseOrderQuery());

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('items.item_name', 'like', "%$search%")
                    ->orWhere('items.item_name_cn', 'like', "%$search%")
                    ->orWhere('items.ean', 'like', "%$search%")
                    ->orWhere('items.remark', 'like', "%$search%");
                // ->orWhere('warehouse_items.parent_no_de', 'like', "%$search%");
            });
        }

        return $query->orderBy('items.id', 'desc');
    }
}
