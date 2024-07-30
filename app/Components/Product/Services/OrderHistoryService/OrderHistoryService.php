<?php

namespace App\Components\Product\Services\OrderHistoryService;

use Illuminate\Support\Facades\Auth;
use Bloomex\Common\Core\Enums\OrderStatus;
use Bloomex\Common\Blca\Models\BlcaOrderItem;
use Bloomex\Common\Blca\Models\BlcaOrderHistory;
use Bloomex\Common\Blca\Models\BlcaOrderItemIngredient;
use App\Components\Orders\Repository\OrderHistoryMutator;
use App\Components\Product\Contracts\SubstitutionHardIngredientInterface;
use App\Components\Product\Contracts\SubstitutionSoftIngredientInterface;

class OrderHistoryService
{
    public function __construct(
        private readonly OrderHistoryMutator $historyMutator,
    ) {
    }

    public function saveSubstitutionExtendedComment(BlcaOrderItemIngredient $orderIngredient, SubstitutionHardIngredientInterface $request, int $isUrgent): BlcaOrderHistory
    {
        $comment = $this->getExtendedComment($request, $orderIngredient, $isUrgent);
        $user = Auth::user();
        return $this->historyMutator->createHistory(
            orderId: $orderIngredient->order_id,
            status : OrderStatus::INVESTIGATION,
            comment: $comment,
            userName: $user->email,
        );
    }

    public function saveSubstitutionSoftComment(BlcaOrderItemIngredient $orderIngredient, SubstitutionSoftIngredientInterface $request): BlcaOrderHistory
    {
        $comment = $this->getComment($request, $orderIngredient);
        $user = Auth::user();
        return $this->historyMutator->createHistory(
            orderId: $orderIngredient->order_id,
            status : OrderStatus::INVESTIGATION,
            comment: $comment,
            userName: $user->email,
        );
    }

    public function saveSubstitutionItemComment(BlcaOrderItem $orderItem,string $type): BlcaOrderHistory
    {
        $comment = $this->getItemComment($orderItem, $type);
        $user = Auth::user();
        return $this->historyMutator->createHistory(
            orderId: $orderItem->order_id,
            status : OrderStatus::IN_PACKAGING,
            comment: $comment,
            userName: $user->email,
        );
    }

    /**
     * @param SubstitutionHardIngredientInterface $request
     * @param BlcaOrderItemIngredient $orderIngredient
     * @param int $isUrgent
     * @return string
     */
    private function getExtendedComment(SubstitutionHardIngredientInterface $request, BlcaOrderItemIngredient $orderIngredient, int $isUrgent): string
    {
        $headerMessage = $isUrgent ? 'Please confirm ' : '';
        return sprintf(
            "%s %s substitution<br>from: %s x %d<br>to:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;%s %s x %d<br>",
            $headerMessage,
            ucfirst($request->getType()),
            $orderIngredient->ingredient_name,
            $orderIngredient->ingredient_quantity,
            $request->getSubstitutionName(),
            $request->getColor(),
            $orderIngredient->ingredient_quantity
        );
    }

    /**
     * @param SubstitutionSoftIngredientInterface $request
     * @param BlcaOrderItemIngredient $orderIngredient
     * @return string
     */
    private function getComment(SubstitutionSoftIngredientInterface $request, BlcaOrderItemIngredient $orderIngredient): string
    {
        return sprintf(
            "%s substitution<br>Ingredient: %s x %d<br>",
            ucfirst($request->getType()),
            $orderIngredient->ingredient_name,
            $orderIngredient->ingredient_quantity,
        );
    }

    private function getItemComment(BlcaOrderItem $orderItem, string $type): string
    {
        return sprintf(
            "%s substitution<br>Product: %s x %d<br>SKU: %s<br>",
            ucfirst($type),
            $orderItem->order_item_name,
            $orderItem->product_quantity,
            $orderItem->order_item_sku,
        );
    }
}