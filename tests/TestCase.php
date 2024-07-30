<?php

namespace Tests;

use App\Models\User;
use Bloomex\Common\Blca\Models\BlcaDriverRate;
use Bloomex\Common\Blca\Models\BlcaDriverRatesPostalcodes;
use Bloomex\Common\Blca\Models\BlcaOrder;
use Bloomex\Common\Blca\Models\BlcaOrderHistory;
use Bloomex\Common\Blca\Models\BlcaOrderHistoryImage;
use Bloomex\Common\Blca\Models\BlcaOrderIngredientSubstitution;
use Bloomex\Common\Blca\Models\BlcaOrderItem;
use Bloomex\Common\Blca\Models\BlcaOrderItemIngredient;
use Bloomex\Common\Blca\Models\BlcaOrderOccasion;
use Bloomex\Common\Blca\Models\BlcaOrderQr;
use Bloomex\Common\Blca\Models\BlcaOrderStatus;
use Bloomex\Common\Blca\Models\BlcaOrderUserInfo;
use Bloomex\Common\Blca\Models\BlcaProduct;
use Bloomex\Common\Blca\Models\BlcaProductIngredientOptions;
use Bloomex\Common\Blca\Models\BlcaSetting;
use Bloomex\Common\Blca\Models\BlcaSubstitutionColor;
use Bloomex\Common\Blca\Models\BlcaSubstitutionItem;
use Bloomex\Common\Blca\Models\BlcaUser;
use Bloomex\Common\Blca\Models\BlcaWarehouse;
use Bloomex\Common\Blca\Models\BlcaWarehouseInfo;
use Faker\Provider\en_CA\Address;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Collection;
use Laravel\Sanctum\Sanctum;
use Spatie\Permission\Models\Role;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    protected User $user;
    protected BlcaOrder $order;
    protected BlcaWarehouse $warehouse;
    protected $warehouseInfo;
    protected $orderHistory;
    protected $orderItems;
    protected $orderQr;
    protected $role;
    protected Collection $images;
    protected BlcaOrderOccasion $orderOccasion;
    protected BlcaOrderStatus $orderStatus;
    protected BlcaOrderUserInfo $orderUserInfo;
    protected BlcaOrderUserInfo $orderUserBilling;
    protected BlcaOrderUserInfo $orderUserShipping;
    protected BlcaDriverRatesPostalcodes $driverRatePostalcode;
    protected BlcaDriverRate $driverRate;
    protected BlcaProduct $product;
    protected $settings;

    public function createUser($email = 'email@example.com')
    {
        $this->user = User::factory()->state([
            'email' => $email,
            'name' => 'John'
        ])->create();

        return $this->user;
    }

    public function createUserWithAccessCode($email = 'email@example.com')
    {
        $this->user = User::factory()->state([
            'email' => $email,
            'name' => 'John',
            'access_code' => md5('123456')
        ])->create();

        return $this->user;
    }

    protected function createUserAndBe($email = 'admin@example.com')
    {
        $this->user = $this->createUser($email);
        Sanctum::actingAs($this->user);

        return $this->user;
    }

    protected function createRole($nameRole = 'Admin')
    {
        $this->role = Role::query()->updateOrCreate(['id' => 1], ['name' => $nameRole, 'guard_name' => 'api']);
        return $this->role;
    }

    protected function createWarehouse()
    {
        $this->warehouse = BlcaWarehouse::factory()->create();
        $this->warehouseInfo = BlcaWarehouseInfo::factory()->state([
            'warehouse_id' => $this->warehouse->warehouse_id
        ])->create();

        return $this->warehouse;
    }

    protected function createOrder(BlcaWarehouse $warehouse = null, $createBt = true, $createSt = true, $deliveryZip = null)
    {
        if (!isset($warehouse)) {
            $warehouse = $this->warehouse;
        }
        $this->order = BlcaOrder::factory()->state([
            'warehouse' => $warehouse->warehouse_code,
            'warehouse_id' => $this->warehouse->warehouse_id
        ])->create();
        if ($createBt) {
            $this->orderUserBilling = $this->createBlcaOrderUserInfo(address_type: "BT");
        }
        if ($createSt) {
            $this->orderUserShipping = $this->createBlcaOrderUserInfo(address_type: "ST", zip: $deliveryZip);
        }
        if ($deliveryZip) {
            $this->createDriverRatePostalcode($deliveryZip);
            $this->order->postalcode_id = $this->driverRatePostalcode->id;
            $this->order->save();
        }

        return $this->order;
    }

    protected function createDriverRatePostalcode(string $zip): BlcaDriverRatesPostalcodes
    {
        $this->driverRate = BlcaDriverRate::factory()->state([
            'warehouse_id' => $this->warehouse->warehouse_id,
        ])->create();

        $this->driverRatePostalcode = BlcaDriverRatesPostalcodes::factory()->state([
            'postalcode' => $zip,
            'id_rate' =>  $this->driverRate->id_rate
        ])->create();

        return $this->driverRatePostalcode;
    }

    protected function createOrderHistory($quantity = 1, BlcaOrder $order = null, BlcaWarehouse $warehouse = null)
    {
        if (!isset($warehouse)) {
            $warehouse = $this->warehouse;
        }
        if (!isset($order)) {
            $order = $this->order;
        }
        $this->orderHistory = BlcaOrderHistory::factory($quantity)->state([
            'order_id' => $order->order_id,
            'warehouse' => $warehouse->warehouse_code,
        ])->create();

        return $this->orderHistory;
    }

    protected function createOrderHistoryImage(BlcaOrderHistory $orderHistory, $quantity = 1): Collection
    {
        $this->images = BlcaOrderHistoryImage::factory($quantity)->state([
            'history_id' => $orderHistory->order_status_history_id,
        ])->create();

        return $this->images;
    }


    protected function attachUserWarehouse(BlcaUser $user, BlcaWarehouse $warehouse): void
    {
        $user->warehouses()->attach($warehouse->warehouse_id);
    }

    protected function createBlcaOrderUserInfo($address_type = 'BT', $zip = null)
    {
        if(!$zip){
            $zip = Address::postcode();
        }
        $this->orderUserInfo = BlcaOrderUserInfo::factory()->state([
            'order_id' => $this->order->order_id,
            'user_id' => $this->user->id,
            'address_type' => $address_type,
            'zip' => $zip
        ])->create();

        return $this->orderUserInfo;
    }

    protected function createBlcaOrderOccasion($code = null, $name = null, $published = 1)
    {
        $this->orderOccasion = BlcaOrderOccasion::factory()->state([
            'order_occasion_code' => $code,
            'order_occasion_name' => $name,
            'published' => $published
        ])->create();

        return $this->orderOccasion;
    }

    protected function createBlcaOrderStatus($code = null, $name = null, $publish = '1')
    {
        $this->orderStatus = BlcaOrderStatus::factory()->state([
            'order_status_code' => $code,
            'order_status_name' => $name,
            'publish' => $publish
        ])->create();

        return $this->orderStatus;
    }

    protected function createOrderQr(BlcaOrder $order)
    {
        $this->orderQr = BlcaOrderQr::factory()->state([
            'order_id' => $order->order_id,
        ])->create();

        return $this->orderQr;
    }

    protected function createOrderItems(BlcaOrder $order, $quantityItems = 1, BlcaWarehouse $warehouse = null)
    {
        if (!isset($warehouse)) {
            $warehouse = $this->warehouse;
        }
        $this->orderItems = BlcaOrderItem::factory($quantityItems)->state([
            'order_id' => $order->order_id,
            'warehouse' => $warehouse->warehouse_code,
        ])->create();
        /** @var BlcaOrderItem $orderItem */
        foreach ($this->orderItems as $orderItem) {
            $quantityItems = $orderItem->product_quantity;
            $this->createOrderItemsIngredients($order->order_id, $orderItem->order_item_id, $quantityItems);
        }

        return $this->orderItems;
    }

    protected function createProduct(string $productSku)
    {
        $this->product = BlcaProduct::factory()->state([
            'product_sku' => $productSku,
        ])->create();

        return $this->product;
    }

    protected function createOrderItemsIngredients(int $orderId, int $itemId, int $quantityItems)
    {
        for ($i = 0; $i < $quantityItems; $i++) {
            BlcaOrderItemIngredient::factory()->state([
                'order_id' => $orderId,
                'order_item_id' => $itemId,
                'ingredient_quantity' => rand(1, 10) * $quantityItems
            ])->create();
        }
    }

    // object how do we know TYPE Substitution (FLOWERS)
    protected function createProductIngredientOption(string $name = null, string $type = null)
    {
        $state = [];
        if (isset($name)) {
            $state['igo_product_name'] = $name;
        }
        if (isset($type)) {
            $state['type'] = $type;
        }

        return BlcaProductIngredientOptions::factory()->state($state)->create();;
    }

    // item related to Ingredient Substitution
    protected function createBlcaIngredientSubstitution($ingredient_id, string $type = null, string $name = null, string $quantity = null)
    {
        $state = [];
        $state['ingredient_id'] = $ingredient_id;
        if (isset($type)) {
            $state['type'] = $type;
        }
        if (isset($name)) {
            $state['ingredient_name'] = $name;
        }
        if (isset($quantity)) {
            $state['ingredient_quantity'] = $quantity;
        }
        return BlcaOrderIngredientSubstitution::factory()->state($state)->create();
    }

    // list items for Substitution
    protected function createBlcaSubstitutionItem(string $name = null, string $type = null)
    {
        $state = [];
        if (isset($name)) {
            $state['name'] = $name;
        }
        if (isset($type)) {
            $state['type'] = $type;
        }
        return BlcaSubstitutionItem::factory()->state($state)->create();
    }

    // list colors for Substitution
    protected function createBlcaSubstitutionColor(string $name = null, string $hex = null)
    {
        $state = [];
        if (isset($name)) {
            $state['name'] = $name;
        }
        if (isset($hex)) {
            $state['hex'] = $hex;
        }
        return BlcaSubstitutionColor::factory()->state($state)->create();
    }

    protected function createSettings(User $user, $type, $value, $quantity = 1): Collection
    {
        $this->settings = BlcaSetting::factory($quantity)->state([
            'type' => $type,
            'value' => $value,
            'updated_by' => $user->id,
        ])->create();

        return $this->settings;
    }
}
