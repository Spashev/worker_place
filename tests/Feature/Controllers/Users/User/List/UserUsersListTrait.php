<?php

namespace Tests\Feature\Controllers\Users\User\List;

use Illuminate\Testing\TestResponse;

trait UserUsersListTrait
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
                    'created_at',
                    'last_visit_at',
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