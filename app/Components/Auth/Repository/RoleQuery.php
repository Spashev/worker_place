<?php

namespace App\Components\Auth\Repository;

use Spatie\Permission\Models\Role;
use Illuminate\Database\Eloquent\Builder;

class RoleQuery
{
    /**
     * @param Role $role
     */
    public function __construct(
        protected readonly Role $role
    ) {
    }
    public function getRolesByIds(array $rolesIds): Builder
    {
        return $this->role->newModelQuery()->whereIn('id', $rolesIds);
    }

    public function getApiRoles(): Builder
    {
        return $this->role->newModelQuery()->where('guard_name', 'api');
    }
}