<?php

namespace App\Components\Product\Services;

use App\Components\Product\Contracts\SubstitutionSoftIngredientInterface;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\DatabaseManager;
use Bloomex\Common\Blca\Models\BlcaOrderItem;
use App\Components\Orders\Repository\OrderProductQuery;
use App\Components\Product\Repository\OrderProductMutator;
use App\Components\Product\Services\OrderHistoryService\OrderHistoryService;

class OrderItemSubstitutionService
{
    public function __construct(
        private readonly OrderProductMutator $orderProductMutator,
        private readonly OrderProductQuery $orderProductQuery,
        private readonly OrderHistoryService $orderHistoryService,
        private readonly Log                           $log,
        private readonly DatabaseManager               $db,
    ) {
    }

    /**
     * @throws \Throwable
     */
    public function saveItemSubstitution(int $orderItemId, SubstitutionSoftIngredientInterface $request): BlcaOrderItem
    {
        try {
            $this->db->beginTransaction();
            /** @var BlcaOrderItem $orderItem */
            $orderItem = $this->orderProductQuery->productByItemId($orderItemId)->first();
            $this->orderProductMutator->setSubstitutionType($orderItemId, $request->getType());
            $this->orderHistoryService->saveSubstitutionItemComment($orderItem, $request->getType());
            $this->db->commit();
        } catch (\Throwable $t) {
            $this->db->rollBack();
            $this->log::error($t->getMessage());
            throw new Exception('Internal error check logs');
        }

        return $orderItem->refresh();
    }
}