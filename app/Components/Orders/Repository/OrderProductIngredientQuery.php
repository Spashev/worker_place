<?php

namespace App\Components\Orders\Repository;

use Illuminate\Database\Eloquent\Builder;
use Bloomex\Common\Blca\Models\BlcaOrderItemIngredient;

class OrderProductIngredientQuery
{
    public function __construct(
        protected readonly BlcaOrderItemIngredient $orderItemIngredient
    ) {
    }

    public function getOrderItemIngredient(int $orderIngredientId): Builder
    {
        return $this->orderItemIngredient->newModelQuery()
            ->where('order_ingredient_id', $orderIngredientId);
    }
}