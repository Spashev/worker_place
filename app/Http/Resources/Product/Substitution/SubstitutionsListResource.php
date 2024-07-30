<?php

namespace App\Http\Resources\Product\Substitution;

use Illuminate\Http\Resources\Json\JsonResource;

class SubstitutionsListResource extends JsonResource
{
    public static $wrap = null;
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request): array
    {
        return [
            'substitutionItems' => SubstitutionItemsResource::collection($this->substitutionItems),
            'substitutionColors' => SubstitutionColorsResource::collection($this->substitutionColors),
        ];
    }
}