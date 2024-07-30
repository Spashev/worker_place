<?php

namespace Tests\Feature\Controllers\Auth\Setting\LogoutAll;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class LogoutAllTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * @test
     */
    public function logoutAllUsers_success_admin_response200()
    {
        // GIVEN
        $this->createUserAndBe();
        $this->createUserAndBe();
        $user = $this->createUserAndBe();
        $adminRole = $this->createRole('Admin');
        $permission = Permission::create(['name' => 'settings.create', 'guard_name' => 'api']);
        $user->givePermissionTo($permission);
        $user->assignRole($adminRole);

        // WHEN
        $response = $this->withHeaders(['Accept' => 'application/json'])
            ->json('post', route('settings.logout.all'));

        // THEN
        $this->assertEquals(200, $response->getStatusCode());
    }

    /**
     * @test
     */
    public function logoutAllUsers_success_admin_responseStructure()
    {
        // GIVEN
        $user = $this->createUserAndBe();
        $adminRole = $this->createRole('Admin');
        $permission = Permission::create(['name' => 'settings.create', 'guard_name' => 'api']);
        $user->givePermissionTo($permission);
        $user->assignRole($adminRole);

        // WHEN
        $response = $this->withHeaders(['Accept' => 'application/json'])
            ->json('post', route('settings.logout.all'));

        // THEN
        $response->assertJsonStructure([
            'message',
        ]);
    }

    /**
     * @test
     */
    public function logoutAllUsers_success_super_admin_response200()
    {
        // GIVEN
        $this->createUserAndBe();
        $this->createUserAndBe();
        $user = $this->createUserAndBe();
        $adminRole = $this->createRole('Super Admin');
        $permission = Permission::create(['name' => 'settings.*', 'guard_name' => 'api']);
        $user->givePermissionTo($permission);
        $user->assignRole($adminRole);

        // WHEN
        $response = $this->withHeaders(['Accept' => 'application/json'])
            ->json('post', route('settings.logout.all'));

        // THEN
        $this->assertEquals(200, $response->getStatusCode());
    }

    /**
     * @test
     */
    public function logoutAllUsers_filedAccess_response403()
    {
        // GIVEN
        $user = $this->createUserAndBe();
        $manager = $this->createRole('Warehouse manager');
        Permission::create(['name' => 'settings.create', 'guard_name' => 'api']);
        $user->assignRole($manager);


        // WHEN
        $response = $this->withHeaders(['Accept' => 'application/json'])
            ->json('post', route('settings.logout.all'));

        // THEN
        $this->assertEquals(403, $response->getStatusCode());
    }
}