<?php

namespace Tests\Unit\Components\Services\User\List;

use App\Http\Requests\Order\OrderListRequest;

trait UserUsersListTrait
{
    public function makeCredentials($data): OrderListRequest
    {
        $request = new OrderListRequest($data);
        $request
            ->setContainer(app())
            ->validateResolved();

        return $request;
    }

}