<?php

namespace Tests\Unit\Components\Services\User\Create;

use App\Http\Requests\User\UserCreateRequest;

trait UserCreateTrait
{
    public function makeCredentials($data): UserCreateRequest
    {
        $request = new UserCreateRequest($data);
        $request->request->add($data);
        $request->setContainer(app());
        $request->validateResolved();

        return $request;
    }
}