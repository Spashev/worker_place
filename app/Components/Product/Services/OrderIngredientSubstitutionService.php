<?php

namespace App\Components\Product\Services;

use App\Components\Orders\Repository\OrderMutator;
use Bloomex\Common\Core\Enums\OrderStatus;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\DatabaseManager;
use App\Components\Orders\Repository\OrderProductIngredientQuery;
use App\Components\Product\Services\OrderHistoryService\OrderHistoryService;
use Bloomex\Common\Blca\Models\BlcaOrderItemIngredient;
use Bloomex\Common\Blca\Models\BlcaOrderIngredientSubstitution;
use App\Components\Orders\Repository\OrderProductIngredientMutator;
use App\Components\Product\Repository\IngredientSubstitutionMutator;
use App\Components\Product\Contracts\SubstitutionHardIngredientInterface;
use App\Components\Product\Contracts\SubstitutionSoftIngredientInterface;

class OrderIngredientSubstitutionService
{
    public function __construct(
        private readonly OrderProductIngredientMutator $orderProductIngredientMutator,
        private readonly IngredientSubstitutionMutator $ingredientSubstitutionMutator,
        private readonly OrderProductIngredientQuery   $orderProductIngredientQuery,
        private readonly OrderHistoryService           $orderHistoryService,
        private readonly OrderMutator                  $orderService,
        private readonly OrderUrgentService            $orderUrgentService,
        private readonly Log                           $log,
        private readonly DatabaseManager               $db,
    ) {
    }

    /**
     * @throws \Throwable
     */
    public function saveHardIngredientSubstitution(int $orderIngredientId, SubstitutionHardIngredientInterface $request): BlcaOrderIngredientSubstitution
    {
        try {
            $user = Auth::user();
            $this->db->beginTransaction();
            /** @var BlcaOrderItemIngredient $orderIngredient */
            $orderIngredient = $this->orderProductIngredientQuery->getOrderItemIngredient($orderIngredientId)->first();
            $this->orderProductIngredientMutator->setSubstitutionType($orderIngredientId, $request->getType());
            $this->ingredientSubstitutionMutator->setOldSubstitutionNotActive($orderIngredientId);
            $ingredientSubstitution = $this->ingredientSubstitutionMutator->setSubstitution($orderIngredientId, $orderIngredient->ingredient_quantity, $request);
            $isUrgent = $this->orderUrgentService->isUrgent($request->getType());
            $this->orderService->updateStatusAndUrgentOrder($orderIngredient->order_id,OrderStatus::INVESTIGATION, $user->email, $isUrgent);
            $this->orderHistoryService->saveSubstitutionExtendedComment($orderIngredient, $request, $isUrgent);
            $this->db->commit();
        } catch (\Throwable $t) {
            $this->db->rollBack();
            $this->log::error($t->getMessage());
            throw new Exception('Internal error check logs');
        }

        return $ingredientSubstitution;
    }

    /**
     * @throws \Throwable
     */
    public function saveSoftIngredientSubstitution(int $orderIngredientId, SubstitutionSoftIngredientInterface $request): void
    {
        try {
            $user = Auth::user();
            $this->db->beginTransaction();
            /** @var BlcaOrderItemIngredient $orderIngredient */
            $orderIngredient = $this->orderProductIngredientQuery->getOrderItemIngredient($orderIngredientId)->first();
            $this->ingredientSubstitutionMutator->setOldSubstitutionNotActive($orderIngredientId);
            $this->orderProductIngredientMutator->setSubstitutionType($orderIngredientId, $request->getType());
            $this->orderService->updateStatusAndUrgentOrder($orderIngredient->order_id,OrderStatus::INVESTIGATION, $user->email,0);
            $this->orderHistoryService->saveSubstitutionSoftComment($orderIngredient, $request);
            $this->db->commit();
        } catch (\Throwable $t) {
            $this->db->rollBack();
            $this->log::error($t->getMessage());
            throw new Exception('Internal error check logs');
        }
    }
}