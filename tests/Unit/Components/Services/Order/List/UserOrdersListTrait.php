<?php

namespace Tests\Unit\Components\Services\Order\List;

use App\Http\Requests\Order\OrderListRequest;
use Bloomex\Common\Blca\Models\BlcaOrder;
use Bloomex\Common\Blca\Models\BlcaOrderOccasion;
use Bloomex\Common\Blca\Models\BlcaOrderStatus;
use Bloomex\Common\Blca\Models\BlcaOrderUserInfo;
use Bloomex\Common\Blca\Models\BlcaWarehouse;
use Bloomex\Common\Blca\Models\BlcaWarehouseInfo;
use Bloomex\Common\Core\Enums\OrderOccasion;
use Bloomex\Common\Core\Enums\OrderStatus;
use Carbon\Carbon;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

trait UserOrdersListTrait
{
    public function makeCredentials($data): OrderListRequest
    {
        $request = new OrderListRequest($data);
        $request
            ->setContainer(app())
            ->validateResolved();

        return $request;
    }

    protected function createCustomOrder(
        BlcaWarehouse $warehouse = null,
                      $customer_occasion = null,
                      $order_status = null,
                      $delivery_date = null,
                      $created_date = null,
                      $updated_date = null
    ) {
        if(!isset($warehouse)){
            $warehouse = $this->warehouse;
        }
        $occasion = $this->createOccasion($customer_occasion);
        $status = $this->createStatus($order_status);

        if(!isset($updated_date)){
            $updated_date = $this->createRandomDateBefore2000('Y-m-d H:i:s');
        }

        if(!isset($created_date)){
            $created_date = $this->createRandomDateBefore2000('Y-m-d H:i:s');
        }

        if(!isset($delivery_date)){
            $delivery_date = $this->createRandomDateBefore2000('Y-m-d');
        }

        $this->order = BlcaOrder::factory()->state([
            'customer_occasion' => $occasion->order_occasion_code,
            'order_status' => $status->order_status_code,
            'warehouse' => $warehouse->warehouse_code,
            'warehouse_id' => $warehouse->warehouse_id,
            'ddate' => $delivery_date,
            'cdate' => strtotime($created_date),
            'mdate' => strtotime($updated_date),
        ])->create();

        return $this->order;
    }

    protected function createCustomWarehouse($name = null, $code = null){
        if(!isset($name)){
            $name = Arr::random(['Ottawa', 'Toronto', 'Montreal', 'Auburn', 'RedDeer', 'Edmonton', 'Calgary']);
        }

        if(!isset($code)){
            $code = Arr::random(['WH01', 'WH02', 'WH03', 'bcz'. 'WH05', 'WH08', 'WH09']);
        }
        $this->warehouse = BlcaWarehouse::factory()->state([
            'warehouse_code' => $code,
            'warehouse_name' => $name
        ])->create();
        $this->warehouseInfo = BlcaWarehouseInfo::factory()->state([
            'warehouse_id' => $this->warehouse->warehouse_id
        ])->create();

        return $this->warehouse;
    }

    /**
     * @param null|string $customer_occasion
     * @return BlcaOrderOccasion
     */
    public function createOccasion(?string $customer_occasion): BlcaOrderOccasion
    {
        $existBlcaOrderOccasion = BlcaOrderOccasion::where('order_occasion_name', $customer_occasion)->first();
        if ($customer_occasion && OrderOccasion::existByValue($customer_occasion) && !$existBlcaOrderOccasion) {
            $enumOccasion = OrderOccasion::getByValue($customer_occasion);
            $occasion = BlcaOrderOccasion::factory()->state([
                'order_occasion_code' => $enumOccasion->name,
                'order_occasion_name' => $enumOccasion->value
            ])->create();
        } else if ($existBlcaOrderOccasion) {
            return $existBlcaOrderOccasion;
        } else {
            $occasion = BlcaOrderOccasion::factory()->create();
        }
        return $occasion;
    }

    /**
     * @param null|string $order_status
     * @return BlcaOrderStatus
     */
    public function createStatus(?string $order_status): BlcaOrderStatus
    {
        $existBlcaOrderStatus = BlcaOrderStatus::where('order_status_code', $order_status)->first();
        if ($order_status && OrderStatus::getEnumByValue($order_status) && !$existBlcaOrderStatus) {
            $enumStatus = OrderStatus::getEnumByValue($order_status);
            $status = BlcaOrderStatus::factory()->state([
                'order_status_code' => $enumStatus->value,
                'order_status_name' => $enumStatus->name,
            ])->create();
        } else if ($existBlcaOrderStatus) {
            return $existBlcaOrderStatus;
        } else {
            $status = BlcaOrderStatus::factory()->create();
        }
        return $status;
    }

    public function createRandomDateBefore2000($format): string
    {
        $startDate = Carbon::createFromDate(1970, 1, 1);
        $endDate = Carbon::createFromDate(2000, 1, 1);
        $randomDate = Carbon::createFromTimestamp(mt_rand($startDate->timestamp, $endDate->timestamp));
        return $randomDate->format($format);
    }

    public function createOrderCustomUserShippingZip(string $zip, $warehouse)
    {
        if (!isset($warehouse)) {
            $warehouse = $this->warehouse;
        }
        $this->order = BlcaOrder::factory()->state([
            'warehouse' => $warehouse->warehouse_code
        ])->create();

        $this->orderUserBilling = $this->createBlcaOrderUserInfoCustom(address_type: "BT");
        $this->orderUserShipping = $this->createBlcaOrderUserInfoCustom(address_type: "ST", zip: $zip);

        return $this->order;
    }

    protected function createBlcaOrderUserInfoCustom($address_type = 'BT', $zip = 'R1R1R1')
    {
        $this->orderUserInfo = BlcaOrderUserInfo::factory()->state([
            'order_id' => $this->order->order_id,
            'user_id' => $this->user->id,
            'address_type' => $address_type,
            'zip' => $zip
        ])->create();

        return $this->orderUserInfo;
    }
}