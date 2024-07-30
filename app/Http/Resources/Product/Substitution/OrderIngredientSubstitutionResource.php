<?php

namespace App\Http\Resources\Product\Substitution;

use Bloomex\Common\Blca\Models\BlcaOrderIngredientSubstitution;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderIngredientSubstitutionResource extends JsonResource
{
    public static $wrap = null;

    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return array
     */
    public function toArray($request): array
    {
        /** @var BlcaOrderIngredientSubstitution $this */
        return [
            'id' => $this->id,
            'type' => $this->type,
            'ingredient_id' => $this->ingredient_id,
            'ingredient_name' => $this->ingredient_name,
            'ingredient_quantity' => $this->ingredient_quantity,
        ];
    }
}