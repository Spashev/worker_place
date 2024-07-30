<?php

namespace App\Components\Orders\Contracts;

use App\Models\User;
use Bloomex\Common\Blca\Models\BlcaOrder;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface OrderQueryInterface
{
    public function list(PaginationInterface $queryParams, User $auth): ?LengthAwarePaginator;
    public function loadOrderRelations(int $orderId): BlcaOrder;
}