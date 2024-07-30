<?php

namespace App\Http\Resources\User;

use Bloomex\Common\Blca\Models\BlcaColumn;
use Illuminate\Http\Resources\Json\JsonResource;

class UserColumnsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return array
     */
    public function toArray($request)
    {
        /** @var BlcaColumn $this */
        return [
            'columns' => $this->columns,
        ];
    }
}