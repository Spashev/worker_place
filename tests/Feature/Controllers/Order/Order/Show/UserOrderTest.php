<?php

namespace Tests\Feature\Controllers\Order\Order\Show;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class UserOrderTest extends TestCase
{
    use DatabaseTransactions;
    use UserOrderTrait;

    /**
     * @test
     */
    public function show_success_response200()
    {
        // GIVEN
        $user_one = $this->createUserAndBe();
        $warehouse = $this->createWarehouse();
        $this->attachUserWarehouse($user_one, $warehouse);
        $order = $this->createOrder();

        // WHEN
        $response = $this->withHeaders(['Accept' => 'application/json'])
            ->json('get', route('user.order.show', ['order' => $order->order_id]));

        // THEN
        $this->assertEquals(200, $response->getStatusCode());
    }

    /**
     * @test
     */
    public function show_success_checkJsonStructure()
    {
        // GIVEN
        $user_one = $this->createUserAndBe();
        $warehouse = $this->createWarehouse();
        $this->attachUserWarehouse($user_one, $warehouse);
        $order = $this->createOrder();
        $this->createOrderHistory(5);

        // WHEN
        $response = $this->withHeaders(['Accept' => 'application/json'])
            ->json('get', route('user.order.show', ['order' => $order->order_id]));

        // THEN
        $this->assertJson($response->baseResponse->getContent());
        $this->assertStructureJson($response);
    }
}