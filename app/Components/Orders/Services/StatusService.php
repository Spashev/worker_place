<?php

namespace App\Components\Orders\Services;

use App\Components\Orders\Repository\StatusQuery;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Collection;

class StatusService
{
    public function __construct(
        private readonly StatusQuery $statusQuery,
        private readonly Log         $log,
    ) {
    }

    public function list(): Collection
    {
        try {
            $statuses = $this->statusQuery->publishList();
        } catch (\Throwable $t) {
            $this->log::error($t->getMessage());
            throw new Exception('Internal error check logs');
        }
        return $statuses;
    }
}