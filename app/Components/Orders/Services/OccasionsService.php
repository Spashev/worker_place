<?php

namespace App\Components\Orders\Services;

use App\Components\Orders\Repository\OccasionsQuery;
use Exception;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

class OccasionsService
{
    public function __construct(
        private readonly OccasionsQuery $occasionsQuery,
        private readonly Log            $log,
    ) {
    }

    public function list(): Collection
    {
        try {
            $occasions = $this->occasionsQuery->publishedList();
        } catch (\Throwable $t) {
            $this->log::error($t->getMessage());
            throw new Exception('Internal error check logs');
        }
        return $occasions;
    }
}