<?php

namespace App\Components\Product\Repository;

use Bloomex\Common\Blca\Models\BlcaSubstitutionColor;
use Illuminate\Database\Eloquent\Builder;

class SubstitutionColorsQuery
{
    public function __construct(
        protected readonly BlcaSubstitutionColor $substitutionColor
    ) {
    }

    public function getSubstitutionColors(): Builder
    {
        return $this->substitutionColor->newModelQuery()
            ->where('is_active', 1);
    }
}