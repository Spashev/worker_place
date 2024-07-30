<?php

namespace App\Components\Product\Services;

use Exception;
use Illuminate\Support\Facades\Log;
use Bloomex\Common\Core\Enums\ProductTypes;
use App\Components\Product\Repository\SubstitutionColorsQuery;
use App\Components\Product\Repository\SubstitutionItemsQuery;
use Bloomex\Common\Blca\Models\BlcaProductIngredientOptions;
use App\Components\Product\Repository\IngredientOptionsQuery;

class ProductIngredientService
{
    public function __construct(
        private readonly IngredientOptionsQuery  $ingredientQuery,
        private readonly SubstitutionItemsQuery  $substitutionItemsQuery,
        private readonly SubstitutionColorsQuery $substitutionColorsQuery,
        private readonly Log                     $log,
    ) {
    }

    /**
     * @throws Exception
     */
    public function list(string $ingredientName): object
    {
        $substitutionColors = collect();
        try {
            /** @var BlcaProductIngredientOptions $ingredientOption */
            $ingredientOption = $this->ingredientQuery->getIngredientByName($ingredientName)->first();
            if (isset($ingredientOption)) {
                $substitutionItems = $this->substitutionItemsQuery->getSubstitutionItemsByType($ingredientOption->type)->get();
                if($ingredientOption->type === ProductTypes::Flower->value){
                    $substitutionColors = $this->substitutionColorsQuery->getSubstitutionColors()->get();
                }
            } else {
                $substitutionItems = $this->substitutionItemsQuery->getSubstitutionItems()->get();
                $substitutionColors = $this->substitutionColorsQuery->getSubstitutionColors()->get();
            }

            return (object) [
                'substitutionItems' => $substitutionItems,
                'substitutionColors' => $substitutionColors,
            ];

        } catch (\Throwable $t) {
            $this->log::error($t->getMessage());
            throw new Exception('Internal error check logs');
        }
    }
}