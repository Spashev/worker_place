<?php

namespace App\Components\Orders\Contracts;

use Illuminate\Support\Collection;

interface IngredientServiceInterface
{
    public function listIngredients(IngredientListInterface $request): Collection;
    public function getOrderId(IngredientListInterface $request): int;
}