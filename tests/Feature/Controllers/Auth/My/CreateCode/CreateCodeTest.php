<?php

namespace Tests\Feature\Controllers\Auth\My\CreateCode;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class CreateCodeTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * @test
     */
    public function success_manager_response201()
    {
        // GIVEN
        $user = $this->createUserAndBe();
        $managerRole = $this->createRole('Warehouse manager');
        $user->assignRole($managerRole);

        // WHEN
        $response = $this->withHeaders(['Accept' => 'application/json'])
            ->json('post', route('my.createCode'));

        // THEN
        $this->assertEquals(201, $response->getStatusCode());
        $response->assertJsonStructure([
            'access_code',
        ]);
    }

    /**
     * @test
     */
    public function success_packager_response201()
    {
        // GIVEN
        $user = $this->createUserAndBe();
        $packagerRole = $this->createRole('Packer');
        $user->assignRole($packagerRole);

        // WHEN
        $response = $this->withHeaders(['Accept' => 'application/json'])
            ->json('post', route('my.createCode'));

        // THEN
        $this->assertEquals(201, $response->getStatusCode());
        $response->assertJsonStructure([
            'access_code',
        ]);
    }

    /**
     * @test
     */
    public function success_twoRoles_response201()
    {
        // GIVEN
        $user = $this->createUserAndBe();
        $managerRole = $this->createRole('Warehouse manager');
        $packagerRole = $this->createRole('Packer');
        $user->assignRole($managerRole);
        $user->assignRole($packagerRole);

        // WHEN
        $response = $this->withHeaders(['Accept' => 'application/json'])
            ->json('post', route('my.createCode'));

        // THEN
        $this->assertEquals(201, $response->getStatusCode());
        $response->assertJsonStructure([
            'access_code',
        ]);
    }

    /**
     * @test
     */
    public function failed_notLogin_response401()
    {
        // GIVEN
        $user = $this->createUser();
        $managerRole = $this->createRole('Warehouse manager');
        $packagerRole = $this->createRole('Packer');
        $user->assignRole($managerRole);
        $user->assignRole($packagerRole);

        // WHEN
        $response = $this->withHeaders(['Accept' => 'application/json'])
            ->json('post', route('my.createCode'));

        // THEN
        $this->assertEquals(401, $response->getStatusCode());
        $response->assertJsonStructure([
            'message',
        ]);
    }
}