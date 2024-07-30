<?php

namespace Tests\Unit\Components\Services\Product\Substitution\IngredientsSubstitutionsList;

use App\Components\Product\Services\ProductIngredientService;
use Bloomex\Common\Blca\Models\BlcaOrder;
use Bloomex\Common\Blca\Models\BlcaOrderItemIngredient;
use Bloomex\Common\Core\Enums\ProductTypes;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Collection;
use Tests\TestCase;

class IngredientsSubstitutionsList extends TestCase
{
    use DatabaseTransactions;

    /**
     * @test
     * @throws \Exception
     */
    public function listSubstitutions_responseFlowersType_dbHasTwo()
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

        /** @var ProductIngredientService $tested_method */
        $tested_method = $this->app->make(ProductIngredientService::class);

        // WHEN
        $response = $tested_method->list($ingredient->ingredient_name);

        // THEN
        $this->assertDatabaseCount(BlcaOrder::class, 1);
        $this->assertInstanceOf(Collection::class, $response->substitutionItems);
        $this->assertCount(1, $response->substitutionItems);
        $this->assertInstanceOf(Collection::class, $response->substitutionColors);
        $this->assertCount(1, $response->substitutionColors);
    }

    /**
     * @test
     * @throws \Exception
     */
    public function listSubstitutions_responseFlowersType_dbHasMore()
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
        $this->createBlcaSubstitutionItem('random name two', ProductTypes::Gourmet->value);
        $this->createBlcaSubstitutionItem('random name three', ProductTypes::HardGood->value);
        $this->createBlcaSubstitutionColor('Red', '#ff0000');

        /** @var ProductIngredientService $tested_method */
        $tested_method = $this->app->make(ProductIngredientService::class);

        // WHEN
        $response = $tested_method->list($ingredient->ingredient_name);

        // THEN
        $this->assertDatabaseCount(BlcaOrder::class, 1);
        $this->assertInstanceOf(Collection::class, $response->substitutionItems);
        $this->assertCount(1, $response->substitutionItems);
        $this->assertInstanceOf(Collection::class, $response->substitutionColors);
        $this->assertCount(1, $response->substitutionColors);
    }

    /**
     * @test
     * @throws \Exception
     */
    public function listSubstitutions_responseEmpty_dbHasTwo()
    {
        // GIVEN
        $user = $this->createUserAndBe();
        $warehouse = $this->createWarehouse();
        $this->attachUserWarehouse($user, $warehouse);

        $order = $this->createOrder();
        $this->createOrderItems($order);
        /** @var BlcaOrderItemIngredient $ingredient */
        $ingredient = $order->productsIngredients->first();
        $this->createProductIngredientOption($ingredient->ingredient_name, ProductTypes::Gourmet->value);
        $this->createBlcaSubstitutionItem('random name one', ProductTypes::Flower->value);
        $this->createBlcaSubstitutionItem('random name two', ProductTypes::HardGood->value);
        $this->createBlcaSubstitutionColor('Red', '#ff0000');

        /** @var ProductIngredientService $tested_method */
        $tested_method = $this->app->make(ProductIngredientService::class);

        // WHEN
        $response = $tested_method->list($ingredient->ingredient_name);

        // THEN
        $this->assertDatabaseCount(BlcaOrder::class, 1);
        $this->assertCount(0, $response->substitutionItems);
        $this->assertInstanceOf(Collection::class, $response->substitutionColors);
        $this->assertCount(0, $response->substitutionColors);
    }

    /**
     * @test
     * @throws \Exception
     */
    public function listSubstitutions_responseAll_dbHasFour()
    {
        // GIVEN
        $user = $this->createUserAndBe();
        $warehouse = $this->createWarehouse();
        $this->attachUserWarehouse($user, $warehouse);

        $order = $this->createOrder();
        $this->createOrderItems($order);
        /** @var BlcaOrderItemIngredient $ingredient */
        $ingredient = $order->productsIngredients->first();
        $this->createProductIngredientOption('some name', ProductTypes::Gourmet->value);

        $this->createBlcaSubstitutionItem('random name one', ProductTypes::Flower->value);
        $this->createBlcaSubstitutionItem('random name two', ProductTypes::Flower->value);
        $this->createBlcaSubstitutionItem('random name three', ProductTypes::HardGood->value);
        $this->createBlcaSubstitutionColor('Red', '#ff0000');
        $this->createBlcaSubstitutionColor('Gray', '#ffffff');

        /** @var ProductIngredientService $tested_method */
        $tested_method = $this->app->make(ProductIngredientService::class);

        // WHEN
        $response = $tested_method->list($ingredient->ingredient_name);

        // THEN
        $this->assertDatabaseCount(BlcaOrder::class, 1);
        $this->assertInstanceOf(Collection::class, $response->substitutionItems);
        $this->assertCount(3, $response->substitutionItems);
        $this->assertInstanceOf(Collection::class, $response->substitutionColors);
        $this->assertCount(2, $response->substitutionColors);
    }

    /**
     * @test
     * @throws \Exception
     */
    public function listSubstitutions_responseOnlyHardGood_dbHasFour()
    {
        // GIVEN
        $user = $this->createUserAndBe();
        $warehouse = $this->createWarehouse();
        $this->attachUserWarehouse($user, $warehouse);

        $order = $this->createOrder();
        $this->createOrderItems($order);
        /** @var BlcaOrderItemIngredient $ingredient */
        $ingredient = $order->productsIngredients->first();
        $this->createProductIngredientOption($ingredient->ingredient_name, ProductTypes::HardGood->value);

        $this->createBlcaSubstitutionItem('random name one', ProductTypes::HardGood->value);
        $this->createBlcaSubstitutionItem('random name two', ProductTypes::HardGood->value);
        $this->createBlcaSubstitutionColor('Red', '#ff0000');
        $this->createBlcaSubstitutionColor('Gray', '#ffffff');

        /** @var ProductIngredientService $tested_method */
        $tested_method = $this->app->make(ProductIngredientService::class);

        // WHEN
        $response = $tested_method->list($ingredient->ingredient_name);

        // THEN
        $this->assertDatabaseCount(BlcaOrder::class, 1);
        $this->assertInstanceOf(Collection::class, $response->substitutionItems);
        $this->assertCount(2, $response->substitutionItems);
        $this->assertInstanceOf(Collection::class, $response->substitutionColors);
        $this->assertCount(0, $response->substitutionColors);
    }

    /**
     * @test
     * @throws \Exception
     */
    public function listSubstitutions_responseOnlyGourmet_dbHasFour()
    {
        // GIVEN
        $user = $this->createUserAndBe();
        $warehouse = $this->createWarehouse();
        $this->attachUserWarehouse($user, $warehouse);

        $order = $this->createOrder();
        $this->createOrderItems($order);
        /** @var BlcaOrderItemIngredient $ingredient */
        $ingredient = $order->productsIngredients->first();
        $this->createProductIngredientOption($ingredient->ingredient_name, ProductTypes::Gourmet->value);

        $this->createBlcaSubstitutionItem('random name one', ProductTypes::Gourmet->value);
        $this->createBlcaSubstitutionItem('random name two', ProductTypes::Gourmet->value);
        $this->createBlcaSubstitutionColor('Red', '#ff0000');
        $this->createBlcaSubstitutionColor('Gray', '#ffffff');

        /** @var ProductIngredientService $tested_method */
        $tested_method = $this->app->make(ProductIngredientService::class);

        // WHEN
        $response = $tested_method->list($ingredient->ingredient_name);

        // THEN
        $this->assertDatabaseCount(BlcaOrder::class, 1);
        $this->assertInstanceOf(Collection::class, $response->substitutionItems);
        $this->assertCount(2, $response->substitutionItems);
        $this->assertInstanceOf(Collection::class, $response->substitutionColors);
        $this->assertCount(0, $response->substitutionColors);
    }
}