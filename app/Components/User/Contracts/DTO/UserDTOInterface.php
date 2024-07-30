<?php

namespace App\Components\User\Contracts\DTO;

interface UserDTOInterface
{
    public function getName(): string;
    public function getEmail(): string;
    public function getRoles(): array;
    public function getWarehouses(): array;
}