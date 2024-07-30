<?php

namespace App\Components\Orders\Contracts;

interface IngredientListInterface
{
    public function getOrderCode(): string;
    public function getOrderId(): int;
    public function isQrCode(): bool;
}