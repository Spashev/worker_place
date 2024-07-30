<?php

namespace App\Components\User\Service\Validators\Traits;

use App\Exceptions\ForbiddenException;
use App\Helpers\ErrorCode;
use App\Models\User;

trait CheckAccessWarehousesTrait
{
    /**
     * @throws ForbiddenException
     */
    private function checkWarehousesBelongs(User $creator, array $warehousesIds): void
    {
        if($creator->getGuardRoleNames()->contains('Super admin')){
            return;
        }
        $creatorWarehouses = $creator->warehouses->pluck('warehouse_id');
        $diff = collect($warehousesIds)->diff($creatorWarehouses);
        if ($diff->isNotEmpty()) {
            throw new ForbiddenException(trans(ErrorCode::WRONG_WAREHOUSE));
        }
    }
}