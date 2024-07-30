<?php

namespace Tests\Feature\Controllers\Warehouses\UserWarehouseList;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class UserWarehouseListTest extends TestCase
{
    use DatabaseTransactions;
    use UserWarehouseListTrait;

    /**
     * @test
     */
    public function userWarehousesList_success_response200()
    {
        // GIVEN
        $user = $this->createUserAndBe();

        $warehouseOne = $this->createWarehouse();
        $warehouseTwo = $this->createWarehouse();
        $this->attachUserWarehouse($user, $warehouseOne);
        $this->attachUserWarehouse($user, $warehouseTwo);

        // WHEN
        $response = $this->withHeaders(['Accept' => 'application/json'])
            ->json('get', route('user.warehouse.list'));

        // THEN
        $this->assertEquals(200, $response->getStatusCode());
    }

    /**
     * @test
     */
    public function userWarehousesList_success_checkStructure()
    {
        // GIVEN
        $user = $this->createUserAndBe();

        $warehouseOne = $this->createWarehouse();
        $warehouseTwo = $this->createWarehouse();
        $this->attachUserWarehouse($user, $warehouseOne);
        $this->attachUserWarehouse($user, $warehouseTwo);

        // WHEN
        $response = $this->withHeaders(['Accept' => 'application/json'])
            ->json('get', route('user.warehouse.list'));

        // THEN
        $this->assertJsonStructure($response);
    }

    /**
     * @test
     */
    public function userWarehousesList_failed_unauthenticated()
    {
        // GIVEN
        $user = $this->createUser();

        $warehouseOne = $this->createWarehouse();
        $warehouseTwo = $this->createWarehouse();
        $this->attachUserWarehouse($user, $warehouseOne);
        $this->attachUserWarehouse($user, $warehouseTwo);

        // WHEN
        $response = $this->withHeaders(['Accept' => 'application/json'])
            ->json('get', route('user.warehouse.list'));

        // THEN
        $this->assertEquals(401, $response->getStatusCode());
        $response->assertUnauthorized();
    }
}