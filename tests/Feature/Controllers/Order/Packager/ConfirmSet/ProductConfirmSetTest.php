<?php

namespace Tests\Feature\Controllers\Order\Packager\ConfirmSet;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class ProductConfirmSetTest extends TestCase
{
    use DatabaseTransactions;
    use ProductConfirmSetTrait;

    /**
     * @test
     */
    public function success_confirmById_response200()
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
            ->json('post', route('order.confirm.set'), ['order_id' => (string)$order->order_id]);

        // THEN
        $this->assertEquals(201, $response->getStatusCode());
    }

    /**
     * @test
     */
    public function success_confirmById_checkStructure()
    {
        // GIVEN
        $user = $this->createUserAndBe();
        $warehouse = $this->createWarehouse();
        $this->attachUserWarehouse($user, $warehouse);

        $order = $this->createOrder();
        $this->createOrderItems($order, 3);

        // WHEN
        $response = $this->withHeaders(['Accept' => 'application/json'])
            ->json('post', route('order.confirm.set'), ['order_id' => (string)$order->order_id]);

        // THEN
        $this->assertJsonStructure($response);
    }

    /**
     * @test
     */
    public function failed_confirmById_notLogin()
    {
        // GIVEN
        $user = $this->createUser();
        $warehouse = $this->createWarehouse();
        $this->attachUserWarehouse($user, $warehouse);

        $order = $this->createOrder();
        $this->createOrderItems($order, 3);

        // WHEN
        $response = $this->withHeaders(['Accept' => 'application/json'])
            ->json('post', route('order.confirm.set'), ['order_id' => (string)$order->order_id]);

        // THEN
        $this->assertEquals(401, $response->getStatusCode());
    }

    /**
     * @test
     * @dataProvider getWrongOrderData
     * @param array $credentials
     */
    public function failed_response422(array $credentials)
    {
        // GIVEN
        $user = $this->createUserAndBe();
        $warehouse = $this->createWarehouse();
        $this->attachUserWarehouse($user, $warehouse);

        $order = $this->createOrder();
        $this->createOrderItems($order, 3);

        // WHEN
        $response = $this->withHeaders(['Accept' => 'application/json'])
            ->json('post', route('order.confirm.set'), $credentials);

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
    public function failed_confirmById_notBelongsWarehouse()
    {
        // GIVEN
        $user = $this->createUserAndBe();
        $warehouse = $this->createWarehouse();
        $this->attachUserWarehouse($user, $warehouse);

        $warehouse_two = $this->createWarehouse();
        $order = $this->createOrder($warehouse_two);
        $this->createOrderItems($order, 3);

        // WHEN
        $response = $this->withHeaders(['Accept' => 'application/json'])
            ->json('post', route('order.confirm.set'), ['order_id' => $order->order_id]);

        // THEN
        $this->assertEquals(422, $response->getStatusCode());
    }
}