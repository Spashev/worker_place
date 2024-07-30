<?php

namespace App\Components\Orders\Services\OrderIngredientsServices;

use App\Components\Orders\Contracts\IngredientListInterface;
use App\Components\Orders\Contracts\IngredientServiceInterface;
use App\Components\Orders\Repository\OrderProductQuery;
use Illuminate\Support\Collection;

class IngredientsOrderIdService implements IngredientServiceInterface
{
    public function __construct(
        private readonly OrderProductQuery $orderProductQuery,
    ) {
    }

    public function listIngredients(IngredientListInterface $request): Collection
    {
        $orderId = $request->getOrderId();
        return $this->orderProductQuery
            ->productByIdWithIngredients($orderId)
            ->get();
    }

    public function listProducts(int $orderId): Collection
    {
        return $this->orderProductQuery
            ->productByIdWithIngredients($orderId)
            ->get();
    }

    public function getOrderId(IngredientListInterface $request): int
    {
        return $request->getOrderId();
    }
}