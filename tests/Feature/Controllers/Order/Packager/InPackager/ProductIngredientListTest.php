<?php

namespace Tests\Feature\Controllers\Order\Packager\InPackager;

use Bloomex\Common\Core\Enums\ProductTypes;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class ProductIngredientListTest extends TestCase
{
    use DatabaseTransactions;
    use ProductIngredientListTrait;

    /**
     * @test
     */
    public function list_success_byQrCode_response200()
    {
        // GIVEN
        $user = $this->createUserAndBe();
        $warehouse = $this->createWarehouse();
        $this->attachUserWarehouse($user, $warehouse);

        $status = $this->createBlcaOrderStatus('T', 'Test');
        $order = $this->createCustomOrder($status->order_status_code, $warehouse);
        $qr = $this->createOrderQr($order);
        $this->createOrderItems($order, 3);

        // WHEN
        $response = $this->withHeaders(['Accept' => 'application/json'])
            ->json('post', route('order.packaging'), ['order_code' => $qr->token]);

        // THEN
        $this->assertEquals(200, $response->getStatusCode());
    }

    /**
     * @test
     */
    public function list_success_byOrderId_response200()
    {
        // GIVEN
        $user = $this->createUserAndBe();
        $warehouse = $this->createWarehouse();
        $this->attachUserWarehouse($user, $warehouse);

        $status = $this->createBlcaOrderStatus('T', 'Test');
        $order = $this->createCustomOrder($status->order_status_code, $warehouse);
        $this->createOrderItems($order, 3);

        // WHEN
        $response = $this->withHeaders(['Accept' => 'application/json'])
            ->json('post', route('order.packaging'), ['order_id' => (string)$order->order_id]);

        // THEN
        $this->assertEquals(200, $response->getStatusCode());
    }

    /**
     * @test
     */
    public function list_success_byQrCode_checkStructure()
    {
        // GIVEN
        $user = $this->createUserAndBe();
        $warehouse = $this->createWarehouse();
        $this->attachUserWarehouse($user, $warehouse);

        $status = $this->createBlcaOrderStatus('T', 'Test');
        $order = $this->createCustomOrder($status->order_status_code, $warehouse);
        $qr = $this->createOrderQr($order);
        $items = $this->createOrderItems($order, 1);
        $product = $items->first();
        $ingredient = $product->productIngredients->first();
        $ingredient->substitution_type = 'major';
        $ingredient->save();
        $this->createBlcaIngredientSubstitution($ingredient->order_ingredient_id, ProductTypes::Flower->value, 'random name', $ingredient->ingredient_quantity);

        // WHEN
        $response = $this->withHeaders(['Accept' => 'application/json'])
            ->json('post', route('order.packaging'), ['order_code' => $qr->token]);

        // THEN
        $this->assertJsonStructure($response);
    }

    /**
     * @test
     */
    public function list_success_byOrderId_checkStructure()
    {
        // GIVEN
        $user = $this->createUserAndBe();
        $warehouse = $this->createWarehouse();
        $this->attachUserWarehouse($user, $warehouse);

        $status = $this->createBlcaOrderStatus('T', 'Test');
        $order = $this->createCustomOrder($status->order_status_code, $warehouse);
        $this->createOrderItems($order, 3);

        // WHEN
        $response = $this->withHeaders(['Accept' => 'application/json'])
            ->json('post', route('order.packaging'), ['order_id' => (string)$order->order_id]);

        // THEN
        $this->assertJsonStructure($response);
    }

    /**
     * @test
     */
    public function list_failed_notLogin()
    {
        // GIVEN
        $user = $this->createUser();
        $warehouse = $this->createWarehouse();
        $this->attachUserWarehouse($user, $warehouse);

        $status = $this->createBlcaOrderStatus('T', 'Test');
        $order = $this->createCustomOrder($status->order_status_code, $warehouse);
        $this->createOrderItems($order, 3);

        // WHEN
        $response = $this->withHeaders(['Accept' => 'application/json'])
            ->json('post', route('order.packaging'));

        // THEN
        $this->assertEquals(401, $response->getStatusCode());
    }

    /**
     * @test
     * @dataProvider getWrongOrderData
     * @param array $credentials
     */
    public function list_failed_response422(array $credentials)
    {
        // GIVEN
        $user = $this->createUserAndBe();
        $warehouse = $this->createWarehouse();
        $this->attachUserWarehouse($user, $warehouse);

        $status = $this->createBlcaOrderStatus('T', 'Test');
        $order = $this->createCustomOrder($status->order_status_code, $warehouse);
        $this->createOrderItems($order, 3);

        // WHEN
        $response = $this->withHeaders(['Accept' => 'application/json'])
            ->json('post', route('order.packaging'), $credentials);

        // THEN
        $response->assertUnprocessable();
        $this->assertJson($response->baseResponse->getContent());
        $response->assertJsonStructure([
            'message',
        ]);
    }

    /**
     * @test
     */
    public function list_failed_byOrderId_notBelongsWarehouse()
    {
        // GIVEN
        $user = $this->createUserAndBe();
        $warehouse = $this->createWarehouse();
        $this->attachUserWarehouse($user, $warehouse);

        $warehouse_two = $this->createWarehouse();
        $status = $this->createBlcaOrderStatus('T', 'Test');
        $order = $this->createCustomOrder($status->order_status_code, $warehouse_two);
        $this->createOrderItems($order, 3, $warehouse_two);

        // WHEN
        $response = $this->withHeaders(['Accept' => 'application/json'])
            ->json('post', route('order.packaging'), ['order_id' => $order->order_id]);

        // THEN
        $this->assertEquals(422, $response->getStatusCode());
    }

    /**
     * @test
     */
    public function list_failed_byOrderCode_notBelongsWarehouse()
    {
        // GIVEN
        // TODO иногда не проходит
        $warehouse = $this->createWarehouse();
        $user = $this->createUserAndBe();
        $this->attachUserWarehouse($user, $warehouse);

        $status = $this->createBlcaOrderStatus('T', 'Test');
        $warehouse_two = $this->createWarehouse();
        $order = $this->createCustomOrder($status->order_status_code, $warehouse_two);
        $qr = $this->createOrderQr($order);
        $this->createOrderItems($order, 3, $warehouse_two);

        // WHEN
        $response = $this->withHeaders(['Accept' => 'application/json'])
            ->json('post', route('order.packaging'), ['order_code' => $qr->token]);

        // THEN
        $this->assertEquals(422, $response->getStatusCode());
    }
}