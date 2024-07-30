<?php

namespace Tests\Feature\Controllers\Product\Substitution\SubstitutionHard;

use Illuminate\Support\Str;
use Illuminate\Testing\TestResponse;

trait SaveHardSubstitutionTrait
{
    public static function getWrongData()
    {
        yield 'empty' => [
            'credentials' => [],
            "errors" => [
                "name" => [
                    "The name field is required."
                ],
                "quantity" => [
                    "The quantity field is required."
                ],
                "type" => [
                    "The type field is required."
                ]
            ]
        ];
        yield 'empty name' => [
            'credentials' => [
                "quantity" => 3,
                "type" => "major",
            ],
            "errors" => [
                "name" => [
                    "The name field is required."
                ],
            ]
        ];
        yield 'null name' => [
            'credentials' => [
                "name" => null,
                "quantity" => 3,
                "type" => "major",
            ],
            "errors" => [
                "name" => [
                    "The name field is required."
                ],
            ]
        ];
        yield 'long name' => [
            'credentials' => [
                "name" => Str::random(52),
                "quantity" => 3,
                "type" => "major",
            ],
            "errors" => [
                "name" => [
                    "The name must not be greater than 50 characters."
                ],
            ]
        ];
        yield 'quantity 0' => [
            'credentials' => [
                "name" => "Custom name",
                "quantity" => 0,
                "type" => "major",
            ],
            "errors" => [
                "quantity" => [
                    "The quantity must be at least 1."
                ],
            ]
        ];
        yield 'quantity null' => [
            'credentials' => [
                "name" => "Custom name",
                "quantity" => null,
                "type" => "major",
            ],
            "errors" => [
                "quantity" => [
                    "The quantity field is required."
                ],
            ]
        ];
        yield 'type is long' => [
            'credentials' => [
                "name" => "Custom name",
                "quantity" => 2,
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
                "name" => "Custom name",
                "quantity" => 2,
                "type" => null,
            ],
            "errors" => [
                "type" => [
                    "The type field is required."
                ],
            ]
        ];
        yield 'color is long' => [
            'credentials' => [
                "name" => "Custom name",
                "quantity" => 2,
                "type" => "major",
                "color" => Str::random(21),
            ],
            "errors" => [
                "color" => [
                    "The color must not be greater than 20 characters."
                ],
            ]
        ];
        yield 'color is integer' => [
            'credentials' => [
                "name" => "Custom name",
                "quantity" => 2,
                "type" => "major",
                "color" => 20,
            ],
            "errors" => [
                "color" => [
                    "The color must be a string."
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
            'id',
            'type',
            'ingredient_id',
            'ingredient_name',
            'ingredient_quantity'
        ]);
    }
}