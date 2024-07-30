<?php

namespace Tests\Feature\Controllers\Order\Image;

use Tests\TestCase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\Unit\Components\Services\Image\SaveImage\SaveImageTrait;

class SaveImageTest extends TestCase
{
    use DatabaseTransactions;
    use SaveImageTrait;

    protected function setUp(): void
    {
        parent::setUp();
        $this->storage = config('image.disk');
    }

    /** @test  */
    public function saveImage_codeSuccess()
    {
        // GIVEN
        Storage::fake($this->storage);
        Bus::fake();

        $user = $this->createUserAndBe();
        $warehouse = $this->createWarehouse();
        $this->attachUserWarehouse($user, $warehouse);
        $order = $this->createOrder();
        $orderHistories = $this->createOrderHistory(1, $order, $warehouse);
        $id = $orderHistories->first()->order_status_history_id;

        $img = UploadedFile::fake()->image('new_image.jpg')->size(400);
        $entryData = [
            'image' => $img,
        ];

        // WHEN
        $response = $this->json('post',
            route('history.image.add', ['history' => $id]), $entryData,
            ['Content_Type' => 'FormData']);

        // THEN
        $this->assertEquals(201, $response->getStatusCode());
    }

    /** @test  */
    public function saveImage_checkJsonStructure_success()
    {
        // GIVEN
        Storage::fake($this->storage);
        Bus::fake();

        $user = $this->createUserAndBe();
        $warehouse = $this->createWarehouse();
        $this->attachUserWarehouse($user, $warehouse);
        $order = $this->createOrder();
        $orderHistories = $this->createOrderHistory(1, $order, $warehouse);
        $id = $orderHistories->first()->order_status_history_id;

        $img = UploadedFile::fake()->image('new_image.jpg')->size(400);
        $entryData = [
            'image' => $img,
        ];

        // WHEN
        $response = $this->json('post',
            route('history.image.add', ['history' => $id]), $entryData,
            ['Content_Type' => 'FormData']);

        // THEN
        $this->assertJson($response->baseResponse->getContent());
        $response->assertJsonStructure([
            'id',
            'thumb',
            'original'
        ]);
    }

    /** @test  */
    public function saveImage_toLarge_failed()
    {
        // GIVEN
        Storage::fake($this->storage);
        Bus::fake();

        $user = $this->createUserAndBe();
        $warehouse = $this->createWarehouse();
        $this->attachUserWarehouse($user, $warehouse);
        $order = $this->createOrder();
        $orderHistories = $this->createOrderHistory(1, $order, $warehouse);
        $id = $orderHistories->first()->order_status_history_id;

        $img = UploadedFile::fake()->image('new_image.jpg')->size(10000);
        $entryData = [
            'image' => $img,
        ];

        // WHEN
        $response = $this->json('post',
            route('history.image.add', ['history' => $id]), $entryData,
            ['Content_Type' => 'FormData']);

        // THEN
        $this->assertEquals(422, $response->getStatusCode());
        $this->assertJson($response->baseResponse->getContent());
        $response->assertJsonStructure([
            'message',
        ]);
    }

    /** @test  */
    public function saveImage_wrongName_failed()
    {
        // GIVEN
        Storage::fake($this->storage);
        Bus::fake();

        $user = $this->createUserAndBe();
        $warehouse = $this->createWarehouse();
        $this->attachUserWarehouse($user, $warehouse);
        $order = $this->createOrder();
        $orderHistories = $this->createOrderHistory(1, $order, $warehouse);
        $id = $orderHistories->first()->order_status_history_id;

        $img = UploadedFile::fake()->image('new_image.jpg')->size(100);
        $entryData = [
            'wrong' => $img,
        ];

        // WHEN
        $response = $this->json('post',
            route('history.image.add', ['history' => $id]), $entryData,
            ['Content_Type' => 'FormData']);

        // THEN
        $this->assertEquals(422, $response->getStatusCode());
        $this->assertJson($response->baseResponse->getContent());
        $response->assertJsonStructure([
            'message',
        ]);
    }

    /** @test  */
    public function saveImage_wrongExtension_failed()
    {
        // GIVEN
        Storage::fake($this->storage);
        Bus::fake();

        $user = $this->createUserAndBe();
        $warehouse = $this->createWarehouse();
        $this->attachUserWarehouse($user, $warehouse);
        $order = $this->createOrder();
        $orderHistories = $this->createOrderHistory(1, $order, $warehouse);
        $id = $orderHistories->first()->order_status_history_id;

        $img = UploadedFile::fake()->image('new_image.pdf')->size(100);
        $entryData = [
            'wrong' => $img,
        ];

        // WHEN
        $response = $this->json('post',
            route('history.image.add', ['history' => $id]), $entryData,
            ['Content_Type' => 'FormData']);

        // THEN
        $this->assertEquals(422, $response->getStatusCode());
        $this->assertJson($response->baseResponse->getContent());
        $response->assertJsonStructure([
            'message',
        ]);
    }

    /** @test  */
    public function saveImage_userNotLogin_failed()
    {
        // GIVEN
        Storage::fake($this->storage);
        Bus::fake();

        $user = $this->createUser();
        $warehouse = $this->createWarehouse();
        $this->attachUserWarehouse($user, $warehouse);
        $order = $this->createOrder();
        $orderHistories = $this->createOrderHistory(1, $order, $warehouse);
        $id = $orderHistories->first()->order_status_history_id;

        $img = UploadedFile::fake()->image('new_image.pdf')->size(100);
        $entryData = [
            'image' => $img,
        ];

        // WHEN
        $response = $this->json('post',
            route('history.image.add', ['history' => $id]), $entryData,
            ['Content_Type' => 'FormData']);

        // THEN
        $this->assertEquals(401, $response->getStatusCode());
        $this->assertJson($response->baseResponse->getContent());
        $response->assertJsonStructure([
            'message',
        ]);
    }
}