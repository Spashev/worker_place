<?php

namespace App\Http\Resources\Auth;

use Bloomex\Common\Blca\Models\BlcaSetting;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Support\Collection;

class SettingsListResource extends ResourceCollection
{
    public static $wrap = null;
    /**
     * UserWarehouseCollectionResource constructor.
     * Enable wrap for this resource
     *
     * @param $resource
     */
    public function __construct($resource)
    {
        parent::__construct($resource);
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
            /** @var BlcaSetting $item */
            return [
                'id' => $item->id,
                'key' => $item->key,
                'value' => $item->value,
                'type' => $item->type,
                'author' => $item->updatedBy->email,
                'updated_at' => $item->updated_at,
            ];
        });
    }
}