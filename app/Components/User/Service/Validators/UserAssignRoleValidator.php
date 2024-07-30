<?php

namespace App\Components\User\Service\Validators;

use App\Components\User\Service\Validators\Traits\CheckAccessRolesTrait;
use App\Exceptions\ForbiddenException;
use App\Models\User;

class UserAssignRoleValidator
{
    use CheckAccessRolesTrait;

    /**
     * @throws ForbiddenException
     */
    public function check(User $creator, array $rolesIds): void
    {
        $this->checkAccessToRoles($creator, $rolesIds);
    }
}