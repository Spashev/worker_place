<?php

namespace App\Components\User\Contracts\DTO;

interface UserUpdateDTOInterface
{
    public function getRoles(): array;
    public function getWarehouses(): array;
}