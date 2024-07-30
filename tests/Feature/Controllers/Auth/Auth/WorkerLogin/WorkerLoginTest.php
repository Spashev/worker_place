<?php

namespace Tests\Feature\Controllers\Auth\Auth\WorkerLogin;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class WorkerLoginTest extends TestCase
{
    use DatabaseTransactions;
    use WorkerLoginTrait;

    /**
     * @test
     */
    public function login_success_response200()
    {
        // GIVEN
        $credentials = $this->createCredentials();
        $user = $this->createUserWithAccessCode();
        $roleWorker = Role::query()->updateOrCreate(['id' => 1], ['name' => 'Worker', 'guard_name' => 'api']);
        $roleAdmin = Role::query()->updateOrCreate(['id' => 2], ['name' => 'Admin', 'guard_name' => 'api']);
        $user->assignRole($roleWorker);
        $user->assignRole($roleAdmin);

        // WHEN
        $response = $this->withHeaders(['Accept' => 'application/json'])
            ->json('post', route('user.login.worker'), $credentials);

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
        $user = $this->createUserWithAccessCode();
        $role = Role::query()->updateOrCreate(['id' => 1], ['name' => 'worker', 'guard_name' => 'api']);
        $user->assignRole($role);

        // WHEN
        $response = $this->withHeaders(['Accept' => 'application/json'])
            ->json('post', route('user.login.worker'), $credentials);

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
        $this->createUserWithAccessCode();

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
        $this->createUserWithAccessCode();

        // WHEN
        $response = $this->withHeaders(['Accept' => 'application/json'])
            ->json('get', route('user.login'), $credentials);

        // THEN
        $this->assertEquals(405, $response->getStatusCode());
    }
}
