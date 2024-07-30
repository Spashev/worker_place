<?php

namespace App\Components\Orders\Repository;

use App\Helpers\TimeHelper;
use Bloomex\Common\Blca\Models\BlcaOrderHistory;
use Bloomex\Common\Core\Enums\OrderStatus;

class OrderHistoryMutator
{
    /**
     * @param BlcaOrderHistory $orderHistory
     */
    public function __construct(
        protected readonly BlcaOrderHistory $orderHistory,
        protected readonly TimeHelper       $timeHelper
    ) {
    }

    public function createHistory(
        int         $orderId,
        OrderStatus $status,
        ?string      $comment,
        string      $userName,
        bool        $customerNotified = false,
        bool        $securityNotified = false,
        bool        $warehouseNotified = false,
        bool        $recipientNotified = false,
    ): BlcaOrderHistory
    {
        return $this->orderHistory->newModelQuery()
            ->create([
                'order_id' => $orderId,
                'comments' => $comment,
                'order_status_code' => $status->value,
                'user_name' => $userName,
                'customer_notified' => $customerNotified ? 1 : 0,
                'security_notified' => $securityNotified ? 1 : 0,
                'warehouse_notified' => $warehouseNotified ? 1 : 0,
                'recipient_notified' => $recipientNotified ? 1 : 0,
                'date_added' => $this->timeHelper->getDateTimeOfToronto(),
            ]);
    }
}