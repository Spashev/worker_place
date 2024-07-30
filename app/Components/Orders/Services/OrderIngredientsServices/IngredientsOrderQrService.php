<?php

namespace App\Components\Orders\Services\OrderIngredientsServices;

use App\Components\Orders\Contracts\IngredientListInterface;
use App\Components\Orders\Contracts\IngredientServiceInterface;
use App\Components\Orders\Repository\OrderProductQuery;
use App\Components\Orders\Repository\OrderQrQuery;
use Illuminate\Support\Collection;

class IngredientsOrderQrService implements IngredientServiceInterface
{
    public function __construct(
        private readonly OrderProductQuery $orderProductQuery,
        private readonly OrderQrQuery $orderQrQuery,
    ) {
    }

    public function listIngredients(IngredientListInterface $request): Collection
    {
        $orderCode = $request->getOrderCode();
        return $this->orderProductQuery
            ->productByQrWithIngredients($orderCode)
            ->get();
    }

    public function getOrderId(IngredientListInterface $request): int
    {
        $orderCode = $request->getOrderCode();
        return $this->orderQrQuery->getOrderId($orderCode);
    }
}