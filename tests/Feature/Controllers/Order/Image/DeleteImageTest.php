<?php

namespace Tests\Feature\Controllers\Order\Image;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class DeleteImageTest extends TestCase
{
    use DatabaseTransactions;

    /** @test  */
    public function removeImage_codeSuccess()
    {
        // GIVEN
        $user = $this->createUserAndBe();
        $warehouse = $this->createWarehouse();
        $this->attachUserWarehouse($user, $warehouse);
        $order = $this->createOrder();
        $orderHistories = $this->createOrderHistory(1, $order, $warehouse);
        $images = $this->createOrderHistoryImage( $orderHistories->first());
        $imageId = $images->first()->id;

        // WHEN
        $response = $this->withHeaders(['Accept' => 'application/json'])
            ->json('delete', route('history.image.delete',  ['historyImage' => $imageId]));

        // THEN
        $this->assertEquals(200, $response->getStatusCode());
    }

    /** @test  */
    public function removeImage_checkJsonStructure_success()
    {
        // GIVEN
        $user = $this->createUserAndBe();
        $warehouse = $this->createWarehouse();
        $this->attachUserWarehouse($user, $warehouse);
        $order = $this->createOrder();
        $orderHistories = $this->createOrderHistory(1, $order, $warehouse);
        $images = $this->createOrderHistoryImage( $orderHistories->first());
        $imageId = $images->first()->id;

        // WHEN
        $response = $this->withHeaders(['Accept' => 'application/json'])
            ->json('delete', route('history.image.delete',  ['historyImage' => $imageId]));

        // THEN
        $response->assertJsonStructure([
            'message',
        ]);
    }

    /** @test  */
    public function removeImage_wrongIdGiven_failed()
    {
        // GIVEN
        $user = $this->createUserAndBe();
        $warehouse = $this->createWarehouse();
        $this->attachUserWarehouse($user, $warehouse);
        $order = $this->createOrder();
        $orderHistories = $this->createOrderHistory(1, $order, $warehouse);
        $images = $this->createOrderHistoryImage( $orderHistories->first());
        $imageId = $images->first()->id;

        // WHEN
        $response = $this->withHeaders(['Accept' => 'application/json'])
            ->json('delete', route('history.image.delete',  ['historyImage' => -258]));

        // THEN
        $this->assertEquals(404, $response->getStatusCode());
        $response->assertJsonStructure([
            'message',
        ]);
    }

    /** @test  */
    public function removeImage_noLogin_success()
    {
        // GIVEN
        $user = $this->createUser();
        $warehouse = $this->createWarehouse();
        $this->attachUserWarehouse($user, $warehouse);
        $order = $this->createOrder();
        $orderHistories = $this->createOrderHistory(1, $order, $warehouse);
        $images = $this->createOrderHistoryImage( $orderHistories->first());
        $imageId = $images->first()->id;

        // WHEN
        $response = $this->withHeaders(['Accept' => 'application/json'])
            ->json('delete', route('history.image.delete',  ['historyImage' => $imageId]));

        // THEN
        $this->assertEquals(401, $response->getStatusCode());
    }
}