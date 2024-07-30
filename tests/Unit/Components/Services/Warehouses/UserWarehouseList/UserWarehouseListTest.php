<?php

namespace Tests\Unit\Components\Services\Warehouses\UserWarehouseList;

use App\Components\Warehouse\Services\WarehouseService;
use Bloomex\Common\Blca\Models\BlcaWarehouse;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class UserWarehouseListTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * @test
     * @throws \Exception
     */
    public function userWarehouseList_userHasTwoWarehouse()
    {
        // GIVEN
        $user = $this->createUserAndBe();

        $warehouseOne = $this->createWarehouse();
        $warehouseTwo = $this->createWarehouse();
        $this->attachUserWarehouse($user, $warehouseOne);
        $this->attachUserWarehouse($user, $warehouseTwo);

        /** @var WarehouseService $tested_method */
        $tested_method = $this->app->make(WarehouseService::class);

        // WHEN
        $response = $tested_method->usersWarehousesList();

        // THEN
        $this->assertCount(2, $response);
    }

    /**
     * @test
     * @throws \Exception
     */
    public function userWarehouseList_userHasTwoWarehouse_dbHasFour()
    {
        // GIVEN
        $user = $this->createUserAndBe();

        $warehouseOne = $this->createWarehouse();
        $warehouseTwo = $this->createWarehouse();
        $this->createWarehouse();
        $this->createWarehouse();
        $this->attachUserWarehouse($user, $warehouseOne);
        $this->attachUserWarehouse($user, $warehouseTwo);

        /** @var WarehouseService $tested_method */
        $tested_method = $this->app->make(WarehouseService::class);

        // WHEN
        $response = $tested_method->usersWarehousesList();

        // THEN
        $this->assertCount(2, $response);
        $this->assertDatabaseCount(BlcaWarehouse::class, 4);
    }
}