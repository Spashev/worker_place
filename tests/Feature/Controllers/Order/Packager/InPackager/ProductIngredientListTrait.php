<?php

namespace Tests\Feature\Controllers\Order\Packager\InPackager;

use Bloomex\Common\Blca\Models\BlcaOrder;
use Bloomex\Common\Blca\Models\BlcaOrderStatus;
use Bloomex\Common\Blca\Models\BlcaWarehouse;
use Illuminate\Testing\TestResponse;

trait ProductIngredientListTrait
{
    public static function getWrongOrderData(): array
    {
        return [
            'wrongOrdersId' => [
                ['order_id' => '123r321'],
            ],
            'wrongOrdersCode' => [
                ['order_code' => 123321],
            ],
            'notExistOrdersCode' => [
                ['order_code' => 'c6a3aa763432a51ad45351060c0cc078'],
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
            'order_id',
            'history_id',
            'products' => [
                '*' => [
                    'order_id',
                    'item_id',
                    'quantity',
                    'name',
                    'sku',
                    'substitution_type',
                    'image_full',
                    'image_thumb',
                    'ingredients' => [
                        '*' => [
                            'ingredient_id',
                            'item_id',
                            'name',
                            'quantity',
                            'substitution_type'
                        ]
                    ],
                ]
            ]
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