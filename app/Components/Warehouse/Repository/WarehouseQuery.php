<?php

namespace App\Components\Warehouse\Repository;

use App\Components\Warehouse\Contracts\WarehouseQueryInterface;
use Bloomex\Common\Blca\Models\BlcaWarehouse;
use Illuminate\Foundation\Application;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class WarehouseQuery implements WarehouseQueryInterface
{
    /**
     * @param Application $app
     * @param BlcaWarehouse $order
     */
    public function __construct(
        protected readonly Application $app,
        protected readonly BlcaWarehouse   $warehouse
    ) {
    }

    public function getAllQuery(): Builder
    {
        return $this->getQuery();
    }

    private function getQuery(): Builder
    {
        return $this->warehouse->newQuery();
    }

    public function orderWarehouse(string $token): BlcaWarehouse
    {
        return BlcaWarehouse::join('jos_vm_orders', 'jos_vm_warehouse.warehouse_code', '=', 'jos_vm_orders.warehouse')
            ->join('jos_vm_orders_qr', 'jos_vm_orders.order_id', '=', 'jos_vm_orders_qr.order_id')
            ->where('jos_vm_orders_qr.token', $token)
            ->select('jos_vm_warehouse.*')
            ->first();
    }
}