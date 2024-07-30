<?php

namespace App\Components\Orders\Services\OrderService;

use App\Components\Orders\Repository\OrderMutator;
use Bloomex\Common\Blca\Models\BlcaOrder;
use Bloomex\Common\Core\Enums\OrderStatus;
use Exception;
use Illuminate\Database\DatabaseManager;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class OrderMutatorService
{
    public function __construct(
        private readonly OrderMutator $orderMutator,
        private readonly DatabaseManager   $db,
        private readonly Log               $log,
    ) {
    }

    public function updateStatus(int $orderId, OrderStatus $status): bool
    {
        $user = Auth::user();
        try {
            $this->db->beginTransaction();
            $isUpdated = $this->orderMutator->updateStatusOrder(
                orderId: $orderId,
                status : $status,
                userName: $user->email,
            );
            $this->db->commit();
        } catch (\Throwable $t) {
            $this->db->rollBack();
            $this->log::error($t->getMessage());
            throw new Exception('Internal error check logs');
        }
        return $isUpdated;
    }
}