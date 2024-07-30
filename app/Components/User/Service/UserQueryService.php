<?php

namespace App\Components\User\Service;

use App\Components\Orders\Contracts\PaginationInterface;
use App\Components\User\Contracts\UserCreateInterface;
use App\Components\User\Repository\UserQuery;
use App\Http\Requests\User\UserExistRequest;
use App\Models\User;
use Bloomex\Common\Blca\Models\BlcaUser;
use Exception;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Log;

class UserQueryService
{
    public function __construct(
        private readonly UserQuery $userQuery,
        private readonly Log               $log,
    ) {
    }

    /**
     * @throws Exception
     */
    public function list(PaginationInterface $request): ?LengthAwarePaginator
    {
        try {
            $authWarehouses = auth()->user()->warehouses->pluck('warehouse_id');
            $users = $this->userQuery->list($request, $authWarehouses);
        } catch (\Throwable $t) {
            $this->log::error($t->getMessage());
            throw new Exception('Internal error check logs');
        }
        return $users;
    }

    public function exist(UserExistRequest $request): ?User
    {
        $email = $request->getEmail();
        try {
            /** @var User $user */
            $user = $this->userQuery->getByEmail($email)->first();
        } catch (\Throwable $t) {
            $this->log::error($t->getMessage());
            throw new Exception('Internal error check logs');
        }

        return $user;
    }
}