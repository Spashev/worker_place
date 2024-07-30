<?php

namespace App\Components\Orders\Repository;

use Bloomex\Common\Blca\Models\BlcaOrderItem;
use Illuminate\Database\Eloquent\Builder;

class OrderProductQuery
{
    /**
     * @param BlcaOrderItem $orderItem
     */
    public function __construct(
        protected readonly BlcaOrderItem $orderItem
    ) {
    }

    public function productByIdWithIngredients(int $orderId): Builder
    {
        return $this->orderItem->newModelQuery()
            ->with(['productIngredients'])
            ->where('order_id', $orderId);
    }

    public function productByItemId(int $orderItemId): Builder
    {
        return $this->orderItem->newModelQuery()
            ->where('order_item_id', $orderItemId);
    }

    public function productByQrWithIngredients(string $orderCode): Builder
    {
        return $this->orderItem->newModelQuery()
            ->with(['productIngredients.latestSubstitutionIngredient'])
            ->whereHas('orderQr', function ($query) use ($orderCode) {
                $query->where('token', $orderCode);
            });
    }
}