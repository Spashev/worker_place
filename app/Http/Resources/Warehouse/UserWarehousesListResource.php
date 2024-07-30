<?php

namespace App\Http\Resources\Warehouse;

use Bloomex\Common\Blca\Models\BlcaWarehouse;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Support\Collection;

class UserWarehousesListResource extends ResourceCollection
{
    /**
     * UserWarehouseCollectionResource constructor.
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
            /** @var BlcaWarehouse $item */
            return [
                'id' => $item->warehouse_id,
                'value' => $item->warehouse_code,
                'title' => $item->warehouse_name,
            ];
        });
    }
}