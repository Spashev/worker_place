<?php

namespace Tests\Feature\Controllers\Auth\Setting\ListSettings;

use Illuminate\Testing\TestResponse;

trait ListSettingsTrait
{
    /**
     * @param TestResponse $response
     *
     * @return void
     */
    protected function assertJsonStructureExist(TestResponse $response): void
    {
        $response->assertJsonStructure([
            '*' => [
                'id',
                'key',
                'value',
                'author',
                'updated_at',
            ],
        ]);
    }
}