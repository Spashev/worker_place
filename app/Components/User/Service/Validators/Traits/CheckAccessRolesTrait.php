<?php

namespace App\Components\User\Service\Validators\Traits;

use App\Models\User;
use App\Helpers\ErrorCode;
use App\Exceptions\ForbiddenException;
use App\Components\Auth\Services\RoleService;

trait CheckAccessRolesTrait
{
    public function __construct(private readonly RoleService $roleService)
    {}

    /**
     * @throws ForbiddenException
     */
    private function checkAccessToRoles(User $creator, array $targetRoles): void
    {
        if ($creator->getGuardRoleNames()->contains('Super admin')) {
            return;
        }
        $allowedRoles = $this->roleService->getAllManagedRoles($creator->getGuardRoleNames());

        $isSuperAdmin = $creator->getGuardRoleNames()->contains('Super admin');
        $isAdmin = $creator->getGuardRoleNames()->contains('Admin');
        $isWarehouseManager = $creator->getGuardRoleNames()->contains('Warehouse manager');

        $canManageTargetRoles = collect($targetRoles)->diff($allowedRoles)->isEmpty();

        $result = ($isSuperAdmin && $canManageTargetRoles) ||
            ($isAdmin && $canManageTargetRoles) ||
            ($isWarehouseManager && $canManageTargetRoles);

        if (!$result) {
            throw new ForbiddenException(trans(ErrorCode::WRONG_ROLES));
        }
    }
}