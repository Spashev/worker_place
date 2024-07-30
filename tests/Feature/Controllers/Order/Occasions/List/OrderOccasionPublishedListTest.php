<?php

namespace Tests\Feature\Controllers\Order\Occasions\List;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class OrderOccasionPublishedListTest extends TestCase
{
    use DatabaseTransactions;
    use OrderOccasionPublishedListTrait;

    /**
     * @test
     */
    public function listPublishedOccasions_success_response200()
    {
        // GIVEN
        $this->createUserAndBe();

        $this->createBlcaOrderOccasion('TEST', 'test');
        $this->createBlcaOrderOccasion('TE', 'Random word');
        $this->createBlcaOrderOccasion('DF', 'test',0);

        // WHEN
        $response = $this->withHeaders(['Accept' => 'application/json'])
            ->json('get', route('occasion.list'));

        // THEN
        $this->assertEquals(200, $response->getStatusCode());
    }

    /**
     * @test
     */
    public function listPublishedOccasions_success_checkStructure()
    {
        // GIVEN
        $this->createUserAndBe();

        $this->createBlcaOrderOccasion('TEST', 'test');
        $this->createBlcaOrderOccasion('TE', 'Random word');
        $this->createBlcaOrderOccasion('DF', 'test',0);

        // WHEN
        $response = $this->withHeaders(['Accept' => 'application/json'])
            ->json('get', route('occasion.list'));

        // THEN
        $this->assertJsonStructure($response);
    }

    /**
     * @test
     */
    public function listPublishedOccasions_failed_unauthenticated()
    {
        // GIVEN
        $this->createUser();

        $this->createBlcaOrderOccasion('TEST', 'test');
        $this->createBlcaOrderOccasion('TE', 'Random word');
        $this->createBlcaOrderOccasion('DF', 'test',0);

        // WHEN
        $response = $this->withHeaders(['Accept' => 'application/json'])
            ->json('get', route('occasion.list'));

        // THEN
        $this->assertEquals(401, $response->getStatusCode());
        $response->assertUnauthorized();
    }
}