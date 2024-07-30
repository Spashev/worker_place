<?php

namespace App\Components\Orders\Repository;

use Bloomex\Common\Blca\Models\BlcaOrder;
use Bloomex\Common\Core\Enums\OrderStatus;

class OrderMutator
{
    /**
     * @param BlcaOrder $order
     */
    public function __construct(
        protected readonly BlcaOrder $order
    ){
    }

    public function updateStatusOrder(int $orderId, OrderStatus $status, string $userName): bool
    {
        return $this->order->newQuery()
            ->where('order_id', $orderId)
            ->update([
                'order_status' => $status->value,
                'username' => $userName,
            ]);
    }

    public function updateStatusAndUrgentOrder(int $orderId, OrderStatus $status, string $userName, int $isUrgent): bool
    {
        return $this->order->newQuery()
            ->where('order_id', $orderId)
            ->update([
                'order_status' => $status->value,
                'username' => $userName,
                'is_urgent' => $isUrgent,
            ]);
    }
}