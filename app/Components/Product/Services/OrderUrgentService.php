<?php

namespace App\Components\Product\Services;

class OrderUrgentService
{
    public function isUrgent(string $type): int
    {
        return $type === 'major' ? 1 : 0;
    }
}