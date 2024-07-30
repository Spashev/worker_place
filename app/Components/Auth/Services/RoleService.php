<?php

namespace App\Components\Auth\Services;

use App\Components\Auth\Repository\RoleQuery;
use App\Models\User;
use Illuminate\Support\Collection;

class RoleService
{
    public function __construct(protected readonly RoleQuery $roleQuery)
    {
    }

    public function getRolesNamesByIds(array $rolesIds): Collection
    {
        return $this->roleQuery->getRolesByIds($rolesIds)->pluck('name');
    }

    public function getApiRoles(): Collection
    {
        return $this->roleQuery->getApiRoles()->get();
    }

    public function getAllManagedRoles(Collection $roles): Collection
    {
        return $roles->flatMap(function ($role) {
            return config('bloomex.roles')[$role] ?? [];
        })->unique();
    }
}