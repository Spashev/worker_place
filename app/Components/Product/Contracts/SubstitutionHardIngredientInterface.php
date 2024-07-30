<?php

namespace App\Components\Product\Contracts;

interface SubstitutionHardIngredientInterface
{
    public function getSubstitutionName(): string;
    public function getQuantity(): int;
    public function getColor(): string|null;
    public function getType(): string;
}