<?php

namespace App\Http\Resources\Order\OrderPackaging;

use Bloomex\Common\Blca\Models\BlcaOrderHistory;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderInPackagingResource extends JsonResource
{
    public static $wrap = null;

    /**
     * OrderProductResource constructor.
     * Enable wrap for this resource
     *
     * @param $resource
     * @param $products
     */
    public function __construct($resource, $products)
    {
        parent::__construct($resource);
        $this->products = $products;
    }

    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return array
     */
    public function toArray($request): array
    {
        /** @var BlcaOrderHistory $this */
        return [
            'order_id' => $this->order_id,
            'history_id' => $this->order_status_history_id,
            'products' => OrderProductsResource::collection($this->products),
        ];
    }

}