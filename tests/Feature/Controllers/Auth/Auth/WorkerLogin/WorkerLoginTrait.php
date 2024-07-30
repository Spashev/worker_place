<?php

namespace Tests\Feature\Controllers\Auth\Auth\WorkerLogin;

trait WorkerLoginTrait
{
    public function createCredentials($password = 123456)
    {
        return [
            'password' => $password,
        ];
    }

    public static function wrongCredentials()
    {
        return [
            'email_is_not_email' => [
                [
                    'email' => 'not_email',
                    'password' => "Password1",
                ],
            ],
            'email_is_empty' => [
                [
                    'email' => '',
                    'password' => "Password1",
                ],
            ],
            'email_is_not_exists' => [
                [
                    'password' => "Password1",
                ],
            ],
            'email_is_not_null' => [
                [
                    'email' => null,
                    'password' => "Password1",
                ],
            ],
            'password_is_empty' => [
                [
                    'email' => 'email@email.com',
                    'password' => '',
                ],
            ],
            'password_is_null' => [
                [
                    'email' => 'email@email.com',
                    'password' => null,
                ],
            ],
            'password_is_not_exists' => [
                [
                    'email' => 'email@email.com',
                ],
            ],
        ];
    }

    /**
     * @param \Illuminate\Testing\TestResponse $response
     *
     * @return void
     */
    protected function assertJsonStructure(\Illuminate\Testing\TestResponse $response): void
    {
        $response->assertJsonStructure([
            'data' => [
                'id',
                'name',
                'token',
                'email',
                'roles'
            ]
        ]);
    }
}
