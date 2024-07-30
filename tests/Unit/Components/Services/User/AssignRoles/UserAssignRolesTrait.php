<?php

namespace Tests\Unit\Components\Services\User\AssignRoles;

use App\Http\Requests\User\UserRolesRequest;

trait UserAssignRolesTrait
{
    public function makeCredentials($data): UserRolesRequest
    {
        $request = new UserRolesRequest($data);
        $request->request->add($data);
        $request->setContainer(app());
        $request->validateResolved();

        return $request;
    }
}