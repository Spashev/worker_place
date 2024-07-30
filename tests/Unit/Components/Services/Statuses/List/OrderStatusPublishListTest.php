<?php

namespace Tests\Unit\Components\Services\Statuses\List;

use App\Components\Orders\Services\StatusService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class OrderStatusPublishListTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * @test
     * @throws \Exception
     */
    public function listPublishStatus_dbHasTwoPublish()
    {
        // GIVEN
        $this->createUserAndBe();

        $this->createBlcaOrderStatus('1', 'test');
        $this->createBlcaOrderStatus('TE', 'Random word');
        $this->createBlcaOrderStatus('DF', 'test','0');

        /** @var StatusService $tested_method */
        $tested_method = $this->app->make(StatusService::class);

        // WHEN
        $response = $tested_method->list();

        // THEN
        $this->assertCount(2, $response);
    }
}