<?php

namespace App\Http\Resources\Order;

use Bloomex\Common\Blca\Models\BlcaOrderHistory;
use Bloomex\Common\Core\Enums\OrderStatus;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Support\Collection;

class OrderHistoryListResource extends ResourceCollection
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
            /** @var BlcaOrderHistory $item */
            return [
                'id' => $item->order_status_history_id,
                'created_at' => $item->date_added->toDateTimeString(),
                'status' =>  $item->order_status_code ? trans('statuses.' . $item->order_status_code) : null,
                'creator' => $item->user_name,
                'comment' =>  $item->comments,
            ];
        });
    }
}