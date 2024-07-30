<?php

namespace App\Http\Resources\Order\OrderPackaging;

use Bloomex\Common\Blca\Models\BlcaOrderItem;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderProductsResource extends JsonResource
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
        /** @var BlcaOrderItem $this */
        return [
            'order_id' => $this->order_id,
            'item_id' => $this->order_item_id,
            'quantity' => $this->product_quantity,
            'name' => $this->order_item_name,
            'sku' => $this->order_item_sku,
            'substitution_type' => $this->substitution_type,
            'image_full' => isset($this->product) ? $this->product->product_full_image : null,
            'image_thumb' => isset($this->product) ? $this->product->product_thumb_image : null,
            'ingredients' => ProductIngredientsListResource::make($this->productIngredients, $this->product_quantity),
        ];
    }
}