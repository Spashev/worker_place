<?php

namespace Tests\Feature\Controllers\Product\Substitution\List;

use Bloomex\Common\Blca\Models\BlcaOrderItemIngredient;
use Bloomex\Common\Core\Enums\ProductTypes;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class IngredientsSubstitutionsListTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * @test
     */
    public function list_success_response200()
    {
        // GIVEN
        $user = $this->createUserAndBe();
        $warehouse = $this->createWarehouse();
        $this->attachUserWarehouse($user, $warehouse);

        $order = $this->createOrder();
        $this->createOrderItems($order);
        /** @var BlcaOrderItemIngredient $ingredient */
        $ingredient = $order->productsIngredients->first();
        $this->createProductIngredientOption($ingredient->ingredient_name, ProductTypes::Flower->value);
        $this->createBlcaSubstitutionItem('random name one', ProductTypes::Flower->value);
        $this->createBlcaSubstitutionColor('Red', '#ff0000');

        // WHEN
        $response = $this->withHeaders(['Accept' => 'application/json'])
            ->json('get', route('ingredients.substitutions.list', ['ingredient' => $ingredient->order_ingredient_id]));

        // THEN
        $this->assertEquals(200, $response->getStatusCode());
    }

    /**
     * @test
     */
    public function list_failed_response404()
    {
        // GIVEN
        $user = $this->createUserAndBe();
        $warehouse = $this->createWarehouse();
        $this->attachUserWarehouse($user, $warehouse);

        $order = $this->createOrder();
        $this->createOrderItems($order);
        /** @var BlcaOrderItemIngredient $ingredient */
        $order->productsIngredients->first();

        // WHEN
        $response = $this->withHeaders(['Accept' => 'application/json'])
            ->json('get', route('ingredients.substitutions.list', ['ingredient' => -123]));

        // THEN
        $this->assertEquals(404, $response->getStatusCode());
    }

    /**
     * @test
     */
    public function list_failed_response401()
    {
        // GIVEN
        $user = $this->createUser();
        $warehouse = $this->createWarehouse();
        $this->attachUserWarehouse($user, $warehouse);

        $order = $this->createOrder();
        $this->createOrderItems($order);
        /** @var BlcaOrderItemIngredient $ingredient */
        $ingredient = $order->productsIngredients->first();

        // WHEN
        $response = $this->withHeaders(['Accept' => 'application/json'])
            ->json('get', route('ingredients.substitutions.list', ['ingredient' => $ingredient->order_ingredient_id]));

        // THEN
        $this->assertEquals(401, $response->getStatusCode());
    }
}