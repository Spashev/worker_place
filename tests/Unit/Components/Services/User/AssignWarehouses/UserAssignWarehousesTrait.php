<?php

namespace Tests\Unit\Components\Services\User\AssignWarehouses;

use App\Http\Requests\User\UserWarehousesRequest;

trait UserAssignWarehousesTrait
{
    public function makeCredentials($data): UserWarehousesRequest
    {
        $request = new UserWarehousesRequest($data);
        $request->request->add($data);
        $request->setContainer(app());
        $request->validateResolved();

        return $request;
    }
}