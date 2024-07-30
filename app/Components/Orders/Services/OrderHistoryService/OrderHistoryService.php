<?php

namespace App\Components\Orders\Services\OrderHistoryService;

use Exception;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\DatabaseManager;
use Bloomex\Common\Core\Enums\OrderStatus;
use Bloomex\Common\Blca\Models\BlcaOrderItem;
use Bloomex\Common\Blca\Models\BlcaOrderHistory;
use Bloomex\Common\Blca\Models\BlcaOrderItemIngredient;
use App\Components\Orders\Repository\OrderHistoryMutator;

class OrderHistoryService
{
    public function __construct(
        private readonly OrderHistoryMutator $historyMutator,
        private readonly Log                 $log,
        protected readonly DatabaseManager   $db,
    ) {
    }

    /**
     * @throws \Throwable
     */
    public function saveInPackagingHistory(int $orderId, OrderStatus $status, ?string $comment): BlcaOrderHistory
    {
        $user = Auth::user();
        try {
            $this->db->beginTransaction();
            $history = $this->historyMutator->createHistory(
                orderId: $orderId,
                status : $status,
                comment: $comment,
                userName: $user->email,
            );
            $this->db->commit();
        } catch (\Throwable $t) {
            $this->db->rollBack();
            $this->log::error($t->getMessage());
            throw new Exception('Internal error check logs');
        }
        return $history;
    }

    /**
     * @throws \Throwable
     */
    public function saveProductSetHistory(int $orderId, string $comment, OrderStatus $status): BlcaOrderHistory
    {
        $user = Auth::user();
        try {
            $this->db->beginTransaction();
            $history = $this->historyMutator->createHistory(
                orderId: $orderId,
                status : $status,
                comment: $comment,
                userName: $user->email,
            );
            $this->db->commit();
        } catch (\Throwable $t) {
            $this->db->rollBack();
            $this->log::error($t->getMessage());
            throw new Exception('Internal error check logs');
        }
        return $history;
    }

    /**
     * @throws \Throwable
     */
    public function saveConfirmHistory(int $orderId, Collection $products, OrderStatus $status): BlcaOrderHistory
    {
        $user = Auth::user();
        $comment = 'Worker confirmed this set:<br>';
        $products->map(function ($product) use (&$comment) {
            /** @var BlcaOrderItem $product*/
            $comment .= $product->order_item_name . '<br>';
            $product->productIngredients->map(function ($ingredient) use (&$comment) {
                /** @var BlcaOrderItemIngredient $ingredient */
                $comment .= ' - ' . $ingredient->ingredient_name.  '<br>';
            } );
        });
        try {
            $this->db->beginTransaction();
            $history = $this->historyMutator->createHistory(
                orderId: $orderId,
                status : $status,
                comment: $comment,
                userName: $user->email,
            );
            $this->db->commit();
        } catch (\Throwable $t) {
            $this->db->rollBack();
            $this->log::error($t->getMessage());
            throw new Exception('Internal error check logs');
        }
        return $history;
    }
}