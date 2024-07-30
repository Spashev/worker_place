<?php

namespace Tests\Feature\Controllers\Order\Order\List;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class UserOrdersListTest extends TestCase
{
    use DatabaseTransactions;
    use UserOrdersListTrait;

    /**
     * @test
     */
    public function list_success_response200()
    {
        // GIVEN
        $user_one = $this->createUserAndBe();
        $warehouse = $this->createWarehouse();
        $this->attachUserWarehouse($user_one, $warehouse);

        $this->createOrder();

        // WHEN
        $response = $this->withHeaders(['Accept' => 'application/json'])
            ->json('get', route('user.order.list'));

        // THEN
        $this->assertEquals(200, $response->getStatusCode());
    }

    /**
     * @test
     */
    public function list_success_checkStructure()
    {
        // GIVEN
        $user_one = $this->createUserAndBe();
        $warehouse = $this->createWarehouse();
        $this->attachUserWarehouse($user_one, $warehouse);

        $this->createOrder();
        $this->createOrder();

        // WHEN
        $response = $this->withHeaders(['Accept' => 'application/json'])
            ->json('get', route('user.order.list'));

        // THEN
        $this->assertJsonStructure($response);
    }

    /**
     * @test
     * @dataProvider getOrderIdsString
     * @param array $credentials
     */
    public function list_failed_response400(array $credentials)
    {
        // GIVEN
        $user_one = $this->createUserAndBe();
        $warehouse = $this->createWarehouse();
        $this->attachUserWarehouse($user_one, $warehouse);

        $this->createOrder();

        // WHEN
        $response = $this->withHeaders(['Accept' => 'application/json'])
            ->json('get', route('user.order.list'), $credentials);

        // THEN
        $response->assertUnprocessable();
        $this->assertJson($response->baseResponse->getContent());
        $response->assertJsonStructure([
            'message',
        ]);
    }
}
