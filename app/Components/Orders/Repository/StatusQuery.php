<?php

namespace App\Components\Orders\Repository;

use Illuminate\Support\Collection;
use Bloomex\Common\Blca\Models\BlcaOrderStatus;

class StatusQuery
{
    /**
     * @param BlcaOrderStatus $orderStatus
     */
    public function __construct(
        protected readonly BlcaOrderStatus $orderStatus
    ) {
    }

    public function publishList(): Collection
    {
        $r =  $this->orderStatus->newModelQuery()
            ->where('publish', '=','1')
            ->orderBy('order_status_name')
            ->get();
        return $r;
    }

    public function getStatus(string $token): ?BlcaOrderStatus
    {
        return BlcaOrderStatus::join('jos_vm_orders', 'jos_vm_order_status.order_status_code', '=', 'jos_vm_orders.order_status')
            ->join('jos_vm_orders_qr', 'jos_vm_orders.order_id', '=', 'jos_vm_orders_qr.order_id')
            ->where('jos_vm_orders_qr.token', $token)
            ->select('jos_vm_order_status.*')
            ->first();
    }
}