<?php

namespace App\Http\Resources\Product\Substitution;

use Bloomex\Common\Blca\Models\BlcaOrderItem;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderItemResource extends JsonResource
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
        ];
    }
}