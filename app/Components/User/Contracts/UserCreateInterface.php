<?php

namespace App\Components\User\Contracts;

use App\Components\User\Contracts\DTO\UserDTOInterface;

interface UserCreateInterface
{
    public function getUserDTO(): UserDTOInterface;
}