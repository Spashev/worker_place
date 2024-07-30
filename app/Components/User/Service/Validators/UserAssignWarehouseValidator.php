<?php

namespace App\Components\User\Service\Validators;

use App\Components\User\Service\Validators\Traits\CheckAccessWarehousesTrait;
use App\Exceptions\ForbiddenException;
use App\Models\User;

class UserAssignWarehouseValidator
{
    use CheckAccessWarehousesTrait;
    /**
     * @throws ForbiddenException
     */
    public function check(User $creator, array $warehouses): void
    {
        $this->checkWarehousesBelongs($creator, $warehouses);
    }
}