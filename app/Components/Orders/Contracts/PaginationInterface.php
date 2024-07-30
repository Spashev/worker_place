<?php

namespace App\Components\Orders\Contracts;

interface PaginationInterface
{
    public function getPerPage(): int;
    public function getPage(): int;
}