<?php

namespace App\Components\Product\Repository;

use Bloomex\Common\Blca\Models\BlcaSubstitutionItem;
use Illuminate\Database\Eloquent\Builder;

class SubstitutionItemsQuery
{
    public function __construct(
        protected readonly BlcaSubstitutionItem $substitutionItem
    ) {
    }

    public function getSubstitutionItemsByType(string $type): Builder
    {
        return $this->substitutionItem->newModelQuery()
            ->where('type', $type)
            ->where('is_active', 1)
            ->orderBy('sort');
    }

    public function getSubstitutionItems(): Builder
    {
        return $this->substitutionItem->newModelQuery()
            ->where('is_active', 1)
            ->orderBy('sort');
    }
}