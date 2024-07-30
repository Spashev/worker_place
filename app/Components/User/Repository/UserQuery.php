<?php

namespace App\Components\User\Repository;

use App\Components\Orders\Contracts\PaginationInterface;
use App\Components\User\Filters\UsersFilter;
use App\Models\User;
use Bloomex\Common\Blca\Models\BlcaUser;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Application;
use Illuminate\Support\Collection;

class UserQuery
{
    /**
     * @param Application $app
     * @param BlcaUser $user
     */
    public function __construct(
        protected readonly Application $app,
        protected readonly User    $user,
    ) {
    }

    public function list(PaginationInterface $queryParams, Collection $warehouseIds)
    {
        /** @var User $auth */
        $auth = auth()->user();
        $data = $queryParams->validated();
        $filters = $this->app->make(UsersFilter::class, ['queryParams' => $data]);
        $query = $this->user->filter($filters);
        if (!$auth->hasRole('Super admin')) {
            $query = $query->myOwnUsers($warehouseIds);
        }

        $result = $query->paginate($queryParams->getPerPage());

        return $result;
    }

    public function getByEmail(string $email): Builder
    {
        return $this->user->newQuery()->where('email', $email);
    }
}
