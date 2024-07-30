<?php

namespace Tests\Feature\Controllers\Users\User\Exists;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class UserExistsTest extends TestCase
{
    use DatabaseTransactions;
    use UserExistTrait;

    /**
     * @test
     */
    public function exist_success_response200()
    {
        // GIVEN
        $user = $this->createUserAndBe();
        $adminRole = $this->createRole('Admin');
        $permission = Permission::create(['name' => 'users.view', 'guard_name' => 'api']);
        $user->givePermissionTo($permission);
        $user->assignRole($adminRole);
        $warehouse = $this->createWarehouse();
        $this->attachUserWarehouse($user, $warehouse);

        $data = [
            'email' => 'new-user@bloomex.ca',
        ];

        // WHEN
        $response = $this->withHeaders(['Accept' => 'application/json'])
            ->json('get', route('user.user.exist', $data));

        // THEN
        $this->assertEquals(200, $response->getStatusCode());
    }

    /**
     * @test
     */
    public function exist_success_checkStructure()
    {
        // GIVEN
        $user = $this->createUserAndBe();
        $adminRole = $this->createRole('Admin');
        $permission = Permission::create(['name' => 'users.view', 'guard_name' => 'api']);
        $user->givePermissionTo($permission);
        $user->assignRole($adminRole);
        $warehouse = $this->createWarehouse();
        $this->attachUserWarehouse($user, $warehouse);

        $data = [
            'email' => 'new-user@bloomex.ca',
        ];

        // WHEN
        $response = $this->withHeaders(['Accept' => 'application/json'])
            ->json('get', route('user.user.exist', $data));

        // THEN
        $this->assertJsonStructureNotExist($response);
    }

    /**
     * @test
     */
    public function exist_success_modelExist_response201()
    {
        // GIVEN
        $user = $this->createUserAndBe('new-user@bloomex.ca');
        $adminRole = $this->createRole('Admin');
        $permission = Permission::create(['name' => 'users.view', 'guard_name' => 'api']);
        $user->givePermissionTo($permission);
        $user->assignRole($adminRole);
        $warehouse = $this->createWarehouse();
        $this->attachUserWarehouse($user, $warehouse);

        $data = [
            'email' => 'new-user@bloomex.ca',
        ];

        // WHEN
        $response = $this->withHeaders(['Accept' => 'application/json'])
            ->json('get', route('user.user.exist', $data));

        // THEN
        $this->assertEquals(201, $response->getStatusCode());
    }

    /**
     * @test
     */
    public function exist_success_modelExist_checkStructure()
    {
        // GIVEN
        $user = $this->createUserAndBe('new-user@bloomex.ca');
        $adminRole = $this->createRole('Admin');
        $permission = Permission::create(['name' => 'users.view', 'guard_name' => 'api']);
        $user->givePermissionTo($permission);
        $user->assignRole($adminRole);
        $warehouse = $this->createWarehouse();
        $this->attachUserWarehouse($user, $warehouse);

        $data = [
            'email' => 'new-user@bloomex.ca',
        ];

        // WHEN
        $response = $this->withHeaders(['Accept' => 'application/json'])
            ->json('get', route('user.user.exist', $data));

        // THEN
        $this->assertJsonStructureExist($response);
    }
}