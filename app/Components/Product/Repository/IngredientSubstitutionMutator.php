<?php

namespace App\Components\Product\Repository;

use App\Helpers\TimeHelper;
use App\Components\Product\Contracts\SubstitutionHardIngredientInterface;
use Bloomex\Common\Blca\Models\BlcaOrderIngredientSubstitution;

class IngredientSubstitutionMutator
{
    public function __construct(
        protected readonly BlcaOrderIngredientSubstitution $orderItemIngredientSubstitution,
        protected readonly TimeHelper                      $timeHelper
    ) {
    }

    public function setSubstitution(int $orderIngredientId,int $orderIngredientQuantity, SubstitutionHardIngredientInterface $request): BlcaOrderIngredientSubstitution
    {
        return $this->orderItemIngredientSubstitution->newModelQuery()
            ->create([
                'ingredient_id' => $orderIngredientId,
                'type' => $request->getType(),
                'ingredient_name' => $request->getSubstitutionName() . ' ' . $request->getColor(),
                'ingredient_quantity' => $orderIngredientQuantity,
                'is_active' => true,
                'created_at' => $this->timeHelper->getDateTimeOfToronto(),
                'updated_at' => $this->timeHelper->getDateTimeOfToronto(),
            ]);
    }

    public function setOldSubstitutionNotActive(int $orderIngredientId): void
    {
        $this->orderItemIngredientSubstitution->newModelQuery()
            ->where('ingredient_id', $orderIngredientId)
            ->update(['is_active' => false]);
    }
}