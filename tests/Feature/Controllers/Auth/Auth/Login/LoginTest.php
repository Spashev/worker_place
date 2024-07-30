<?php

namespace Tests\Feature\Controllers\Auth\Auth\Login;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use DatabaseTransactions;
    use LoginTrait;

    /**
     * @test
     */
    public function login_success_response200()
    {
        // GIVEN
        $credentials = $this->createCredentials();
        $this->createUser();

        // WHEN
        $response = $this->withHeaders(['Accept' => 'application/json'])
            ->json('post', route('user.login'), $credentials);

        // THEN
        $this->assertEquals(200, $response->getStatusCode());
    }

    /**
     * @test
     */
    public function login_success_checkJsonStructure()
    {
        // GIVEN
        $credentials = $this->createCredentials();
        $this->createUser();

        // WHEN
        $response = $this->withHeaders(['Accept' => 'application/json'])
            ->json('post', route('user.login'), $credentials);

        // THEN
        $this->assertJsonStructure($response);
    }

    /**
     * @feature Auth
     * @scenario Login
     * @case Failed login, no email given
     *
     * @dataProvider wrongCredentials
     *
     * @param array $credentials
     *
     * @test
     */
    public function login_failed_wrongCredentials(array $credentials)
    {
        // GIVEN
        $this->createUser();

        // WHEN
        $response = $this->withHeaders(['Accept' => 'application/json'])
            ->json('post', route('user.login'), $credentials);

        // THEN
        $this->assertEquals(422, $response->getStatusCode());
    }

    /**
     * @test
     */
    public function login_failed_wrongEndpoint()
    {
        // GIVEN
        $credentials = $this->createCredentials();
        $this->createUser();

        // WHEN
        $response = $this->withHeaders(['Accept' => 'application/json'])
            ->json('get', route('user.login'), $credentials);

        // THEN
        $this->assertEquals(405, $response->getStatusCode());
    }
}
