<?php

namespace App\Components\Auth\Contracts;

interface LoginWorkerRequestInterface
{
    public function getUserPassword(): string;
}
