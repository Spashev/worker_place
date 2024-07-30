<?php

namespace Tests\Unit\Components\Services\User\Update;

use App\Http\Requests\User\UserUpdateRequest;

trait UserUpdateTrait
{
    public function makeCredentials($data): UserUpdateRequest
    {
        $request = new UserUpdateRequest($data);
        $request->request->add($data);
        $request->setContainer(app());
        $request->validateResolved();

        return $request;
    }
}