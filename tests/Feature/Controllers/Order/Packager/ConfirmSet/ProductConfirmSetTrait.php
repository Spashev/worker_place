<?php

namespace Tests\Feature\Controllers\Order\Packager\ConfirmSet;

use Bloomex\Common\Blca\Models\BlcaOrder;
use Bloomex\Common\Blca\Models\BlcaWarehouse;
use Illuminate\Testing\TestResponse;

trait ProductConfirmSetTrait
{
    public static function getWrongOrderData(): array
    {
        return [
            'wrongOrdersId' => [
                ['order_id' => '123r321'],
            ],
            'notExistOrdersId' => [
                ['order_code' => 999999999],
            ],

        ];
    }

    /**
     * @param TestResponse $response
     *
     * @return void
     */
    protected function assertJsonStructure(TestResponse $response): void
    {
        $response->assertJsonStructure([
            'message',
        ]);
    }

    protected function createCustomOrder( string $statusCode, BlcaWarehouse $warehouse = null)
    {
        if (!isset($warehouse)) {
            $warehouse = $this->warehouse;
        }
        $this->order = BlcaOrder::factory()->state([
            'warehouse' => $warehouse->warehouse_code,
            'warehouse_id' => $this->warehouse->warehouse_id,
            'order_status' => $statusCode
        ])->create();

        return $this->order;
    }
}