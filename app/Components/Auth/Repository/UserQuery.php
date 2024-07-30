<?php

namespace App\Components\Auth\Repository;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;

class UserQuery
{
    /**
     * @param User $user
     */
    public function __construct(
        protected readonly User $user
    ) {
    }

    public function userByHash(string $hash): ?User
    {
        /** @var User $user */
        $user = $this->getQuery()
            ->where('access_code', '=', $hash)
            ->first();
        return $user;
    }

    private function getQuery(): Builder
    {
        return $this->user->newQuery();
    }
}