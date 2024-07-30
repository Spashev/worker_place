<?php

namespace Tests\Feature\Controllers\Users\User\AssignWarehouses;

trait UserAssignWarehousesTrait
{
    public static function getWrongCredentials()
    {
        yield 'empty' => [
            'credentials' => [],
            'errors' => [
                "warehouses" => [
                    "The warehouses field is required."
                ],
            ],
        ];

        yield 'empty_array' => [
            'credentials' => ['warehouses' => []],
            'errors' => [
                "warehouses" => [
                    "The warehouses field is required."
                ],
            ],
        ];

        yield 'wrong_role' => [
            'credentials' => ['warehouses' => [-48]],
            'errors' => [
                "warehouses.0" => [
                    "The selected warehouses.0 is invalid."
                ],
            ],
        ];

        yield 'role_string' => [
            'credentials' => ['warehouses' => ['Test']],
            'errors' => [
                "warehouses.0" => [
                    "The warehouses.0 must be a number."
                ],
            ],
        ];
    }
}