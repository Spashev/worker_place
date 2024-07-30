<?php

namespace Tests\Unit\Components\Services\Packager\IngredientsOrderIdService\ListIngredients;

use App\Components\Orders\Services\OccasionsService;
use App\Components\Orders\Services\OrderIngredientsServices\IngredientsOrderIdService;
use App\Components\Orders\Services\OrderIngredientsServices\IngredientsOrderQrService;
use Bloomex\Common\Blca\Models\BlcaOrderItem;
use Bloomex\Common\Blca\Models\BlcaOrderItemIngredient;
use Bloomex\Common\Blca\Models\BlcaProduct;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class ProductIngredientListTest extends TestCase
{
    use DatabaseTransactions;
    use ProductIngredientListTrait;

    /**
     * @test
     */
    public function list_dbHasThreeRelationships()
    {
        // GIVEN
        $user = $this->createUserAndBe();
        $warehouse = $this->createWarehouse();
        $this->attachUserWarehouse($user, $warehouse);

        $order = $this->createOrder();
        $this->createOrderItems($order, 3);

        $credentials = $this->makeCredentials(['order_id' => $order->order_id]);

        /** @var IngredientsOrderIdService $tested_method */
        $tested_method = $this->app->make(IngredientsOrderIdService::class);

        // WHEN
        $response = $tested_method->listIngredients($credentials);

        // THEN
        $this->assertCount(3, $response);
    }

    /**
     * @test
     */
    public function list_dbHasThreeRelationships_twoNotRelated()
    {
        // GIVEN
        $user = $this->createUser();
        $warehouse = $this->createWarehouse();
        $this->attachUserWarehouse($user, $warehouse);
        $order = $this->createOrder();
        $this->createOrderItems($order, 2);

        $user = $this->createUserAndBe();
        $warehouse = $this->createWarehouse();
        $this->attachUserWarehouse($user, $warehouse);
        $order = $this->createOrder();
        $this->createOrderItems($order, 3);

        $credentials = $this->makeCredentials(['order_id' => $order->order_id]);

        /** @var IngredientsOrderIdService $tested_method */
        $tested_method = $this->app->make(IngredientsOrderIdService::class);

        // WHEN
        $response = $tested_method->listIngredients($credentials);

        // THEN
        $this->assertCount(3, $response);
        $this->assertDatabaseCount(BlcaOrderItem::class, 5);
    }

    /**
     * @test
     */
    public function list_dbHasOneOrderItem_oneProductRelated()
    {
        // GIVEN
        $user = $this->createUserAndBe();
        $warehouse = $this->createWarehouse();
        $this->attachUserWarehouse($user, $warehouse);

        $sku = 'TR-001';
        $order = $this->createOrder();
        $this->createCustomOrderItem($order, $sku, $warehouse);
        $this->createProduct($sku);

        $credentials = $this->makeCredentials(['order_id' => $order->order_id]);

        /** @var IngredientsOrderIdService $tested_method */
        $tested_method = $this->app->make(IngredientsOrderIdService::class);

        // WHEN
        $response = $tested_method->listIngredients($credentials);

        // THEN
        $this->assertCount(1, $response);
        $this->assertInstanceOf( BlcaProduct::class, $response->first()->product);
        $this->assertEquals($sku, $response->first()->product->product_sku);
    }

    /**
     * @test
     */
    public function list_dbHasOneOrderItem_sameUrlLinks()
    {
        // GIVEN
        $user = $this->createUserAndBe();
        $warehouse = $this->createWarehouse();
        $this->attachUserWarehouse($user, $warehouse);

        $sku = 'TR-001';
        $order = $this->createOrder();
        $this->createCustomOrderItem($order, $sku, $warehouse);
        $product = $this->createProduct($sku);

        $credentials = $this->makeCredentials(['order_id' => $order->order_id]);

        /** @var IngredientsOrderIdService $tested_method */
        $tested_method = $this->app->make(IngredientsOrderIdService::class);

        // WHEN
        $response = $tested_method->listIngredients($credentials);

        // THEN
        $this->assertCount(1, $response);
        $this->assertInstanceOf( BlcaProduct::class, $response->first()->product);
        $this->assertEquals($product->product_full_image, $response->first()->product->product_full_image);
    }
}