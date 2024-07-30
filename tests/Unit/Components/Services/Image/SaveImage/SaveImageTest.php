<?php

namespace Tests\Unit\Components\Services\Image\SaveImage;

use Tests\TestCase;
use App\Jobs\StoreImage;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Storage;
use Bloomex\Common\Blca\Models\BlcaOrderHistoryImage;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\Components\Orders\Services\ImageService\ImageService;

class SaveImageTest extends TestCase
{
    use DatabaseTransactions;
    use SaveImageTrait;

    protected function setUp(): void
    {
        parent::setUp();
        $this->storage = config('image.disk');
    }

    /** @test */
    public function saveImage_responseIsOrderHistoryImage_success()
    {
        // GIVEN
        $user = $this->createUserAndBe();
        $warehouse = $this->createWarehouse();
        $this->attachUserWarehouse($user, $warehouse);
        $order = $this->createOrder();
        $orderHistory = $this->createOrderHistory(1, $order, $warehouse);

        Storage::fake($this->storage);
        Bus::fake();

        $dataProvider = $this->makeRequest();

        /** @var ImageService $tested_method */
        $tested_method = $this->app->make(ImageService::class);

        // WHEN
        $response = $tested_method->addHistoryImage($orderHistory->first(), $dataProvider);

        // THEN
        $this->assertInstanceOf(BlcaOrderHistoryImage::class, $response);
    }

    /** @test */
    public function saveImage_dbHasTwoRecords_success()
    {
        // GIVEN
        $user = $this->createUserAndBe();
        $warehouse = $this->createWarehouse();
        $this->attachUserWarehouse($user, $warehouse);
        $order = $this->createOrder();
        $orderHistory = $this->createOrderHistory(1, $order, $warehouse);

        Storage::fake($this->storage);
        Bus::fake();

        $dataProvider = $this->makeRequest();

        /** @var ImageService $tested_method */
        $tested_method = $this->app->make(ImageService::class);

        // WHEN
        $tested_method->addHistoryImage($orderHistory->first(), $dataProvider);

        // THEN
        $this->assertDatabaseCount(BlcaOrderHistoryImage::class, 1);
    }

    /** @test */
    public function saveImage_assertDispatchedStoreImage_success()
    {
        // GIVEN
        $user = $this->createUserAndBe();
        $warehouse = $this->createWarehouse();
        $this->attachUserWarehouse($user, $warehouse);
        $order = $this->createOrder();
        $orderHistory = $this->createOrderHistory(1, $order, $warehouse);

        Storage::fake($this->storage);
        Bus::fake();

        $dataProvider = $this->makeRequest();

        /** @var ImageService $tested_method */
        $tested_method = $this->app->make(ImageService::class);

        // WHEN
        $tested_method->addHistoryImage($orderHistory->first(), $dataProvider);

        // THEN
        Bus::assertDispatched(StoreImage::class);
    }
}