<?php

namespace Tests\Feature\Controllers\Order\Order\List;

use Illuminate\Testing\TestResponse;

trait UserOrdersListTrait
{
    public static function getOrderIdsString(): array
    {
        return [
            'wrongOrdersIds' => [
                ['id' => '123r321'],
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
            'data' => [
                '*' => [
                    'id',
                    'occasion',
                    'status',
                    'warehouse',
                    'total',
                    'created_at',
                    'updated_at',
                    'delivered_at',
                ],
            ],
            'links' => [
                'first',
                'last',
                'prev',
                'next',
            ],
            'meta' => [
                'current_page',
                'from',
                'last_page',
                'links' => [
                    '*' => [
                        'url',
                        'label',
                        'active'
                    ]
                ],
                'path',
                'per_page',
                'to',
                'total',
                'columns'
            ]
        ]);
    }
}