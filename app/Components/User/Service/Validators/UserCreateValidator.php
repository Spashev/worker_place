<?php

namespace App\Components\User\Service\Validators;

use App\Components\Auth\Services\RoleService;
use App\Components\User\Contracts\DTO\UserDTOInterface;
use App\Components\User\Service\Validators\Traits\CheckAccessRolesTrait;
use App\Components\User\Service\Validators\Traits\CheckAccessWarehousesTrait;
use App\Exceptions\ForbiddenException;
use App\Helpers\ErrorCode;
use App\Models\User;
use Bloomex\Common\Blca\Models\BlcaUser;
use Illuminate\Validation\ValidationException;

class UserCreateValidator
{
    use CheckAccessRolesTrait;
    use CheckAccessWarehousesTrait;

    private UserDTOInterface $userDto;
    private User $creator;


    /**
     * @throws ValidationException|ForbiddenException
     */
    public function check(UserDTOInterface $userDto, User $creator): void
    {
        $this->userDto = $userDto;
        $this->creator = $creator;

        $this->checkIfExist();
        $this->checkWarehousesBelongs($creator, $this->userDto->getWarehouses());
        $this->checkAccessToRoles($creator, $this->userDto->getRoles());
    }

    private function checkIfExist(): void
    {
        if (BlcaUser::where('email', $this->userDto->getEmail())->exists()) {
            throw ValidationException::withMessages([
                'message' => [trans(ErrorCode::USER_EXISTS)],
            ]);
        }
    }
}