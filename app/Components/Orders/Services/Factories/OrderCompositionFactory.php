<?php

namespace App\Components\Orders\Services\Factories;

use App\Components\Orders\Contracts\IngredientListInterface;
use App\Components\Orders\Contracts\IngredientServiceInterface;
use App\Components\Orders\Services\OrderIngredientsServices\IngredientsOrderIdService;
use App\Components\Orders\Services\OrderIngredientsServices\IngredientsOrderQrService;
use Illuminate\Foundation\Application;

class OrderCompositionFactory
{
    public function __construct(
        private readonly Application $app
    ) {
    }

    public function build(IngredientListInterface $request): IngredientServiceInterface
    {
        if ($request->isQrCode()) {
            return $this->app->make(IngredientsOrderQrService::class);
        } else {
            return $this->app->make(IngredientsOrderIdService::class);
        }
    }
}