<?php

namespace App\Components\User\DTO;

use App\Components\User\Contracts\DTO\UserUpdateDTOInterface;
use Illuminate\Support\Arr;

class UserUpdateDTO implements UserUpdateDTOInterface
{
    /**
     * @param array $raw_user
     */
    public function __construct(private readonly array $raw_user)
    {}

    public function getRoles(): array
    {
        return Arr::get($this->raw_user, 'roles');
    }

    public function getWarehouses(): array
    {
        return Arr::get($this->raw_user, 'warehouses');
    }
}