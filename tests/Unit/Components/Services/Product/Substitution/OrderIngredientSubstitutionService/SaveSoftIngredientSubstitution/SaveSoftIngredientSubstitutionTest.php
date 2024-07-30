<?php

namespace Tests\Unit\Components\Services\Product\Substitution\OrderIngredientSubstitutionService\SaveSoftIngredientSubstitution;

use App\Components\Product\Services\OrderIngredientSubstitutionService;
use Bloomex\Common\Blca\Models\BlcaOrderHistory;
use Bloomex\Common\Blca\Models\BlcaOrderIngredientSubstitution;
use Bloomex\Common\Blca\Models\BlcaOrderItemIngredient;
use Bloomex\Common\Core\Enums\ProductTypes;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class SaveSoftIngredientSubstitutionTest extends TestCase
{
    use DatabaseTransactions;
    use SaveSoftIngredientSubstitutionTrait;

    /**
     * @test
     * @throws \Exception
     */
    public function saveSoftSubstitution_major_success()
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

        $credentials = $this->makeCredentials([
            'type' => 'major'
        ]);

        /** @var OrderIngredientSubstitutionService $tested_method */
        $tested_method = $this->app->make(OrderIngredientSubstitutionService::class);

        // WHEN
        $tested_method->saveSoftIngredientSubstitution($ingredient->order_ingredient_id, $credentials);

        // THEN
        $ingredientUpdated = $ingredient->refresh();
        $this->assertEquals( 'major', $ingredientUpdated->substitution_type);
        $this->assertDatabaseCount( BlcaOrderHistory::class, 1);
    }

    /**
     * @test
     * @throws \Exception
     */
    public function saveSoftSubstitution_minor_success()
    {
        // GIVEN
        $user = $this->createUserAndBe();
        $warehouse = $this->createWarehouse();
        $this->attachUserWarehouse($user, $warehouse);

        $order = $this->createOrder();
        $this->createOrderItems($order);
        /** @var BlcaOrderItemIngredient $ingredient */
        $ingredient = $order->productsIngredients->first();
        $oldSubstitution = $this->createBlcaIngredientSubstitution($ingredient->order_ingredient_id);
        $this->createProductIngredientOption($ingredient->ingredient_name, ProductTypes::Flower->value);
        $this->createBlcaSubstitutionItem('random name one', ProductTypes::Flower->value);
        $this->createBlcaSubstitutionColor('Red', '#ff0000');

        $credentials = $this->makeCredentials([
            'type' => 'minor'
        ]);

        /** @var OrderIngredientSubstitutionService $tested_method */
        $tested_method = $this->app->make(OrderIngredientSubstitutionService::class);

        // WHEN
        $tested_method->saveSoftIngredientSubstitution($ingredient->order_ingredient_id, $credentials);

        // THEN
        $ingredientUpdated = $ingredient->refresh();
        $oldSubstitutionUpdated = $oldSubstitution->refresh();
        $this->assertEquals( 'minor', $ingredientUpdated->substitution_type);
        $this->assertFalse($oldSubstitutionUpdated->is_active);
        $this->assertDatabaseCount( BlcaOrderHistory::class, 1);
        $this->assertDatabaseCount( BlcaOrderIngredientSubstitution::class, 1);
    }
}