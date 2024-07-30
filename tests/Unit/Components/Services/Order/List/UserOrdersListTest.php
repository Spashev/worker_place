<?php

namespace Tests\Unit\Components\Services\Order\List;

use App\Components\Orders\Services\OrderService\OrderQueryService;
use Bloomex\Common\Blca\Models\BlcaDriverRate;
use Bloomex\Common\Blca\Models\BlcaDriverRatesPostalcodes;
use Bloomex\Common\Blca\Models\BlcaOrder;
use Bloomex\Common\Blca\Models\BlcaOrderUserInfo;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class UserOrdersListTest extends TestCase
{
    use DatabaseTransactions;
    use UserOrdersListTrait;

    /**
     * @test
     * @throws \Exception
     */
    public function listOrders_responseAllOrders_dbHasTwo()
    {
        // GIVEN
        $user_one = $this->createUserAndBe();
        $warehouse = $this->createWarehouse();
        $this->attachUserWarehouse($user_one, $warehouse);

        $this->createOrder();
        $this->createOrder();

        $credentials = $this->makeCredentials([]);

        /** @var OrderQueryService $tested_method */
        $tested_method = $this->app->make(OrderQueryService::class);

        // WHEN
        $response = $tested_method->list($credentials);

        // THEN
        $this->assertDatabaseCount(BlcaOrder::class, 2);
        $this->assertDatabaseCount(BlcaOrderUserInfo::class, 4);
        $this->assertInstanceOf(LengthAwarePaginator::class, $response);
        $this->assertInstanceOf(BlcaOrder::class, $response->items()[0]);
        $this->assertInstanceOf(BlcaOrderUserInfo::class, $response->items()[0]->orderUserInfo->first());
    }

    /**
     * @test
     */
    public function listOrders_responseSearchById_oneMatches()
    {
        // GIVEN
        $user_one = $this->createUserAndBe();
        $warehouse = $this->createWarehouse();
        $this->attachUserWarehouse($user_one, $warehouse);

        $order = $this->createOrder();
        $this->createOrder();

        $order_id = $order->order_id;

        $credentials = $this->makeCredentials(['id' => (string) $order_id]);

        /** @var OrderQueryService $tested_method */
        $tested_method = $this->app->make(OrderQueryService::class);

        // WHEN
        $response = $tested_method->list($credentials);

        // THEN
        $this->assertDatabaseCount(BlcaOrder::class, 2);
        $this->assertEquals($order_id, $response->items()[0]->order_id);
        $this->assertCount(1, $response);
    }

    /**
     * @test
     * @throws \Exception
     */
    public function listOrders_responseSearchById_twoMatches()
    {
        // GIVEN
        $user_one = $this->createUserAndBe();
        $warehouse = $this->createWarehouse();
        $this->attachUserWarehouse($user_one, $warehouse);

        $order_one = $this->createOrder();
        $this->createOrder();
        $order_two = $this->createOrder();

        $orderOneId = $order_one->order_id;
        $orderTwoId = $order_two->order_id;

        $credentials = $this->makeCredentials(['id' => $orderOneId . ','. $orderTwoId]);

        /** @var OrderQueryService $tested_method */
        $tested_method = $this->app->make(OrderQueryService::class);

        // WHEN
        $response = $tested_method->list($credentials);

        // THEN
        $this->assertDatabaseCount(BlcaOrder::class, 3);
        $this->assertEquals($orderTwoId, $response->items()[0]->order_id);
        $this->assertEquals($orderOneId, $response->items()[1]->order_id);
        $this->assertCount(2, $response);
    }

    /**
     * @test
     * @throws \Exception
     */
    public function listOrders_responseSearchByStatusInTransit_twoMatches()
    {
        // GIVEN
        $user_one = $this->createUserAndBe();
        $warehouse = $this->createWarehouse();
        $this->attachUserWarehouse($user_one, $warehouse);

        $this->createCustomOrder(order_status: 'Z');
        $this->createCustomOrder(order_status: 'A');
        $this->createCustomOrder(order_status: 'D');
        $this->createCustomOrder(order_status: 'Z');

        $credentials = $this->makeCredentials(['status' => 'Z']); //z = in_transit

        /** @var OrderQueryService $tested_method */
        $tested_method = $this->app->make(OrderQueryService::class);

        // WHEN
        $response = $tested_method->list($credentials);

        // THEN
        $this->assertDatabaseCount(BlcaOrder::class, 4);
        $this->assertEquals('IN_TRANSIT', $response->items()[0]->status->order_status_name);
        $this->assertEquals('IN_TRANSIT', $response->items()[1]->status->order_status_name);
        $this->assertCount(2, $response);
    }
    /**
     * @test
     * @throws \Exception
     */
    public function listOrders_responseSearchByStatusInTransitDelivered_twoMatches()
    {
        // GIVEN
        $user_one = $this->createUserAndBe();
        $warehouse = $this->createWarehouse();
        $this->attachUserWarehouse($user_one, $warehouse);

        $this->createCustomOrder(order_status: 'Z');
        $this->createCustomOrder(order_status: 'U');
        $this->createCustomOrder(order_status: 'D');
        $this->createCustomOrder(order_status: 'A');

        $credentials = $this->makeCredentials(['status' => 'Z,D']); //z = in_transit, d = delivered

        /** @var OrderQueryService $tested_method */
        $tested_method = $this->app->make(OrderQueryService::class);

        // WHEN
        $response = $tested_method->list($credentials);

        // THEN
        $this->assertDatabaseCount(BlcaOrder::class, 4);
        $this->assertEquals('DELIVERED', $response->items()[0]->status->order_status_name);
        $this->assertEquals('IN_TRANSIT', $response->items()[1]->status->order_status_name);
        $this->assertCount(2, $response);
    }

    /**
     * @test
     * @throws \Exception
     */
    public function listOrders_responseSearchByDeliveryDateRange_fourMatches()
    {
        // GIVEN
        $user_one = $this->createUserAndBe();
        $warehouse = $this->createWarehouse();
        $this->attachUserWarehouse($user_one, $warehouse);

        $this->createCustomOrder(delivery_date: '2024-02-01');
        $this->createCustomOrder(delivery_date: '2024-02-02');
        $this->createCustomOrder(delivery_date: '2024-02-03');
        $this->createCustomOrder(delivery_date: '2024-02-03');
        $this->createCustomOrder(delivery_date: '2024-02-04');
        $this->createCustomOrder(delivery_date: '2024-02-05');

        $credentials = $this->makeCredentials(['delivered_at' => '2024-02-01~2024-02-03']);

        /** @var OrderQueryService $tested_method */
        $tested_method = $this->app->make(OrderQueryService::class);

        // WHEN
        $response = $tested_method->list($credentials);

        // THEN
        $this->assertDatabaseCount(BlcaOrder::class, 6);
        $this->assertCount(4, $response);
    }

    /**
     * @test
     * @throws \Exception
     */
    public function listOrders_responseSearchByDeliveryDateList_threeMatches()
    {
        // GIVEN
        $user_one = $this->createUserAndBe();
        $warehouse = $this->createWarehouse();
        $this->attachUserWarehouse($user_one, $warehouse);

        $this->createCustomOrder(delivery_date: '2024-02-01');
        $this->createCustomOrder(delivery_date: '2024-02-02');
        $this->createCustomOrder(delivery_date: '2024-02-03');
        $this->createCustomOrder(delivery_date: '2024-02-03');
        $this->createCustomOrder(delivery_date: '2024-02-04');
        $this->createCustomOrder(delivery_date: '2024-02-05');

        $credentials = $this->makeCredentials(['delivered_at' => '2024-02-01,2024-02-03']);

        /** @var OrderQueryService $tested_method */
        $tested_method = $this->app->make(OrderQueryService::class);

        // WHEN
        $response = $tested_method->list($credentials);

        // THEN
        $this->assertDatabaseCount(BlcaOrder::class, 6);
        $this->assertCount(3, $response);
    }

    /**
     * @test
     * @throws \Exception
     */
    public function listOrders_responseSearchByCreatedDateRange_oneMatches()
    {
        // GIVEN
        $user_one = $this->createUserAndBe();
        $warehouse = $this->createWarehouse();
        $this->attachUserWarehouse($user_one, $warehouse);

        $dataTime = '2024-02-01 12:00:00';

        $this->createCustomOrder(created_date: $dataTime);
        $this->createCustomOrder();
        $this->createCustomOrder();
        $this->createCustomOrder();

        $credentials = $this->makeCredentials(['created_at' => '2024-02-01~2024-02-03']);

        /** @var OrderQueryService $tested_method */
        $tested_method = $this->app->make(OrderQueryService::class);

        // WHEN
        $response = $tested_method->list($credentials);

        // THEN
        $this->assertDatabaseCount(BlcaOrder::class, 4);
        $this->assertEquals($dataTime,   $response->items()[0]->cdate->toDateTimeString());
        $this->assertCount(1, $response);
    }

    /**
     * @test
     * @throws \Exception
     */
    public function listOrders_responseSearchByCreatedDateRange_allMatches()
    {
        // GIVEN
        $user_one = $this->createUserAndBe();
        $warehouse = $this->createWarehouse();
        $this->attachUserWarehouse($user_one, $warehouse);

        $this->createCustomOrder(created_date: '2024-02-01 12:00:00');
        $this->createCustomOrder(created_date: '2024-02-02 12:00:00');
        $this->createCustomOrder(created_date: '2024-02-03 12:00:00');
        $this->createCustomOrder(created_date: '2024-02-05 12:00:00');

        $credentials = $this->makeCredentials(['created_at' => '2024-02-01~2024-02-05']);

        /** @var OrderQueryService $tested_method */
        $tested_method = $this->app->make(OrderQueryService::class);

        // WHEN
        $response = $tested_method->list($credentials);

        // THEN
        $this->assertDatabaseCount(BlcaOrder::class, 4);
        $this->assertCount(4, $response);
    }

    /**
     * @test
     * @throws \Exception
     */
    public function listOrders_responseSearchByUpdatedDateRange_oneMatches()
    {
        // GIVEN
        $user_one = $this->createUserAndBe();
        $warehouse = $this->createWarehouse();
        $this->attachUserWarehouse($user_one, $warehouse);

        $dataTime = '2024-02-01 12:00:00';

        $this->createCustomOrder(updated_date: $dataTime);
        $this->createCustomOrder();
        $this->createCustomOrder();
        $this->createCustomOrder();

        $credentials = $this->makeCredentials(['updated_at' => '2024-02-01~2024-02-03']);

        /** @var OrderQueryService $tested_method */
        $tested_method = $this->app->make(OrderQueryService::class);

        // WHEN
        $response = $tested_method->list($credentials);

        // THEN
        $this->assertDatabaseCount(BlcaOrder::class, 4);
        $this->assertEquals($dataTime,   $response->items()[0]->mdate->toDateTimeString());
        $this->assertCount(1, $response);
    }

    /**
     * @test
     * @throws \Exception
     */
    public function listOrders_responseSearchByOccasion_oneMatches()
    {
        // GIVEN
        $user_one = $this->createUserAndBe();
        $warehouse = $this->createWarehouse();
        $this->attachUserWarehouse($user_one, $warehouse);

        $order = $this->createCustomOrder(customer_occasion: 'Christmas');
        $this->createCustomOrder(customer_occasion: 'Birthday');

        $credentials = $this->makeCredentials(['occasion' => $order->customer_occasion]);

        /** @var OrderQueryService $tested_method */
        $tested_method = $this->app->make(OrderQueryService::class);

        // WHEN
        $response = $tested_method->list($credentials);

        // THEN
        $this->assertDatabaseCount(BlcaOrder::class, 2);
        $this->assertEquals($order->customer_occasion, $response->items()[0]->customer_occasion);
        $this->assertCount(1, $response->items());
    }

    /**
     * @test
     * @throws \Exception
     */
    public function listOrders_responseSearchByWarehouse_oneMatches()
    {
        // GIVEN
        $user_one = $this->createUserAndBe();
        $warehouseOne = $this->createCustomWarehouse('Calgary', 'WH05');
        $warehouseTwo = $this->createCustomWarehouse('Edmonton', 'WH01');
        $this->attachUserWarehouse($user_one, $warehouseOne);
        $this->attachUserWarehouse($user_one, $warehouseTwo);

        $this->createCustomOrder(warehouse: $warehouseTwo);
        $this->createCustomOrder(warehouse: $warehouseOne);
        $this->createCustomOrder(warehouse: $warehouseTwo);
        $this->createCustomOrder(warehouse: $warehouseTwo);

        $credentials = $this->makeCredentials(['warehouse' => $warehouseOne->warehouse_code]);

        /** @var OrderQueryService $tested_method */
        $tested_method = $this->app->make(OrderQueryService::class);

        // WHEN
        $response = $tested_method->list($credentials);

        // THEN
        $this->assertDatabaseCount(BlcaOrder::class, 4);
        $this->assertTrue( $response->items()[0]->warehouse === 'WH05');
        $this->assertCount(1, $response->items());
    }

    /**
     * @test
     * @throws \Exception
     */
    public function listOrders_responseSortByIdAsc()
    {
        // GIVEN
        $user_one = $this->createUserAndBe();
        $warehouse = $this->createWarehouse();
        $this->attachUserWarehouse($user_one, $warehouse);

        $this->createOrder();
        $this->createOrder();
        $this->createOrder();

        $credentials = $this->makeCredentials(['sort' => 'id']);

        /** @var OrderQueryService $tested_method */
        $tested_method = $this->app->make(OrderQueryService::class);

        // WHEN
        $response = $tested_method->list($credentials);

        // THEN
        $this->assertCount(3, $response->items());
        $this->assertTrue( $response->items()[2]->order_id > $response->items()[1]->order_id);
        $this->assertTrue( $response->items()[1]->order_id > $response->items()[0]->order_id);
    }

    /**
     * @test
     * @throws \Exception
     */
    public function listOrders_responseSortByIdDesc()
    {
        // GIVEN
        $user_one = $this->createUserAndBe();
        $warehouse = $this->createWarehouse();
        $this->attachUserWarehouse($user_one, $warehouse);

        $this->createOrder();
        $this->createOrder();
        $this->createOrder();

        $credentials = $this->makeCredentials(['sort' => '-id']);

        /** @var OrderQueryService $tested_method */
        $tested_method = $this->app->make(OrderQueryService::class);

        // WHEN
        $response = $tested_method->list($credentials);

        // THEN
        $this->assertCount(3, $response->items());
        $this->assertTrue( $response->items()[0]->order_id > $response->items()[1]->order_id);
        $this->assertTrue( $response->items()[1]->order_id > $response->items()[2]->order_id);
    }

    /**
     * @test
     * @throws \Exception
     */
    public function listOrders_responseSortByOccasionAsc()
    {
        // GIVEN
        $user_one = $this->createUserAndBe();
        $warehouse = $this->createWarehouse();
        $this->attachUserWarehouse($user_one, $warehouse);

        $this->createCustomOrder(customer_occasion: 'Christmas'); //CHR
        $this->createCustomOrder(customer_occasion: 'Apology'); //APOL
        $this->createCustomOrder(customer_occasion: 'Birthday'); //BIRTH

        $credentials = $this->makeCredentials(['sort' => 'occasion']);

        /** @var OrderQueryService $tested_method */
        $tested_method = $this->app->make(OrderQueryService::class);

        // WHEN
        $response = $tested_method->list($credentials);

        // THEN
        $this->assertCount(3, $response->items());
        $this->assertTrue( $response->items()[0]->customer_occasion === 'APOL');
        $this->assertTrue( $response->items()[1]->customer_occasion === 'BIRTH');
        $this->assertTrue( $response->items()[2]->customer_occasion === 'CHR');
    }

    /**
     * @test
     * @throws \Exception
     */
    public function listOrders_responseSortByOccasionDesc()
    {
        // GIVEN
        $user_one = $this->createUserAndBe();
        $warehouse = $this->createWarehouse();
        $this->attachUserWarehouse($user_one, $warehouse);

        $this->createCustomOrder(customer_occasion: 'Christmas'); //CHR
        $this->createCustomOrder(customer_occasion: 'Apology'); //APOL
        $this->createCustomOrder(customer_occasion: 'Birthday'); //BIRTH

        $credentials = $this->makeCredentials(['sort' => '-occasion']);

        /** @var OrderQueryService $tested_method */
        $tested_method = $this->app->make(OrderQueryService::class);

        // WHEN
        $response = $tested_method->list($credentials);

        // THEN
        $this->assertCount(3, $response->items());
        $this->assertTrue( $response->items()[0]->customer_occasion === 'CHR');
        $this->assertTrue( $response->items()[1]->customer_occasion === 'BIRTH');
        $this->assertTrue( $response->items()[2]->customer_occasion === 'APOL');
    }

    /**
     * @test
     * @throws \Exception
     */
    public function listOrders_responseSortByOccasionDescIdDesc()
    {
        // GIVEN
        $user_one = $this->createUserAndBe();
        $warehouse = $this->createWarehouse();
        $this->attachUserWarehouse($user_one, $warehouse);

        $this->createCustomOrder(customer_occasion: 'Christmas'); //CHR
        $this->createCustomOrder(customer_occasion: 'Apology'); //APOL
        $this->createCustomOrder(customer_occasion: 'Birthday'); //BIRTH
        $this->createCustomOrder(customer_occasion: 'Birthday'); //BIRTH

        $credentials = $this->makeCredentials(['sort' => '-occasion,-id']);

        /** @var OrderQueryService $tested_method */
        $tested_method = $this->app->make(OrderQueryService::class);

        // WHEN
        $response = $tested_method->list($credentials);

        // THEN
        $this->assertCount(4, $response->items());
        $this->assertTrue( $response->items()[0]->customer_occasion === 'CHR');
        $this->assertTrue( $response->items()[1]->customer_occasion === 'BIRTH');
        $this->assertTrue( $response->items()[2]->customer_occasion === 'BIRTH');
        $this->assertTrue( $response->items()[2]->order_id < $response->items()[1]->order_id);
        $this->assertTrue( $response->items()[3]->customer_occasion === 'APOL');
    }

    /**
     * @test
     * @throws \Exception
     */
    public function listOrders_responseSortByOccasionDescIdAsc()
    {
        // GIVEN
        $user_one = $this->createUserAndBe();
        $warehouse = $this->createWarehouse();
        $this->attachUserWarehouse($user_one, $warehouse);

        $this->createCustomOrder(warehouse: $warehouse, customer_occasion: 'Christmas'); //CHR
        $this->createCustomOrder(warehouse: $warehouse, customer_occasion: 'Apology'); //APOL
        $this->createCustomOrder(warehouse: $warehouse, customer_occasion: 'Birthday'); //BIRTH
        $this->createCustomOrder(warehouse: $warehouse, customer_occasion: 'Birthday'); //BIRTH

        $credentials = $this->makeCredentials(['sort' => '-occasion,id']);

        /** @var OrderQueryService $tested_method */
        $tested_method = $this->app->make(OrderQueryService::class);

        // WHEN
        $response = $tested_method->list($credentials);

        // THEN
        $this->assertCount(4, $response->items());
        $this->assertTrue( $response->items()[0]->customer_occasion === 'CHR');
        $this->assertTrue( $response->items()[1]->customer_occasion === 'BIRTH');
        $this->assertTrue( $response->items()[2]->customer_occasion === 'BIRTH');
        $this->assertTrue( $response->items()[2]->order_id > $response->items()[1]->order_id);
        $this->assertTrue( $response->items()[3]->customer_occasion === 'APOL');
    }

    /**
     * @test
     * @throws \Exception
     */
    public function listOrders_responseSortByWarehouseDesc()
    {
        // GIVEN
        $user_one = $this->createUserAndBe();
        $warehouseOne = $this->createCustomWarehouse('Calgary', 'WH05');
        $warehouseTwo = $this->createCustomWarehouse('Edmonton', 'WH01');
        $this->attachUserWarehouse($user_one, $warehouseOne);
        $this->attachUserWarehouse($user_one, $warehouseTwo);

        $this->createCustomOrder(warehouse: $warehouseOne);
        $this->createCustomOrder(warehouse: $warehouseOne);
        $this->createCustomOrder(warehouse: $warehouseTwo);
        $this->createCustomOrder(warehouse: $warehouseTwo);

        $credentials = $this->makeCredentials(['sort' => '-warehouse']);

        /** @var OrderQueryService $tested_method */
        $tested_method = $this->app->make(OrderQueryService::class);

        // WHEN
        $response = $tested_method->list($credentials);

        // THEN
        $this->assertTrue( $response->items()[0]->warehouse === 'WH01');
        $this->assertTrue( $response->items()[1]->warehouse === 'WH01');
        $this->assertTrue( $response->items()[2]->warehouse === 'WH05');
        $this->assertTrue( $response->items()[3]->warehouse === 'WH05');
        $this->assertCount(4, $response->items());
    }

    /**
     * @test
     * @throws \Exception
     */
    public function listOrders_responseSortByWarehouseAsc()
    {
        // GIVEN
        $user_one = $this->createUserAndBe();
        $warehouseOne = $this->createCustomWarehouse('Calgary', 'WH05');
        $warehouseTwo = $this->createCustomWarehouse('Edmonton', 'WH01');
        $this->attachUserWarehouse($user_one, $warehouseOne);
        $this->attachUserWarehouse($user_one, $warehouseTwo);

        $this->createCustomOrder(warehouse: $warehouseOne);
        $this->createCustomOrder(warehouse: $warehouseOne);
        $this->createCustomOrder(warehouse: $warehouseTwo);
        $this->createCustomOrder(warehouse: $warehouseTwo);

        $credentials = $this->makeCredentials(['sort' => 'warehouse']);

        /** @var OrderQueryService $tested_method */
        $tested_method = $this->app->make(OrderQueryService::class);

        // WHEN
        $response = $tested_method->list($credentials);

        // THEN
        $this->assertTrue( $response->items()[0]->warehouse === 'WH05');
        $this->assertTrue( $response->items()[1]->warehouse === 'WH05');
        $this->assertTrue( $response->items()[2]->warehouse === 'WH01');
        $this->assertTrue( $response->items()[3]->warehouse === 'WH01');
        $this->assertCount(4, $response->items());
    }

    /**
     * @test
     * @throws \Exception
     */
    public function listOrders_responseSortByStatusAsc()
    {
        // GIVEN
        $user_one = $this->createUserAndBe();
        $warehouse = $this->createWarehouse();
        $this->attachUserWarehouse($user_one, $warehouse);

        $this->createCustomOrder(warehouse: $warehouse, order_status: 'Z'); //IN TRANSIT #2
        $this->createCustomOrder(warehouse: $warehouse, order_status: 'D'); //DELIVERED #1
        $this->createCustomOrder(warehouse: $warehouse, order_status: 'A'); //PAID #3
        //because sorting by name not code

        $credentials = $this->makeCredentials(['sort' => 'status']);

        /** @var OrderQueryService $tested_method */
        $tested_method = $this->app->make(OrderQueryService::class);

        // WHEN
        $response = $tested_method->list($credentials);

        // THEN
        $this->assertCount(3, $response->items());
        $this->assertTrue( $response->items()[0]->order_status === 'D');
        $this->assertTrue( $response->items()[1]->order_status === 'Z');
        $this->assertTrue( $response->items()[2]->order_status === 'A');
    }

    /**
     * @test
     * @throws \Exception
     */
    public function listOrders_responseSortByStatusDesc()
    {
        // GIVEN
        $user_one = $this->createUserAndBe();
        $warehouse = $this->createWarehouse();
        $this->attachUserWarehouse($user_one, $warehouse);

        $this->createCustomOrder(warehouse: $warehouse, order_status: 'Z'); //IN TRANSIT #2
        $this->createCustomOrder(warehouse: $warehouse, order_status: 'D'); //DELIVERED #3
        $this->createCustomOrder(warehouse: $warehouse, order_status: 'A'); //PAID #1
        //because sorting by name not code

        $credentials = $this->makeCredentials(['sort' => '-status']);

        /** @var OrderQueryService $tested_method */
        $tested_method = $this->app->make(OrderQueryService::class);

        // WHEN
        $response = $tested_method->list($credentials);

        // THEN
        $this->assertCount(3, $response->items());
        $this->assertTrue( $response->items()[0]->order_status === 'A');
        $this->assertTrue( $response->items()[1]->order_status === 'Z');
        $this->assertTrue( $response->items()[2]->order_status === 'D');
    }

    /**
     * @test
     * @throws \Exception
     */
    public function listOrders_responseSortByOccasionAscStatusDesc()
    {
        // GIVEN
        $user_one = $this->createUserAndBe();
        $warehouse = $this->createWarehouse();
        $this->attachUserWarehouse($user_one, $warehouse);

        $this->createCustomOrder(warehouse: $warehouse, customer_occasion: 'Christmas', order_status: 'Z'); //CHR #4
        $this->createCustomOrder(warehouse: $warehouse, customer_occasion: 'Apology', order_status: 'D'); //APOL #1
        $this->createCustomOrder(warehouse: $warehouse, customer_occasion: 'Apology', order_status: 'Z'); //APOL #2
        $this->createCustomOrder(warehouse: $warehouse, customer_occasion: 'Birthday', order_status: 'Z'); //BIRTH #3

        $credentials = $this->makeCredentials(['sort' => 'occasion,-status']);

        /** @var OrderQueryService $tested_method */
        $tested_method = $this->app->make(OrderQueryService::class);

        // WHEN
        $response = $tested_method->list($credentials);

        // THEN
        $this->assertCount(4, $response->items());
        $this->assertTrue( $response->items()[0]->order_status === 'Z');
        $this->assertTrue( $response->items()[0]->customer_occasion === 'APOL');
        $this->assertTrue( $response->items()[1]->order_status === 'D');
        $this->assertTrue( $response->items()[1]->customer_occasion === 'APOL');
        $this->assertTrue( $response->items()[2]->order_status === 'Z');
        $this->assertTrue( $response->items()[2]->customer_occasion === 'BIRTH');
        $this->assertTrue( $response->items()[3]->order_status === 'Z');
        $this->assertTrue( $response->items()[3]->customer_occasion === 'CHR');
    }

    /**
     * @test
     * @throws \Exception
     */
    public function listOrders_responseSortByOccasionDescStatusDesc()
    {
        // GIVEN
        $user_one = $this->createUserAndBe();
        $warehouse = $this->createWarehouse();
        $this->attachUserWarehouse($user_one, $warehouse);

        $this->createCustomOrder(warehouse: $warehouse, customer_occasion: 'Christmas', order_status: 'Z'); //CHR #4
        $this->createCustomOrder(warehouse: $warehouse, customer_occasion: 'Apology', order_status: 'D'); //APOL #1
        $this->createCustomOrder(warehouse: $warehouse, customer_occasion: 'Apology', order_status: 'Z'); //APOL #2
        $this->createCustomOrder(warehouse: $warehouse, customer_occasion: 'Birthday', order_status: 'Z'); //BIRTH #3

        $credentials = $this->makeCredentials(['sort' => '-occasion,-status']);

        /** @var OrderQueryService $tested_method */
        $tested_method = $this->app->make(OrderQueryService::class);

        // WHEN
        $response = $tested_method->list($credentials);

        // THEN
        $this->assertCount(4, $response->items());
        $this->assertTrue( $response->items()[0]->order_status === 'Z');
        $this->assertTrue( $response->items()[0]->customer_occasion === 'CHR');
        $this->assertTrue( $response->items()[1]->order_status === 'Z');
        $this->assertTrue( $response->items()[1]->customer_occasion === 'BIRTH');
        $this->assertTrue( $response->items()[2]->order_status === 'Z');
        $this->assertTrue( $response->items()[2]->customer_occasion === 'APOL');
        $this->assertTrue( $response->items()[3]->order_status === 'D');
        $this->assertTrue( $response->items()[3]->customer_occasion === 'APOL');
    }

    /**
     * @test
     * @throws \Exception
     */
    public function listOrders_responseSortByOccasionDescStatusAsc()
    {
        // GIVEN
        $user_one = $this->createUserAndBe();
        $warehouse = $this->createWarehouse();
        $this->attachUserWarehouse($user_one, $warehouse);

        $this->createCustomOrder(warehouse: $warehouse, customer_occasion: 'Christmas', order_status: 'Z'); //CHR #4
        $this->createCustomOrder(warehouse: $warehouse, customer_occasion: 'Apology', order_status: 'D'); //APOL #1
        $this->createCustomOrder(warehouse: $warehouse, customer_occasion: 'Apology', order_status: 'Z'); //APOL #2
        $this->createCustomOrder(warehouse: $warehouse, customer_occasion: 'Birthday', order_status: 'Z'); //BIRTH #3

        $credentials = $this->makeCredentials(['sort' => '-occasion,status']);

        /** @var OrderQueryService $tested_method */
        $tested_method = $this->app->make(OrderQueryService::class);

        // WHEN
        $response = $tested_method->list($credentials);

        // THEN
        $this->assertCount(4, $response->items());
        $this->assertTrue( $response->items()[0]->order_status === 'Z');
        $this->assertTrue( $response->items()[0]->customer_occasion === 'CHR');
        $this->assertTrue( $response->items()[1]->order_status === 'Z');
        $this->assertTrue( $response->items()[1]->customer_occasion === 'BIRTH');
        $this->assertTrue( $response->items()[2]->order_status === 'D');
        $this->assertTrue( $response->items()[2]->customer_occasion === 'APOL');
        $this->assertTrue( $response->items()[3]->order_status === 'Z');
        $this->assertTrue( $response->items()[3]->customer_occasion === 'APOL');
    }

    /**
     * @test
     * @throws \Exception
     */
    public function listOrders_withDriverRate_dbHasTwo()
    {
        // GIVEN
        $user_one = $this->createUserAndBe();
        $warehouse = $this->createWarehouse();
        $this->attachUserWarehouse($user_one, $warehouse);

        $this->createOrder($warehouse, true, true, 'R1R');
        $this->createOrder($warehouse, true, true, 'T1T1T1');

        $credentials = $this->makeCredentials([]);

        /** @var OrderQueryService $tested_method */
        $tested_method = $this->app->make(OrderQueryService::class);

        // WHEN
        $response = $tested_method->list($credentials);

        // THEN
        $this->assertDatabaseCount(BlcaOrder::class, 2);
        $this->assertDatabaseCount(BlcaDriverRate::class, 2);
        $this->assertCount(2, $response);
        $this->assertInstanceOf(BlcaDriverRate::class, $response->first()->rate);
        $this->assertInstanceOf(BlcaDriverRate::class, $response->last()->rate);
    }

    /**
     * @test
     * @throws \Exception
     */
    public function listOrders_withDriverRate_lowerCase()
    {
        // GIVEN
        $user_one = $this->createUserAndBe();
        $warehouse = $this->createWarehouse();
        $this->attachUserWarehouse($user_one, $warehouse);

        $this->createOrder($warehouse, true, true, 'r1r1r1');
        $this->createOrder($warehouse, true, true, 'T1T1T1');

        $credentials = $this->makeCredentials([]);

        /** @var OrderQueryService $tested_method */
        $tested_method = $this->app->make(OrderQueryService::class);

        // WHEN
        $response = $tested_method->list($credentials);

        // THEN
        $this->assertDatabaseCount(BlcaOrder::class, 2);
        $this->assertDatabaseCount(BlcaDriverRate::class, 2);
        $this->assertCount(2, $response);
        $this->assertInstanceOf(BlcaDriverRate::class, $response->first()->rate);
        $this->assertInstanceOf(BlcaDriverRate::class, $response->last()->rate);
    }

    /**
     * @test
     * @throws \Exception
     */
    public function listOrders_withDriverRate_dbHasOne()
    {
        // GIVEN
        $user_one = $this->createUserAndBe();
        $warehouse = $this->createWarehouse();
        $this->attachUserWarehouse($user_one, $warehouse);

        $this->createOrder($warehouse, true, true, 'R1R1R1');
        $this->createOrder($warehouse);

        $credentials = $this->makeCredentials([]);

        /** @var OrderQueryService $tested_method */
        $tested_method = $this->app->make(OrderQueryService::class);

        // WHEN
        $response = $tested_method->list($credentials);

        // THEN
        $this->assertDatabaseCount(BlcaOrder::class, 2);
        $this->assertDatabaseCount(BlcaDriverRate::class, 1);
        $this->assertCount(2, $response);
        $this->assertInstanceOf(BlcaDriverRate::class, $response->last()->rate);
        $this->assertNull($response->first()->rate);
    }
}