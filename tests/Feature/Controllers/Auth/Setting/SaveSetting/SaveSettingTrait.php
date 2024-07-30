<?php

namespace Tests\Feature\Controllers\Auth\Setting\SaveSetting;

use Illuminate\Support\Str;

trait SaveSettingTrait
{
    public static function getWrongCredentials()
    {
        yield 'empty' => [
            'credentials' => [],
            'errors' => [
                "key" => [
                    "The key field is required."
                ],
                "value" => [
                    "The value field is required."
                ],
                "type" => [
                    "The type field is required."
                ],
            ],
        ];

        yield 'empty value' => [
            'credentials' => [
                ['key' => 'key one', 'value' => null, 'type' => 'string'],
            ],
            'errors' => [
                "value" => [
                    "The value field is required."
                ],
            ],
        ];

        yield 'empty key' => [
            'credentials' => [
                    ['key' => null, 'value' => 'value one', 'type' => 'string'],
            ],
            'errors' => [
                "key" => [
                    "The key field is required."
                ],
            ],
        ];

        yield 'long value' => [
            'credentials' => [
                'key' => 'key one', 'value' => Str::random(256), 'type' => 'string',
            ],
            'errors' => [
                "value" => [
                    "The value must not be greater than 255 characters."
                ],
            ],
        ];

        yield 'long key' => [
            'credentials' => [
                    'key' => Str::random(256), 'value' => 'value one', 'type' => 'string',
            ],
            'errors' => [
                "key" => [
                    "The key must not be greater than 255 characters."
                ],
            ],
        ];

        yield 'short key' => [
            'credentials' => [
                'key' => Str::random(1), 'value' => 'value one', 'type' => 'string',
            ],
            'errors' => [
                "key" => [
                    "The key must be at least 3 characters."
                ],
            ],
        ];

        yield 'wrong type' => [
            'credentials' => [
                'key' => 'qwe', 'value' => 'value one', 'type' => 'custom',
            ],
            'errors' => [
                "type" => [
                    "The selected type is invalid."
                ],
            ],
        ];

        yield 'type not match integer' => [
            'credentials' => [
                'key' => 'qwe', 'value' => 'one', 'type' => 'integer',
            ],
            'errors' => [
                "value" => [
                    "The value must be a valid integer."
                ],
            ],
        ];

        yield 'type not match boolean' => [
            'credentials' => [
                'key' => 'qwe', 'value' => 'not true', 'type' => 'boolean',
            ],
            'errors' => [
                "value" => [
                    "The value must be a valid boolean."
                ],
            ],
        ];

        yield 'type not match string' => [
            'credentials' => [
                'key' => 'qwe', 'value' => 5, 'type' => 'string',
            ],
            'errors' => [
                "value" => [
                    "The value must be a string.",
                    "The value must be a valid string."
                ],
            ],
        ];
    }
}