<?php

namespace App\Models;

use Bloomex\Common\Blca\Models\BlcaUser;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Collection;
use Spatie\Permission\Traits\HasPermissions;
use Spatie\Permission\Traits\HasRoles;

class User extends BlcaUser
{
    use HasPermissions;
    use HasRoles;
    use HasFactory;

    public function getGuardRoleNames(string $guard = 'api'): Collection
    {
        return $this->roles()->where('guard_name', $guard)->pluck('name');
    }

    public function getGuardRoles(string $guard = 'api'): Collection
    {
        return $this->roles()->where('guard_name', $guard)->get(['id', 'name']);
    }
}
