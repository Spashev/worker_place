<?php

namespace App\Http\Resources\Product\Substitution;

use Bloomex\Common\Blca\Models\BlcaSubstitutionItem;
use Illuminate\Http\Resources\Json\JsonResource;

class SubstitutionItemsResource extends JsonResource
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
        /** @var BlcaSubstitutionItem $this */
        return [
            'id' => $this->id,
            'name' => $this->name,
            'type' => $this->type,
            'is_active' => $this->is_active,
        ];
    }
}