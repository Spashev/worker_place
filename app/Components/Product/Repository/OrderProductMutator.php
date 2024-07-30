<?php

namespace App\Components\Product\Repository;

use Bloomex\Common\Blca\Models\BlcaOrderItem;

class OrderProductMutator
{
    /**
     * @param BlcaOrderItem $orderItem
     */
    public function __construct(
        protected readonly BlcaOrderItem $orderItem
    ) {
    }

    public function setSubstitutionType(int $orderItemId, string $substitutionType): bool
    {
        return $this->orderItem->newModelQuery()
            ->where('order_item_id', $orderItemId)
            ->update([
                'substitution_type' => $substitutionType,
            ]);
    }
}