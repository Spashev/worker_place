<?php

namespace Tests\Feature\Controllers\Product\Substitution\SubstitutionSoft;

use Illuminate\Support\Str;
use Illuminate\Testing\TestResponse;

trait SaveSoftSubstitutionTrait
{
    public static function getWrongData()
    {
        yield 'empty' => [
            'credentials' => [],
            "errors" => [
                "type" => [
                    "The type field is required."
                ]
            ]
        ];
        yield 'type is long' => [
            'credentials' => [
                "type" => Str::random(11),
            ],
            "errors" => [
                "type" => [
                    "The type must not be greater than 10 characters."
                ],
            ]
        ];
        yield 'type is null' => [
            'credentials' => [
                "type" => null,
            ],
            "errors" => [
                "type" => [
                    "The type field is required."
                ],
            ]
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
}