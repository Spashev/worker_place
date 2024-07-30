<?php

namespace App\Http\Resources\Order;

use Bloomex\Common\Blca\Models\BlcaDriverRate;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderDriverRatesResource extends JsonResource
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
        /** @var BlcaDriverRate $this */
        return [
            'id' => $this->id_rate,
            'name' => $this->name,
            'warehouse' => isset($this->warehouse) ? $this->warehouse->warehouse_name : '',
            'rate' => $this->rate,
            'rate_driver' => $this->rate_driver,
            'comment' => $this->comment,
        ];
    }

}