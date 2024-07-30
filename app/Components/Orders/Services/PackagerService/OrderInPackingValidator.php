<?php

namespace App\Components\Orders\Services\PackagerService;

use App\Components\Orders\Contracts\IngredientListInterface;
use App\Components\Orders\Repository\OrderQrQuery;
use App\Components\Orders\Repository\OrderQuery;
use App\Components\Orders\Repository\StatusQuery;
use App\Components\Warehouse\Repository\WarehouseQuery;
use App\Helpers\ErrorCode;
use Bloomex\Common\Blca\Models\BlcaUser;
use Illuminate\Validation\ValidationException;

class OrderInPackingValidator
{
    protected int|string $orderIdentifier;
    private IngredientListInterface $resource;

    public function __construct(
        protected readonly OrderQrQuery $orderQrQuery,
        protected readonly OrderQuery $orderQuery,
        protected readonly WarehouseQuery $warehouseQuery,
        protected readonly StatusQuery $statusQuery,
    ){
    }

    /**
     * @throws ValidationException
     */
    public function check(IngredientListInterface $resource): void
    {
        $this->orderIdentifier = $resource->isQrCode() ? $resource->getOrderCode() : $resource->getOrderId();
        $this->resource = $resource;
        $this->orderExist();
        $this->statusIsValid();
        $this->belongsUserWarehouse();
    }

    /**
     * @throws ValidationException
     */
    private function orderExist(): void
    {
        $query = $this->resource->isQrCode() ? $this->orderQrQuery : $this->orderQuery;
        if (!$query->orderExist($this->orderIdentifier)) {
            throw ValidationException::withMessages([
                'message' => [trans(ErrorCode::ORDER_NOT_FOUND)],
            ]);
        }
    }

    private function statusIsValid(): void
    {
        $query = $this->resource->isQrCode() ?$this->statusQuery  : $this->orderQuery;
        $status = $query->getStatus($this->orderIdentifier);

        if ($status && in_array($status->order_status_code, ['F', 'R', 'D'])) {
            throw ValidationException::withMessages([
                'message' => [trans(ErrorCode::WRONG_STATUS,  ['status' => $status->order_status_name])],
            ]);
        }
    }

    private function belongsUserWarehouse(): void
    {
        /** @var BlcaUser $user */
        $user = auth()->user();
        $userWarehouses = $user->warehouses->pluck('warehouse_id');
        $orderData = $this->resource->isQrCode() ? $this->resource->getOrderCode() : $this->resource->getOrderId();
        $query = $this->resource->isQrCode() ? $this->warehouseQuery : $this->orderQuery;
        $warehouseId = $query->orderWarehouse($orderData)->warehouse_id;

        if (!$userWarehouses->contains($warehouseId)) {
            throw ValidationException::withMessages([
                'message' => [trans(ErrorCode::ACCESS_FAILED)],
            ]);
        }
    }
}