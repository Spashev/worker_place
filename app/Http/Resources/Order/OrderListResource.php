<?php

namespace App\Http\Resources\Order;

use Bloomex\Common\Blca\Models\BlcaColumn;
use Bloomex\Common\Blca\Models\BlcaOrder;
use Bloomex\Common\Core\Enums\TimeZones;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Support\Collection;

class OrderListResource extends ResourceCollection
{
    /**
     * OrderCollectionResource constructor.
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
            /** @var BlcaOrder $item */
            return [
                'id' => $item->order_id,
                'occasion' => isset($item->occasion) ? $item->occasion->order_occasion_name : null,
                'status' =>  trans('statuses.' . $item->order_status),
                'warehouse' =>  $item->orderWarehouse->warehouse_name ?? null,
                'total' =>  number_format($item->order_total, 2),
                'created_at' => $item->cdate->timezone(TimeZones::Toronto->value)->toDateTimeString(),
                'updated_at' => $item->mdate->timezone(TimeZones::Toronto->value)->toDateTimeString(),
                'delivered_at' => $item->ddate->format('d-m-Y'),
                'zone' => isset($item->rate) ? OrderDriverRatesResource::make($item->rate) : null
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