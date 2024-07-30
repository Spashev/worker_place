<?php

namespace App\Http\Resources\Order;

use Bloomex\Common\Blca\Models\BlcaOrder;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderDetailsResource extends JsonResource
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
        /** @var BlcaOrder $this */
        return [
            'id' => $this->order_id,
            'order' => OrderResource::make($this),
            'billing' => OrderUserInfoResource::make($this->billing),
            'shipping' => OrderUserInfoResource::make($this->shipping),
            'history' => OrderHistoryListResource::make($this->history),
        ];
    }
}