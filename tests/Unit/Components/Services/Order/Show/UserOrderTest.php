<?php

namespace Tests\Unit\Components\Services\Order\Show;

use App\Components\Orders\Services\OrderService\OrderQueryService;
use App\Exceptions\InvalidOrderUserAccess;
use Bloomex\Common\Blca\Models\BlcaOrder;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class UserOrderTest extends TestCase
{
    use DatabaseTransactions;
    use UserOrderTrait;

    /**
     * @test
     * @throws \Exception
     */
    public function order_responseOrder_dbHasTwo()
    {
        // GIVEN
        $user_one = $this->createUserAndBe();
        $warehouse = $this->createWarehouse();
        $this->attachUserWarehouse($user_one, $warehouse);

        $this->createOrder();
        $order = $this->createOrder();

        /** @var OrderQueryService $tested_method */
        $tested_method = $this->app->make(OrderQueryService::class);

        // WHEN
        $response = $tested_method->show($order);

        // THEN
        $this->assertDatabaseCount(BlcaOrder::class, 2);
        $this->assertEquals($order->order_id, $response->order_id);
    }

    /**
     * @test
     * @throws \Exception
     */
    public function order_responseNoOrder_dbHasTwo()
    {
        // GIVEN
        $user = $this->createUserAndBe();
        $this->createCustomWarehouse('name', 'WH00');
        $order = $this->createOrder();
        $this->createOrder();

        $warehouse = $this->createCustomWarehouse('new', 'WH01');
        $this->attachUserWarehouse($user, $warehouse);

        /** @var OrderQueryService $tested_method */
        $tested_method = $this->app->make(OrderQueryService::class);

        // EXPECT
        $this->expectException(InvalidOrderUserAccess::class);

        // WHEN
        $tested_method->show($order);

        // THEN
        $this->assertDatabaseCount(BlcaOrder::class, 2);
    }
}