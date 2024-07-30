<?php

namespace App\Components\Auth\Contracts;

interface PasswordCreatorInterface
{
    public function createPassword(): string;
    public function createAccessCode(int $numbers): int;
}