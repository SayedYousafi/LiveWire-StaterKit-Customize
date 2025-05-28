<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

class InvoiceService
{
    public function baseInvoiceQuery()
    {
        DB::statement('SET SESSION sql_mode = ""');

        return DB::table('cargos')
            ->join('customers', 'cargos.customer_id', '=', 'customers.id')
            ->join('order_statuses', 'order_statuses.cargo_id', '=', 'cargos.id')
            ->join('order_items', 'order_items.master_id', '=', 'order_statuses.master_id')
            ->where('order_statuses.status', '!=', 'Invoiced')
            ->select(
                'cargos.cargo_no',
                'cargos.id AS cargoId',
                'order_statuses.updated_at AS InvoiceDate',
                'customers.customer_company_name AS Name',
                'customers.id as customerId',
                'order_statuses.*',
                DB::raw('COUNT(order_items.master_id) AS CountItemOrder'),
                DB::raw('SUM(order_items.qty) as totalQty')
            );
    }

    public function invoiceQuery()
    {
        return DB::table('items')
            ->join('order_items', 'items.ItemID_DE', '=', 'order_items.ItemID_DE')
            ->join('orders', 'order_items.order_no', '=', 'orders.order_no')
            ->join('supplier_items', 'items.id', '=', 'supplier_items.item_id')
            ->join('suppliers', 'suppliers.id', '=', 'supplier_items.supplier_id')
            ->join('tarics', 'tarics.id', '=', 'items.taric_id')
            ->join('order_statuses', 'order_statuses.master_id', '=', 'order_items.master_id')
            ->join('cargos', 'cargos.id', '=', 'order_statuses.cargo_id')
            ->join('cargo_types','cargo_types.id','=','cargos.cargo_type_id')
            ->join('customers', 'cargos.customer_id', '=', 'customers.id')
            ->where('supplier_items.is_default', '=', 'Y')
            ->where('order_statuses.status', '!=', 'Invoiced');
    }

    public function getInvoiceSelectColumns(): array
    {
        return [
            'items.*',
            'items.taric_id AS itemTaricId',
            'order_items.id AS OIID',
            'order_items.*',
            'orders.*',
            'suppliers.*',
            'supplier_items.*',
            'order_statuses.*',
            'order_statuses.id AS setItemId',
            'order_statuses.taric_id as srqTaricID',
            'tarics.*',
            'cargos.*',
            'customers.*',
            'cargo_types.cargo_type',
            'order_statuses.id as sqrID'
        ];
    }

    public function getInvoices($id, $terms = null)
    {
        DB::statement('SET SESSION sql_mode = ""');

        $query = $this->invoiceQuery()->where('order_statuses.cargo_id', $id);

        // Base columns always selected
        $select = $this->getInvoiceSelectColumns();

        if ($terms === 'listByItem') {
            $select[] = DB::raw('COUNT(order_statuses.cargo_id) AS total_count');

            $query->select(...$select)
                ->groupBy(
                    'items.id',
                    'order_items.id',
                    'orders.id',
                    'suppliers.id',
                    'supplier_items.id',
                    'order_statuses.cargo_id'
                )
                ->orderBy('items.item_name', 'asc');
        } elseif ($terms === 'listByTarics') {
            $select[] = DB::raw('COUNT(order_statuses.cargo_id) AS total_count');
            $select[] = DB::raw('SUM(
                CASE
                    WHEN order_items.qty = order_statuses.qty_split
                    THEN order_items.qty
                    ELSE order_statuses.qty_split
                END
            ) as totalQty');
            $select[] = DB::raw("SUM(
                (CASE
                    WHEN items.is_eur_special = 'Y' THEN COALESCE(order_statuses.eur_special_price, 0)
                    WHEN items.is_rmb_special = 'Y' THEN COALESCE(EK_net(order_statuses.rmb_special_price, items.cat_id), 0)
                    ELSE COALESCE(EK_net(supplier_items.price_rmb, items.cat_id), 0)
                END) * COALESCE(LEAST(order_items.qty, order_statuses.qty_split), 0)
            ) AS totalValue");

            $query->select(...$select)
                ->orderBy('order_statuses.set_taric_code', 'ASC')
                ->orderBy('tarics.name_en', 'ASC')
                ->groupBy(
                    DB::raw("CASE WHEN items.taric_id = 48 THEN order_statuses.taric_id ELSE tarics.id END"),
                    'order_statuses.set_taric_code',
                    'tarics.name_en'
                );
        } else {
            // fallback (no term)
            $query->select(...$select);
        }

        return $query;
    }
}
