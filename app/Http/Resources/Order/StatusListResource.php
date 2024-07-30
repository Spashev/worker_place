<?php

namespace App\Http\Resources\Order;

use Bloomex\Common\Blca\Models\BlcaOrderStatus;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Support\Collection;

class StatusListResource extends ResourceCollection
{
    /**
     * StatusCollectionResource constructor.
     * Enable wrap for this resource
     *
     * @param $resource
     */
    public function __construct($resource)
    {
        parent::__construct($resource);
        static::wrap('data');
    }

    /**
     * Transform the resource collection into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Support\Collection
     */
    public function toArray($request): Collection
    {
        return $this->collection->map(function ($item, $key) {
            /** @var BlcaOrderStatus $item */
            return [
                'id' => $item->order_status_id,
                'value' => $item->order_status_code,
                'title' => $item->order_status_name,
            ];
        });
    }
}