<?php

namespace App\Http\Resources\Product\Substitution;

use Bloomex\Common\Blca\Models\BlcaSubstitutionColor;
use Illuminate\Http\Resources\Json\JsonResource;

class SubstitutionColorsResource extends JsonResource
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
        /** @var BlcaSubstitutionColor $this */
        return [
            'id' => $this->id,
            'name' => $this->name,
            'hex' => $this->hex,
            'is_active' => $this->is_active,
        ];
    }
}