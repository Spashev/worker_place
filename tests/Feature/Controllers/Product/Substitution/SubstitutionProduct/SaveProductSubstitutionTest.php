<?php

namespace Tests\Feature\Controllers\Product\Substitution\SubstitutionProduct;

use Bloomex\Common\Blca\Models\BlcaOrderItem;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class SaveProductSubstitutionTest extends TestCase
{
    use DatabaseTransactions;
    use SaveProductSubstitutionTrait;

    /**
     * @test
     */
    public function saveProductSubstitution_success_response200()
    {
        // GIVEN
        $user = $this->createUserAndBe();
        $warehouse = $this->createWarehouse();
        $this->attachUserWarehouse($user, $warehouse);

        $order = $this->createOrder();
        $items = $this->createOrderItems($order);
        /** @var BlcaOrderItem $product */
        $product = $items->first();
        $data = [
            'type' => 'minor',
        ];

        // WHEN
        $response = $this->withHeaders(['Accept' => 'application/json'])
            ->json('post', route('product.substitutions', ['product' => $product->order_item_id]), $data);

        // THEN
        $this->assertEquals(200, $response->getStatusCode());
    }

    /**
     * @test
     */
    public function saveProductSubstitution_success_assertStructure()
    {
        // GIVEN
        $user = $this->createUserAndBe();
        $warehouse = $this->createWarehouse();
        $this->attachUserWarehouse($user, $warehouse);

        $order = $this->createOrder();
        $items = $this->createOrderItems($order);
        /** @var BlcaOrderItem $product */
        $product = $items->first();
        $data = [
            'type' => 'minor',
        ];

        // WHEN
        $response = $this->withHeaders(['Accept' => 'application/json'])
            ->json('post', route('product.substitutions', ['product' => $product->order_item_id]), $data);

        // THEN
        $this->assertJsonStructure($response);
    }

    /**
     * @test
     */
    public function saveProductSubstitution_failed_response404()
    {
        // GIVEN
        $user = $this->createUserAndBe();
        $warehouse = $this->createWarehouse();
        $this->attachUserWarehouse($user, $warehouse);

        $order = $this->createOrder();
        $items = $this->createOrderItems($order);
        /** @var BlcaOrderItem $product */
        $product = $items->first();
        $data = [
            'type' => 'minor',
        ];

        // WHEN
        $response = $this->withHeaders(['Accept' => 'application/json'])
            ->json('post', route('ingredients.substitutions.hard', ['ingredient' => -123]), $data);

        // THEN
        $this->assertEquals(404, $response->getStatusCode());
    }

    /**
     * @test
     */
    public function saveProductSubstitution_failed_response401()
    {
        // GIVEN
        $user = $this->createUser();
        $warehouse = $this->createWarehouse();
        $this->attachUserWarehouse($user, $warehouse);

        $order = $this->createOrder();
        $items = $this->createOrderItems($order);
        /** @var BlcaOrderItem $product */
        $product = $items->first();
        $data = [
            'type' => 'minor',
        ];

        // WHEN
        $response = $this->withHeaders(['Accept' => 'application/json'])
            ->json('post', route('product.substitutions', ['product' => $product->order_item_id]), $data);

        // THEN
        $this->assertEquals(401, $response->getStatusCode());
    }

    /**
     * @test
     * @dataProvider getWrongData
     * @param array $credentials
     * @param array $errors
     */
    public function saveProductSubstitution_failed_response422(array $credentials, array $errors)
    {
        // GIVEN
        $user = $this->createUserAndBe();
        $warehouse = $this->createWarehouse();
        $this->attachUserWarehouse($user, $warehouse);

        $order = $this->createOrder();
        $items = $this->createOrderItems($order);
        /** @var BlcaOrderItem $product */
        $product = $items->first();

        // WHEN
        $response = $this->withHeaders(['Accept' => 'application/json'])
            ->json('post', route('product.substitutions', ['product' => $product->order_item_id]), $credentials);


        // THEN
        $response->assertUnprocessable();
        $response->assertJsonValidationErrors($errors);
        $this->assertJson($response->baseResponse->getContent());
        $response->assertJsonStructure([
            'message',
        ]);
    }
}