<?php

namespace App\Components\Product\Repository;

use Bloomex\Common\Blca\Models\BlcaProductIngredientOptions;
use Illuminate\Database\Eloquent\Builder;

class IngredientOptionsQuery
{
    public function __construct(
        protected readonly BlcaProductIngredientOptions   $productIngredientOptions
    ) {
    }

    public function getIngredientByName(string $ingredientName): Builder
    {
        /** @var BlcaProductIngredientOptions $productIngredientOptions */
       return $this->productIngredientOptions->newModelQuery()
            ->select('type')
            ->where('igo_product_name', $ingredientName);
    }
}