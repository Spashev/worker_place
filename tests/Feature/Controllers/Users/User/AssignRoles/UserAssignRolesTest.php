<?php

namespace Tests\Feature\Controllers\Users\User\AssignRoles;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class UserAssignRolesTest extends TestCase
{
    use DatabaseTransactions;
    use UserAssignRolesTrait;

    /**
     * @test
     */
    public function assignRole_success_superAdmin_response201()
    {
        // GIVEN
        $user = $this->createUserAndBe();
        /** @var Role $superAdminRole */
        $superAdminRole = $this->createRole('Super admin');
        $permission = Permission::create(['name' => 'users.*', 'guard_name' => 'api']);
        $superAdminRole->givePermissionTo($permission);
        $user->assignRole($superAdminRole);
        $warehouse = $this->createWarehouse();
        $this->attachUserWarehouse($user, $warehouse);

        /** @var Role $adminRole */
        $adminRole = $this->createRole('Admin');
        /** @var Permission $permissionAdmin */
        $permissionAdmin = Permission::create(['name' => 'users.create', 'guard_name' => 'api']);
        $adminRole->givePermissionTo($permissionAdmin);


        $userForRole = $this->createUser();

        $data = [
            'roles' => [$adminRole->name]
        ];

        // WHEN
        $response = $this->withHeaders(['Accept' => 'application/json'])
            ->json('put', route('user.roles', ['user' => $userForRole->id]), $data);

        // THEN
        $this->assertEquals(201, $response->getStatusCode());
    }

    /**
     * @test
     */
    public function assignRole_success_admin_response201()
    {
        // GIVEN
        $user = $this->createUserAndBe();
        /** @var Role $adminRole */
        $adminRole = $this->createRole('Admin');
        $permission = Permission::create(['name' => 'users.create', 'guard_name' => 'api']);
        $adminRole->givePermissionTo($permission);
        $user->assignRole($adminRole);
        $warehouse = $this->createWarehouse();
        $this->attachUserWarehouse($user, $warehouse);

        /** @var Role $packagerRole */
        $packagerRole = $this->createRole('Packer');
        $userForRole = $this->createUser();

        $data = [
            'roles' => [$packagerRole->name]
        ];

        // WHEN
        $response = $this->withHeaders(['Accept' => 'application/json'])
            ->json('put', route('user.roles', ['user' => $userForRole->id]), $data);

        // THEN
        $this->assertEquals(201, $response->getStatusCode());
    }

    /**
     * @test
     */
    public function assignRole_success_warehouseManager_twoRoles_response201()
    {
        // GIVEN
        $user = $this->createUserAndBe('main@bloomex.ca');
        /** @var Role $managerRole */
        $managerRole = $this->createRole('Warehouse manager');
        $permission = Permission::create(['name' => 'users.create', 'guard_name' => 'api']);
        $managerRole->givePermissionTo($permission);
        $user->assignRole([$managerRole]);
        $warehouse = $this->createWarehouse();
        $this->attachUserWarehouse($user, $warehouse);

        $packagerRole = $this->createRole('Packer');
        $userForRole = $this->createUser();

        $data = [
            'roles' => [$packagerRole->name, $managerRole->name]
        ];

        // WHEN
        $response = $this->withHeaders(['Accept' => 'application/json'])
            ->json('put', route('user.roles', ['user' => $userForRole->id]), $data);

        // THEN
        $this->assertEquals(201, $response->getStatusCode());
    }

    /**
     * @dataProvider getWrongCredentials
     * @param array $credentials
     * @param array $errors
     * @test
     */
    public function assignRole_failed_wrongData_response422(array $credentials, array $errors)
    {
        // GIVEN
        $user = $this->createUserAndBe('main@bloomex.ca');
        /** @var Role $managerRole */
        $managerRole = $this->createRole('Warehouse manager');
        $permission = Permission::create(['name' => 'users.create', 'guard_name' => 'api']);
        $managerRole->givePermissionTo($permission);
        $user->assignRole([$managerRole]);
        $warehouse = $this->createWarehouse();
        $this->attachUserWarehouse($user, $warehouse);

        $userForRole = $this->createUser();

        // WHEN
        $response = $this->withHeaders(['Accept' => 'application/json'])
            ->json('put', route('user.roles', ['user' => $userForRole->id]), $credentials);

        // THEN
        $response->assertJsonValidationErrors($errors);
        $this->assertEquals(422, $response->getStatusCode());
        $this->assertJson($response->baseResponse->getContent());
        $response->assertJsonStructure([
            'message',
        ]);
    }

    /**
     * @test
     */
    public function assignRole_failed_noAccessToRole_response403()
    {
        // GIVEN
        $user = $this->createUserAndBe('main@bloomex.ca');
        /** @var Role $managerRole */
        $managerRole = $this->createRole('Warehouse manager');
        $permission = Permission::create(['name' => 'users.create', 'guard_name' => 'api']);
        $managerRole->givePermissionTo($permission);
        $user->assignRole([$managerRole]);
        $warehouse = $this->createWarehouse();
        $this->attachUserWarehouse($user, $warehouse);

        $adminRole = $this->createRole('Admin');
        $userForRole = $this->createUser();

        $data = [
            'roles' => [$adminRole->name]
        ];

        // WHEN
        $response = $this->withHeaders(['Accept' => 'application/json'])
            ->json('put', route('user.roles', ['user' => $userForRole->id]), $data);

        // THEN
        $this->assertEquals(403, $response->getStatusCode());
        $response->assertJsonStructure([
            'message',
        ]);
    }

    /**
     * @test
     */
    public function assignRole_failed_noAccessOneRole_response403()
    {
        // GIVEN
        $user = $this->createUserAndBe('main@bloomex.ca');
        /** @var Role $managerRole */
        $managerRole = $this->createRole('Warehouse manager');
        $permission = Permission::create(['name' => 'users.create', 'guard_name' => 'api']);
        $managerRole->givePermissionTo($permission);
        $user->assignRole([$managerRole]);
        $warehouse = $this->createWarehouse();
        $this->attachUserWarehouse($user, $warehouse);

        $adminRole = $this->createRole('Admin');
        $userForRole = $this->createUser();

        $data = [
            'roles' => [$adminRole->name, $managerRole->name]
        ];

        // WHEN
        $response = $this->withHeaders(['Accept' => 'application/json'])
            ->json('put', route('user.roles', ['user' => $userForRole->id]), $data);

        // THEN
        $this->assertEquals(403, $response->getStatusCode());
        $response->assertJsonStructure([
            'message',
        ]);
    }
}
