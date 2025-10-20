<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

class ItemDetail
{
    public function getItemDetial($id)
    {
        return DB::table('items')
            ->leftJoin('attachment_item', 'items.id', '=', 'attachment_item.item_id')
            ->leftJoin('attachments','attachments.id', '=', 'attachment_item.attachment_id')
            ->join('parents', 'parents.id', '=', 'items.parent_id')
            ->join('variation_values', 'items.id', '=', 'variation_values.item_id')
            ->join('tarics', 'tarics.id', '=', 'items.taric_id')
            ->join('supplier_items', 'items.id', '=', 'supplier_items.item_id')
            ->join('suppliers', 'suppliers.id', '=', 'supplier_items.supplier_id')
            ->join('warehouse_items', 'warehouse_items.ean', '=', 'items.ean')
            ->join('categories', 'categories.id', '=', 'items.cat_id')
            ->select(
                'parents.*',
                'parents.name_de as de_name',
                'parents.name_de as en_name',
                'tarics.*', 'items.*', 'items.id as ItemID',
                'categories.*', 'categories.name as cat_name', 'variation_values.*',
                'supplier_items.*', 'suppliers.*', 'warehouse_items.*'
            )
            ->where('items.id', $id)
            ->first();

    }
}
