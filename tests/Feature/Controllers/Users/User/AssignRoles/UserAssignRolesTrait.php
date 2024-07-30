<?php

namespace Tests\Feature\Controllers\Users\User\AssignRoles;

trait UserAssignRolesTrait
{
    public static function getWrongCredentials()
    {
        yield 'empty' => [
            'credentials' => [],
            'errors' => [
                "roles" => [
                    "The roles field is required."
                ],
            ],
        ];

        yield 'empty_array' => [
            'credentials' => ['roles' => []],
            'errors' => [
                "roles" => [
                    "The roles field is required."
                ],
            ],
        ];

        yield 'wrong_role' => [
            'credentials' => ['roles' => ['Some role']],
            'errors' => [
                "roles.0" => [
                    "The selected roles.0 is invalid."
                ],
            ],
        ];

        yield 'role_int' => [
            'credentials' => ['roles' => [1]],
            'errors' => [
                "roles.0" => [
                    "The roles.0 must be a string."
                ],
            ],
        ];
    }
}