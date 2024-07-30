<?php

namespace App\Components\User\DTO;

use App\Components\User\Contracts\DTO\UserDTOInterface;
use Illuminate\Support\Arr;

class UserDTO implements UserDTOInterface
{
    /**
     * @param array $raw_user
     */
    public function __construct(private readonly array $raw_user)
    {}

    public function getName(): string
    {
        return Arr::get($this->raw_user, 'name');
    }

    public function getEmail(): string
    {
        return Arr::get($this->raw_user, 'email');
    }

    public function getRoles(): array
    {
        return Arr::get($this->raw_user, 'roles');
    }

    public function getWarehouses(): array
    {
        return Arr::get($this->raw_user, 'warehouses');
    }
}