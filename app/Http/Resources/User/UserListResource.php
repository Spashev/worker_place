<?php

namespace App\Http\Resources\User;

use Bloomex\Common\Blca\Models\BlcaColumn;
use Bloomex\Common\Blca\Models\BlcaOrder;
use Bloomex\Common\Blca\Models\BlcaUser;
use Bloomex\Common\Core\Enums\TimeZones;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Support\Collection;

class UserListResource extends ResourceCollection
{
    /**
     * UserCollectionResource constructor.
     * Enable wrap for this resource
     *
     * @param $resource
     */
    public function __construct($resource, ?BlcaColumn $column)
    {
        parent::__construct($resource);
        static::wrap('data');

        $this->column = $column;
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
        return$this->collection->map(function ($item) {
            /** @var BlcaUser $item */
            return [
                'id' => $item->id,
                'name' => $item->name,
                'email' => $item->email,
                'is_blocked' => (bool)$item->block,
                'created_at' => isset($item->registerDate) ? $item->registerDate->toDateTimeString() : null,
                'last_visit_at' => isset($item->lastvisitDate) ? $item->lastvisitDate->toDateTimeString() : null,
            ];
        });
    }
    /**
     * Get additional data that should be returned with the resource array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function with($request)
    {
        return [
            'meta' => [
                'columns' => $this->column?->columns
            ],
        ];
    }
}