<?php

namespace Tests\Feature\Controllers\Warehouses\UserWarehouseList;

use Illuminate\Testing\TestResponse;

trait UserWarehouseListTrait
{
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
                    'value',
                    'title',
                ]
            ]
        ]);
    }
}