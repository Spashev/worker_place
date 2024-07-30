<?php

namespace Tests\Unit\Components\Services\Occasions\List;

use App\Components\Orders\Services\OccasionsService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class OrderOccasionPublishedListTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * @test
     * @throws \Exception
     */
    public function listPublishedOccasions_dbHasTwoPublished()
    {
        // GIVEN
        $this->createUserAndBe();

        $this->createBlcaOrderOccasion('TEST', 'test');
        $this->createBlcaOrderOccasion('TE', 'Random word');
        $this->createBlcaOrderOccasion('DF', 'test',0);

        /** @var OccasionsService $tested_method */
        $tested_method = $this->app->make(OccasionsService::class);

        // WHEN
        $response = $tested_method->list();

        // THEN
        $this->assertCount(2, $response);
    }
}