<?php

namespace App\Components\User\Contracts;

interface UserColumnsInterface
{
    public function getColumns();
    public function getModel(): string;
}