<?php

namespace Tests\Feature\Controllers\Product\Substitution\SubstitutionSoft;

use Bloomex\Common\Blca\Models\BlcaOrderItemIngredient;
use Bloomex\Common\Core\Enums\ProductTypes;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class SaveSoftSubstitutionTest extends TestCase
{
    use DatabaseTransactions;
    use SaveSoftSubstitutionTrait;

    /**
     * @test
     */
    public function saveSoftSubstitution_success_response201()
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
        $data = [
            'type' => 'minor',
        ];

        // WHEN
        $response = $this->withHeaders(['Accept' => 'application/json'])
            ->json('post', route('ingredients.substitutions.soft', ['ingredient' => $ingredient->order_ingredient_id]), $data);

        // THEN
        $this->assertEquals(201, $response->getStatusCode());
    }

    /**
     * @test
     */
    public function saveSoftSubstitution_success_assertJsonStructure()
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
        $data = [
            'type' => 'minor',
        ];

        // WHEN
        $response = $this->withHeaders(['Accept' => 'application/json'])
            ->json('post', route('ingredients.substitutions.soft', ['ingredient' => $ingredient->order_ingredient_id]), $data);

        // THEN
        $this->assertJsonStructure($response);
    }

    /**
     * @test
     */
    public function saveSoftSubstitution_filed_response401()
    {
        // GIVEN
        $user = $this->createUser();
        $warehouse = $this->createWarehouse();
        $this->attachUserWarehouse($user, $warehouse);

        $order = $this->createOrder();
        $this->createOrderItems($order);
        /** @var BlcaOrderItemIngredient $ingredient */
        $ingredient = $order->productsIngredients->first();
        $this->createProductIngredientOption($ingredient->ingredient_name, ProductTypes::Flower->value);
        $this->createBlcaSubstitutionItem('random name one', ProductTypes::Flower->value);
        $this->createBlcaSubstitutionColor('Red', '#ff0000');
        $data = [
            'type' => 'minor',
        ];

        // WHEN
        $response = $this->withHeaders(['Accept' => 'application/json'])
            ->json('post', route('ingredients.substitutions.soft', ['ingredient' => $ingredient->order_ingredient_id]), $data);

        // THEN
        $this->assertEquals(401, $response->getStatusCode());
    }

    /**
     * @test
     */
    public function saveSoftSubstitution_filed_response404()
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
        $data = [
            'type' => 'minor',
        ];

        // WHEN
        $response = $this->withHeaders(['Accept' => 'application/json'])
            ->json('post', route('ingredients.substitutions.soft', ['ingredient' => -123]), $data);

        // THEN
        $this->assertEquals(404, $response->getStatusCode());
    }

    /**
     * @test
     * @dataProvider getWrongData
     * @param array $credentials
     * @param array $errors
     */
    public function saveSoftSubstitution_failed_response422(array $credentials, array $errors)
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
            ->json('post', route('ingredients.substitutions.soft', ['ingredient' => $ingredient->order_ingredient_id]), $credentials);


        // THEN
        $response->assertUnprocessable();
        $response->assertJsonValidationErrors($errors);
        $this->assertJson($response->baseResponse->getContent());
        $response->assertJsonStructure([
            'message',
        ]);
    }

}