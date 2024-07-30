<?php

namespace Tests\Feature\Controllers\Order\Order\Show;

use Bloomex\Common\Blca\Models\BlcaWarehouse;
use Bloomex\Common\Blca\Models\BlcaWarehouseInfo;
use Illuminate\Support\Arr;

trait UserOrderTrait
{
    /**
     * @param \Illuminate\Testing\TestResponse $response
     *
     * @return void
     */
    protected function assertStructureJson(\Illuminate\Testing\TestResponse $response): void
    {
        $response->assertJsonStructure([
            'id',
            'order' => [
                'id',
                'coupon_code',
                'coupon_type',
                'coupon_value',
                'currency',
                'status',
                'occasion',
                'note',
                'comments',
                'total',
                'subtotal',
                'tax',
                'tax_rate',
                'shipping',
                'shipping_tax',
                'discount',
                'ip_address',
                'lang',
                'updater',
                'created_at',
                'updated_at',
                'delivered_at',
            ],
            'billing' => [
                'id',
                'order_id',
                'last_name',
                'first_name',
                'country',
                'city',
                'state',
                'street_name',
                'street_number',
                'suite',
                'zip',
                'company',
                'phone',
                'email',
                'address_form',
                'address_type',
            ],
            'shipping' => [
                'id',
                'order_id',
                'last_name',
                'first_name',
                'country',
                'city',
                'state',
                'street_name',
                'street_number',
                'suite',
                'zip',
                'company',
                'phone',
                'email',
                'address_form',
                'address_type',
            ],
            'history' =>
                ['*' =>
                    [
                        'id',
                        'created_at',
                        'status',
                        'creator',
                        'comment',
                    ]
                ]
        ]);
    }
}