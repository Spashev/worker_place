<?php

namespace App\Components\Warehouse\Services;

use App\Components\Warehouse\Repository\WarehouseQuery;
use App\Models\User;
use Bloomex\Common\Blca\Models\BlcaUser;
use Exception;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

class WarehouseService
{
    public function __construct(
        private readonly WarehouseQuery $warehouseQuery,
        private readonly Log            $log,
    )
    {
    }

    /**
     * @throws Exception
     */
    public function usersWarehousesList(): Collection
    {
        try {
            /** @var User $loggedUser */
            $loggedUser = auth()->user();
            if (!$loggedUser->hasRole('Super admin')) {
                $warehouses = $loggedUser->warehouses()->get();

            } else {
                $warehouses = $this->warehouseQuery->getAllQuery()->get();
            }

        } catch (\Throwable $t) {
            $this->log::error($t->getMessage());
            throw new Exception('Internal error check logs');
        }
        return $warehouses;
    }
}