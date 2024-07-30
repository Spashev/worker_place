<?php

namespace Tests\Unit\Components\Services\Order\Show;

use Bloomex\Common\Blca\Models\BlcaWarehouse;
use Bloomex\Common\Blca\Models\BlcaWarehouseInfo;
use Illuminate\Support\Arr;

trait UserOrderTrait
{
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
}