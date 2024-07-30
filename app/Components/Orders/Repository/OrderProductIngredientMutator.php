<?php

namespace App\Components\Orders\Repository;

use Bloomex\Common\Blca\Models\BlcaOrderItemIngredient;

class OrderProductIngredientMutator
{
    public function __construct(
        protected readonly BlcaOrderItemIngredient $orderItemIngredient
    ) {
    }

    public function setSubstitutionType(int $orderIngredientId, string $substitutionType): bool
    {
        return $this->orderItemIngredient->newModelQuery()
            ->where('order_ingredient_id', $orderIngredientId)
            ->update([
                'substitution_type' => $substitutionType,
            ]);
    }
}