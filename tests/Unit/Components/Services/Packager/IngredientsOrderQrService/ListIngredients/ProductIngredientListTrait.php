<?php

namespace Tests\Unit\Components\Services\Packager\IngredientsOrderQrService\ListIngredients;

use App\Http\Requests\Order\InPackagingRequest;
use Bloomex\Common\Blca\Models\BlcaOrder;
use Bloomex\Common\Blca\Models\BlcaOrderItem;
use Bloomex\Common\Blca\Models\BlcaWarehouse;

trait ProductIngredientListTrait
{
    public function makeCredentials($data): InPackagingRequest
    {
        $request = new InPackagingRequest($data);
        $request
            ->setContainer(app())
            ->validateResolved();

        return $request;
    }

    protected function createCustomOrderItem(BlcaOrder $order, $sku, BlcaWarehouse $warehouse = null)
    {
        if (!isset($warehouse)) {
            $warehouse = $this->warehouse;
        }
        $orderItem = BlcaOrderItem::factory()->state([
            'order_id' => $order->order_id,
            'warehouse' => $warehouse->warehouse_code,
            'order_item_sku' => $sku
        ])->create();
        /** @var BlcaOrderItem $orderItem */

        $quantityItems = $orderItem->product_quantity;
        $this->createOrderItemsIngredients($order->order_id, $orderItem->order_item_id, $quantityItems);

        return $this->orderItems;
    }
}