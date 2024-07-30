<?php

namespace Tests\Feature\Controllers\Users\User\Create;

trait UserCreateTrait
{
    public static function getWrongCredentials()
    {
        yield 'empty' => [
            'credentials' => [],
            'errors' => [
                "email" => [
                    "The email field is required."
                ],
                "roles" => [
                    "The roles field is required."
                ],
                "warehouses" => [
                    "The warehouses field is required."
                ]
            ],
        ];
        yield 'empty_email' => [
            'credentials' => ['roles' => ['Admin'], 'warehouses' => [1]],
            'errors' => [
                "email" => [
                    "The email field is required."
                ],
            ],
        ];
        yield 'empty_warehouses' => [
            'credentials' => ['email' => 'some@bloomex.ca', 'roles' => ['Admin 2']],
            'errors' => [
                "roles.0" => [
                    "The selected roles.0 is invalid."
                ],
                "warehouses" => [
                    "The warehouses field is required."
                ],
            ],
        ];
        yield 'empty_roles' => [
            'credentials' => ['email' => 'some@bloomex.ca', 'warehouses' => [1]],
            'errors' => [
                "warehouses.0" => [
                    "The selected warehouses.0 is invalid."
                ],
                "roles" => [
                    "The roles field is required."
                ],
            ],
        ];
    }
    public static function getWrongCredentialsCorrectData()
    {
        yield 'empty' => [
            'credentials' => [],
            'errors' => [
                "email" => [
                    "The email field is required."
                ],
                "roles" => [
                    "The roles field is required."
                ],
                "warehouses" => [
                    "The warehouses field is required."
                ]
            ],
        ];
    }
}