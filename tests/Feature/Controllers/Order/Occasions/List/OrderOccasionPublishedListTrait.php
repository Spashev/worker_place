<?php

namespace Tests\Feature\Controllers\Order\Occasions\List;

use Illuminate\Testing\TestResponse;

trait OrderOccasionPublishedListTrait
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