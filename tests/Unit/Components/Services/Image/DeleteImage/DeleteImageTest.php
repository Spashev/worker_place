<?php

namespace Tests\Unit\Components\Services\Image\DeleteImage;

use App\Components\Orders\Services\ImageService\ImageService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class DeleteImageTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function removeImage_true()
    {
        // GIVEN
        $user = $this->createUser();
        $warehouse = $this->createWarehouse();
        $this->attachUserWarehouse($user, $warehouse);
        $order = $this->createOrder();
        $orderHistories = $this->createOrderHistory(1, $order, $warehouse);
        $images = $this->createOrderHistoryImage($orderHistories->first());
        $image = $images->first();

        /** @var ImageService $tested_method */
        $tested_method = $this->app->make(ImageService::class);

        // WHEN
        $response = $tested_method->deleteHistoryImage($image);

        // THEN
        $this->assertTrue($response);
    }
}