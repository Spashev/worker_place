<?php

namespace Tests\Feature\Controllers\Order\Status;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class StatusListTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * @test
     */
    public function list_success_response200()
    {
        // GIVEN
        $user_one = $this->createUserAndBe();
        $warehouse = $this->createWarehouse();
        $this->attachUserWarehouse($user_one, $warehouse);

        $this->createBlcaOrderStatus('Z', 'IN_TRANSIT');
        $this->createBlcaOrderStatus('D', 'DELIVERED');
        $this->createBlcaOrderStatus('1', 'test','0');

        // WHEN
        $response = $this->withHeaders(['Accept' => 'application/json'])
            ->json('get', route('order.status.list'));

        // THEN
        $this->assertEquals(200, $response->getStatusCode());
    }

    /**
     * @test
     */
    public function list_userNotLogin_response401()
    {
        // GIVEN
        $user_one = $this->createUser();
        $warehouse = $this->createWarehouse();
        $this->attachUserWarehouse($user_one, $warehouse);

        $this->createBlcaOrderStatus('Z', 'IN_TRANSIT');
        $this->createBlcaOrderStatus('D', 'DELIVERED');
        $this->createBlcaOrderStatus('1', 'test','0');

        // WHEN
        $response = $this->withHeaders(['Accept' => 'application/json'])
            ->json('get', route('order.status.list'));

        // THEN
        $this->assertEquals(401, $response->getStatusCode());
    }
}
