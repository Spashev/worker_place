<?php

namespace Tests\Feature\Controllers\Users\User\Exists;

use Illuminate\Testing\TestResponse;

trait UserExistTrait
{
    /**
     * @param TestResponse $response
     *
     * @return void
     */
    protected function assertJsonStructureNotExist(TestResponse $response): void
    {
        $response->assertJsonStructure([
            'exist'
        ]);
    }

    /**
     * @param TestResponse $response
     *
     * @return void
     */
    protected function assertJsonStructureExist(TestResponse $response): void
    {
        $response->assertJsonStructure([
            'data' => [
                'id',
                'email',
                'warehouses' => [
                    '*' => [
                        'warehouse_id',
                        'warehouse_name',
                    ]
                ],
                'roles' => [],
            ]
        ]);
    }
}