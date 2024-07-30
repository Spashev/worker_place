<?php

namespace App\Components\User\Contracts;

use App\Components\User\Contracts\DTO\UserUpdateDTOInterface;

interface UserUpdateInterface
{
    public function getUserDTO(): UserUpdateDTOInterface;
}