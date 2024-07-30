<?php

namespace Tests\Unit\Components\Services\Packager\IngredientsOrderQrService\ListIngredients;

use App\Components\Orders\Services\OccasionsService;
use App\Components\Orders\Services\OrderIngredientsServices\IngredientsOrderIdService;
use App\Components\Orders\Services\OrderIngredientsServices\IngredientsOrderQrService;
use Bloomex\Common\Blca\Models\BlcaOrderIngredientSubstitution;
use Bloomex\Common\Blca\Models\BlcaOrderItem;
use Bloomex\Common\Blca\Models\BlcaOrderItemIngredient;
use Bloomex\Common\Blca\Models\BlcaProduct;
use Bloomex\Common\Core\Enums\ProductTypes;
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
        $qr = $this->createOrderQr($order);
        $this->createOrderItems($order, 3);

        $credentials = $this->makeCredentials(['order_code' => $qr->token]);

        /** @var IngredientsOrderQrService $tested_method */
        $tested_method = $this->app->make(IngredientsOrderQrService::class);

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
        $qr = $this->createOrderQr($order);
        $this->createOrderItems($order, 3);

        $credentials = $this->makeCredentials(['order_code' => $qr->token]);


        /** @var IngredientsOrderQrService $tested_method */
        $tested_method = $this->app->make(IngredientsOrderQrService::class);

        // WHEN
        $response = $tested_method->listIngredients($credentials);

        // THEN
        $this->assertCount(3, $response);
        $this->assertDatabaseCount(BlcaOrderItem::class, 5);
    }

    /**
     * @test
     */
    public function list_dbHasThreeOrderItems_oneSubstitution()
    {
        // GIVEN
        $user = $this->createUserAndBe();
        $warehouse = $this->createWarehouse();
        $this->attachUserWarehouse($user, $warehouse);

        $order = $this->createOrder();
        $qr = $this->createOrderQr($order);
        $items = $this->createOrderItems($order, 3);
        $product = $items->first();
        $ingredient = $product->productIngredients->first();
        $ingredient->substitution_type = 'major';
        $ingredient->save();
        $this->createBlcaIngredientSubstitution($ingredient->order_ingredient_id, ProductTypes::Flower->value, 'random name', $ingredient->ingredient_quantity);

        $credentials = $this->makeCredentials(['order_code' => $qr->token]);

        /** @var IngredientsOrderQrService $tested_method */
        $tested_method = $this->app->make(IngredientsOrderQrService::class);

        // WHEN
        $response = $tested_method->listIngredients($credentials);

        // THEN
        $this->assertCount(3, $response);
        $this->assertDatabaseCount( BlcaOrderItem::class, 3);
        $this->assertInstanceOf(BlcaOrderIngredientSubstitution::class, $response->first()->productIngredients->first()->latestSubstitutionIngredient);
        $this->assertDatabaseCount(BlcaOrderIngredientSubstitution::class, 1);
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
        $qr = $this->createOrderQr($order);
        $this->createCustomOrderItem($order, $sku, $warehouse);
        $this->createProduct($sku);

        $credentials = $this->makeCredentials(['order_code' => $qr->token]);

        /** @var IngredientsOrderQrService $tested_method */
        $tested_method = $this->app->make(IngredientsOrderQrService::class);

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
        $qr = $this->createOrderQr($order);
        $this->createCustomOrderItem($order, $sku, $warehouse);
        $product = $this->createProduct($sku);

        $credentials = $this->makeCredentials(['order_code' => $qr->token]);

        /** @var IngredientsOrderQrService $tested_method */
        $tested_method = $this->app->make(IngredientsOrderQrService::class);

        // WHEN
        $response = $tested_method->listIngredients($credentials);

        // THEN
        $this->assertCount(1, $response);
        $this->assertInstanceOf( BlcaProduct::class, $response->first()->product);
        $this->assertEquals($product->product_full_image, $response->first()->product->product_full_image);
    }
}