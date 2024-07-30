<?php

namespace Tests\Feature\Controllers\Users\User\Update;

trait UserUpdateTrait
{
    public static function getWrongCredentials()
    {
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
}