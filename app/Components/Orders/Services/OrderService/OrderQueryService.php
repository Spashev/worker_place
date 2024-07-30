<?php

namespace App\Components\Orders\Services\OrderService;

use App\Components\Orders\Contracts\PaginationInterface;
use App\Components\Orders\Repository\OrderQuery;
use App\Components\Orders\Validators\OrderUserValidator;
use App\Exceptions\InvalidOrderUserAccess;
use App\Models\User;
use Bloomex\Common\Blca\Models\BlcaOrder;
use Bloomex\Common\Blca\Models\BlcaUser;
use Bloomex\Common\Core\Enums\OrderStatus;
use Exception;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class OrderQueryService
{
    public function __construct(
        private readonly OrderQuery $orderQuery,
//        private readonly DatabaseManager   $db,
        private readonly Log               $log,
        private readonly OrderUserValidator $orderUserValidator
    ) {
    }

    /**
     * @throws Exception
     */
    public function list(PaginationInterface $request): ?LengthAwarePaginator
    {
        try {
            /** @var User $auth */
            $auth = auth()->user();
            $orders = $this->orderQuery->list($request, $auth);
        } catch (\Throwable $t) {
            $this->log::error($t->getMessage());
            throw new Exception('Internal error check logs');
        }
        return $orders;
    }

    /**
     * @throws Exception
     */
    public function show(BlcaOrder $order): BlcaOrder
    {
        /** @var User $user */
        $user = auth()->user();

        if (! $this->orderUserValidator->userHasOrderAccess($user, $order)) {
            throw new InvalidOrderUserAccess();
        }
        try {
            $order = $this->orderQuery->loadOrderRelations($order->order_id);
        } catch (\Throwable $t) {
            $this->log::error($t->getMessage());
            throw new Exception('Internal error check logs');
        }
        return $order;
    }


}