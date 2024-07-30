<?php

namespace App\Components\Orders\Services\PackagerService;

use App\Components\Orders\Contracts\IngredientListInterface;
use App\Components\Orders\Repository\OrderQuery;
use App\Components\Orders\Services\Factories\OrderCompositionFactory;
use App\Components\Orders\Services\OrderHistoryService\OrderHistoryService;
use App\Components\Orders\Services\OrderIngredientsServices\IngredientsOrderIdService;
use App\Components\Orders\Services\OrderService\OrderMutatorService;
use Bloomex\Common\Blca\Models\BlcaOrder;
use Bloomex\Common\Blca\Models\BlcaOrderHistory;
use Bloomex\Common\Core\Enums\OrderStatus;
use Illuminate\Support\Collection;

class PackagerManager
{
    public function __construct(
        protected readonly OrderCompositionFactory   $factory,
        protected readonly OrderHistoryService       $historyService,
        protected readonly OrderMutatorService       $orderService,
        protected readonly OrderQuery                $orderQuery,
        protected readonly IngredientsOrderIdService $orderIdService,
        protected readonly OrderInPackingValidator   $validator,
        protected BlcaOrderHistory                   $history,
    ) {
    }

    /**
     * @throws \Throwable
     */
    public function doPackaging(IngredientListInterface $resource): Collection
    {
        $orderCompositionService = $this->factory->build($resource);
        $this->validator->check($resource);
        $data = $orderCompositionService->listIngredients($resource);
        $orderId = $orderCompositionService->getOrderId($resource);

        $status = $this->orderQuery->getStatus($orderId);
        if($status->order_status_code!= OrderStatus::INVESTIGATION->value) {
            $this->history = $this->historyService->saveInPackagingHistory($orderId, OrderStatus::IN_PACKAGING,  null);
            $this->orderService->updateStatus($orderId, OrderStatus::IN_PACKAGING);
        }else{
            $this->history = $this->historyService->saveInPackagingHistory($orderId, OrderStatus::INVESTIGATION,  null);
        }

        return $data;
    }

    public function getHistory(): BlcaOrderHistory
    {
        return $this->history;
    }

    /**
     * @throws \Throwable
     */
    public function confirmSet(IngredientListInterface $resource): Collection
    {
        $orderCompositionService = $this->factory->build($resource);
        $this->validator->check($resource);
        $products = $orderCompositionService->listIngredients($resource);
        $orderId = $orderCompositionService->getOrderId($resource);

        $status = $this->orderQuery->getStatus($orderId);
        if($status->order_status_code!= OrderStatus::INVESTIGATION->value) {
            $this->historyService->saveConfirmHistory($orderId, $products, OrderStatus::IN_PACKAGING);
            $this->orderService->updateStatus($orderId, OrderStatus::IN_PACKAGING);
        }else {
            $this->historyService->saveConfirmHistory($orderId, $products, OrderStatus::INVESTIGATION);
        }

        return $products;
    }

    /**
     * @throws \Throwable
     */
    public function packaged(BlcaOrder $order): BlcaOrderHistory
    {
        if($order->order_status == OrderStatus::INVESTIGATION->value) {
            return $this->historyService->saveInPackagingHistory($order->order_id, OrderStatus::INVESTIGATION, 'The worker finished packing and confirmed the entire order set');
        }
        return $this->historyService->saveInPackagingHistory($order->order_id, OrderStatus::IN_PACKAGING, 'The worker finished packing and confirmed the entire order set');
    }
}