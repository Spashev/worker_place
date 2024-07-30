<?php

namespace App\Http\Resources\Order\OrderPackaging;

use Bloomex\Common\Blca\Models\BlcaOrderItemIngredient;
use Illuminate\Http\Resources\Json\ResourceCollection;

class ProductIngredientsListResource extends ResourceCollection
{
    protected int $quantity;

    /**
     * OrderCollectionResource constructor.
     * Enable wrap for this resource
     *
     * @param $resource
     * @param $quantity
     */
    public function __construct($resource, $quantity)
    {
        parent::__construct($resource);
        $this->quantity = $quantity;
    }

    /**
     * Transform the resource collection into an array.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Support\Collection
     */
    public function toArray($request)
    {
        return $this->collection->map(function ($item, $key) {
            /** @var BlcaOrderItemIngredient $item */
            return [
                'item_id' => $item->order_item_id,
                'ingredient_id' => $item->order_ingredient_id,
                'name' => $item->ingredient_name,
                'quantity' => $item->ingredient_quantity / $this->quantity,
                'substitution_type' => $item->substitution_type,
                $this->mergeWhen(isset($item->latestSubstitutionIngredient), [
                    'substitution' => IngredientSubstitutionResource::make($item->latestSubstitutionIngredient)])
            ];
        });
    }

}