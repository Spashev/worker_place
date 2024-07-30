<?php

namespace App\Components\Orders\Repository;

use Bloomex\Common\Blca\Models\BlcaOrderQr;
use Bloomex\Common\Blca\Models\BlcaOrderStatus;

class OrderQrQuery
{
    /**
     * @param BlcaOrderQr $orderQr
     */
    public function __construct(
        protected readonly BlcaOrderQr $orderQr
    ) {
    }

    public function getOrderId(string $orderCode): int
    {
        return $this->orderQr->newModelQuery()
            ->where('token', $orderCode)
            ->value('order_id');
    }

    public function orderExist(string $orderCode): bool
    {
        return $this->orderQr->newModelQuery()
            ->where('token', $orderCode)
            ->exists();
    }

    public function orderStatus(string $orderCode): BlcaOrderStatus
    {
        return $this->orderQr->newModelQuery()
            ->where('token', $orderCode)
            ->status;
    }
}