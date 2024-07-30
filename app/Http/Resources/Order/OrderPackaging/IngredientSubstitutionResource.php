<?php

namespace App\Http\Resources\Order\OrderPackaging;

use Bloomex\Common\Blca\Models\BlcaOrderIngredientSubstitution;
use Illuminate\Http\Resources\Json\JsonResource;

class IngredientSubstitutionResource extends JsonResource
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
            'ingredient_id' => $this->ingredient_id,
            'quantity' => $this->ingredient_quantity,
            'name' => $this->ingredient_name,
            'type' => $this->type
        ];
    }

}