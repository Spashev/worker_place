<?php

namespace App\Http\Resources\Order\Image;

use Bloomex\Common\Blca\Models\BlcaOrderHistoryImage;
use Illuminate\Http\Resources\Json\JsonResource;

class HistoryImageResource extends JsonResource
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
        /** @var BlcaOrderHistoryImage $this */
        return [
            'id' => $this->id,
            'thumb' => $this->thumb_link,
            'original' => $this->image_link,
        ];
    }
}