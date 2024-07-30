<?php

namespace Tests\Unit\Components\Services\Product\Substitution\OrderIngredientSubstitutionService\SaveHardIngredientSubstitution;

use App\Components\Product\Services\OrderIngredientSubstitutionService;
use Bloomex\Common\Blca\Models\BlcaOrderHistory;
use Bloomex\Common\Blca\Models\BlcaOrderIngredientSubstitution;
use Bloomex\Common\Blca\Models\BlcaOrderItemIngredient;
use Bloomex\Common\Core\Enums\ProductTypes;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class SaveHardIngredientSubstitutionTest extends TestCase
{

    use DatabaseTransactions;
    use SaveHardIngredientSubstitutionTrait;

    /**
     * @test
     * @throws \Exception
     */
    public function saveHardSubstitution_success_responseIngredientSubstitution()
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
            'name' => 'some name',
            'quantity' => 1,
            'type' => 'major'
        ]);

        /** @var OrderIngredientSubstitutionService $tested_method */
        $tested_method = $this->app->make(OrderIngredientSubstitutionService::class);

        // WHEN
        $response = $tested_method->saveHardIngredientSubstitution($ingredient->order_ingredient_id, $credentials);

        // THEN
        $this->assertDatabaseCount(BlcaOrderIngredientSubstitution::class, 1);
        $this->assertInstanceOf(BlcaOrderIngredientSubstitution::class, $response);
        $this->assertDatabaseCount( BlcaOrderHistory::class, 1);
    }

    /**
     * @test
     * @throws \Exception
     */
    public function saveHardSubstitutionSecondRecord_success_responseIngredientSubstitution()
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
            'name' => 'some name',
            'quantity' => 1,
            'type' => 'major'
        ]);

        /** @var OrderIngredientSubstitutionService $tested_method */
        $tested_method = $this->app->make(OrderIngredientSubstitutionService::class);

        // WHEN
        $response = $tested_method->saveHardIngredientSubstitution($ingredient->order_ingredient_id, $credentials);

        // THEN
        $oldSubstitutionUpdated = $oldSubstitution->refresh();
        $this->assertFalse($oldSubstitutionUpdated->is_active);
        $this->assertDatabaseCount(BlcaOrderIngredientSubstitution::class, 2);
        $this->assertInstanceOf(BlcaOrderIngredientSubstitution::class, $response);
        $this->assertDatabaseCount( BlcaOrderHistory::class, 1);
    }
}