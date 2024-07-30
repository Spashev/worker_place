<?php

namespace App\Components\User\Service\Validators;

use App\Components\User\Contracts\DTO\UserDTOInterface;
use App\Components\User\Contracts\DTO\UserUpdateDTOInterface;
use App\Components\User\Service\Validators\Traits\CheckAccessRolesTrait;
use App\Components\User\Service\Validators\Traits\CheckAccessWarehousesTrait;
use App\Exceptions\ForbiddenException;
use App\Models\User;

class UserUpdateValidator
{
    use CheckAccessRolesTrait;
    use CheckAccessWarehousesTrait;

    private UserUpdateDTOInterface $userDto;
    private User $creator;

    /**
     * @throws ForbiddenException
     */
    public function check(UserUpdateDTOInterface $userDto, User $creator): void
    {
        $this->userDto = $userDto;
        $this->creator = $creator;

        $this->checkWarehousesBelongs($creator, $this->userDto->getWarehouses());
        $this->checkAccessToRoles($creator, $this->userDto->getRoles());
    }
}