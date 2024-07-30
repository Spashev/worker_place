<?php

namespace App\Components\Orders\Repository;

use App\Components\Orders\Contracts\OrderQueryInterface;
use App\Components\Orders\Contracts\PaginationInterface;
use App\Components\Orders\Filters\OrdersFilter;
use App\Models\User;
use Bloomex\Common\Blca\Models\BlcaOrderStatus;
use Bloomex\Common\Blca\Models\BlcaWarehouse;
use Bloomex\Common\Core\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
use Bloomex\Common\Blca\Models\BlcaOrder;
use Illuminate\Foundation\Application;

class OrderQuery implements OrderQueryInterface
{
    /**
     * @param Application $app
     * @param BlcaOrder $order
     */
    public function __construct(
        protected readonly Application $app,
        protected readonly BlcaOrder   $order
    ) {
    }

    public function list(PaginationInterface $queryParams, User $auth): ?LengthAwarePaginator
    {
        $data = $queryParams->validated();
        $filters = $this->app->make(OrdersFilter::class, ['queryParams' => $data]);
        /** @var Builder $query */
        $query = $this->order->filter($filters);
        $query = $query->with(['rate.warehouse']);

//        $oneYearAgo = strtotime('-1 year');
//        $query = $query->where('cdate', '>=', $oneYearAgo);

        if (!$auth->hasRole('Super admin')) {
            $query = $query->userOrders($auth->id);
        }

        $result = $query->paginate($queryParams->getPerPage());

        return $result;
    }

    public function loadOrderRelations(int $orderId): BlcaOrder
    {
        /** @var BlcaOrder $order */
        $order = $this->order->newModelQuery()
            ->with([])
            ->where('order_id', $orderId)
            ->first();
        return $order;
    }

    public function orderExist(int $orderId): bool
    {
        return $this->order->newModelQuery()
            ->where('order_id', $orderId)
            ->exists();
    }

    public function getStatus(int $orderId): BlcaOrderStatus
    {
        return $this->order->newModelQuery()
            ->find($orderId)
            ->status;
    }

    public function orderWarehouse(int $orderId): BlcaWarehouse
    {
        return$this->order->newModelQuery()
            ->find($orderId)
            ->orderWarehouse;
    }
}