<?php

namespace App\Http\Resources\Order;

use Bloomex\Common\Blca\Models\BlcaOrderOccasion;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Support\Collection;

class OccasionListResource extends ResourceCollection
{
    /**
     * OccasionCollectionResource constructor.
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
     *
     * @return \Illuminate\Support\Collection
     */
    public function toArray($request): Collection
    {
        return $this->collection->map(function ($item, $key) {
            /** @var BlcaOrderOccasion $item */
            return [
                'id' => $item->order_occasion_id,
                'value' =>  $item->order_occasion_code,
                'title' =>   $item->order_occasion_name
            ];
        });
    }
}