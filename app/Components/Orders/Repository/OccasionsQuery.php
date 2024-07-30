<?php

namespace App\Components\Orders\Repository;

use Bloomex\Common\Blca\Models\BlcaOrderOccasion;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class OccasionsQuery
{
    /**
     * @param BlcaOrderOccasion $orderOccasion
     */
    public function __construct(
        protected readonly BlcaOrderOccasion $orderOccasion
    ) {
    }

    public function publishedList(): Collection
    {
        return $this->orderOccasion->newModelQuery()
            ->where('published', '=',true)
            ->orderBy('order_occasion_name')
            ->get();
    }
}