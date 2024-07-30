<?php

namespace App\Components\Auth\Contracts;

interface LoginRequestInterface
{
    public function getEmail(): string;
    public function getUserPassword(): string;
}
