<?php

namespace Tests\Feature\Controllers\Users\User\Update;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class UserUpdateTest extends TestCase
{
    use DatabaseTransactions;
    use UserUpdateTrait;

    /**
     * @test
     */
    public function update_success_superAdmin_response202()
    {
        // GIVEN
        $user = $this->createUserAndBe();
        $superAdminRole = $this->createRole('Super admin');
        $permission = Permission::create(['name' => 'users.*', 'guard_name' => 'api']);
        $superAdminRole->givePermissionTo($permission);
        $user->assignRole($superAdminRole);

        $warehouse = $this->createWarehouse();
        $this->attachUserWarehouse($user, $warehouse);

        $adminRole = $this->createRole('Admin');
        $notAttachedWarehouse = $this->createWarehouse();
        $newUser = $this->createUser();

        $data = [
            'warehouses' => [$notAttachedWarehouse->warehouse_id],
            'roles' => [$adminRole->name]
        ];

        // WHEN
        $response = $this->withHeaders(['Accept' => 'application/json'])
            ->json('put', route('user.update', ['user' => $newUser->id]), $data);

        // THEN
        $this->assertEquals(202, $response->getStatusCode());
    }

    /**
     * @test
     */
    public function update_success_admin_response202()
    {
        // GIVEN
        $user = $this->createUserAndBe();
        $adminRole = $this->createRole('Admin');
        $permission = Permission::create(['name' => 'users.create', 'guard_name' => 'api']);
        $adminRole->givePermissionTo($permission);
        $user->assignRole($adminRole);
        $warehouse = $this->createWarehouse();
        $this->attachUserWarehouse($user, $warehouse);

        $newUser = $this->createUser();
        $data = [
            'warehouses' => [$warehouse->warehouse_id],
            'roles' => [$this->role->name]
        ];

        // WHEN
        $response = $this->withHeaders(['Accept' => 'application/json'])
            ->json('put', route('user.update', ['user' => $newUser->id]), $data);

        // THEN
        $this->assertEquals(202, $response->getStatusCode());
    }

    /**
     * @test
     */
    public function create_success_warehouseManager_response202()
    {
        // GIVEN
        $user = $this->createUserAndBe('main@bloomex.ca');
        $managerRole = $this->createRole('Warehouse manager');
        $permission = Permission::create(['name' => 'users.create', 'guard_name' => 'api']);
        $managerRole->givePermissionTo($permission);
        $packagerRole = $this->createRole('Packer');
        $user->assignRole([$managerRole]);
        $warehouse = $this->createWarehouse();
        $this->attachUserWarehouse($user, $warehouse);

        $newUser = $this->createUser();
        $data = [
            'warehouses' => [$warehouse->warehouse_id],
            'roles' => [$packagerRole->name]
        ];

        // WHEN
        $response = $this->withHeaders(['Accept' => 'application/json'])
            ->json('put', route('user.update', $newUser), $data);

        // THEN
        $this->assertEquals(202, $response->getStatusCode());
    }

    /**
     * @dataProvider getWrongCredentials
     * @param array $credentials
     * @param array $errors
     * @test
     */
    public function create_failed_wrongData_response422(array $credentials, array $errors)
    {
        // GIVEN
        $user = $this->createUserAndBe();
        $superAdminRole = $this->createRole('Super admin');
        $permission = Permission::create(['name' => 'users.create', 'guard_name' => 'api']);
        $superAdminRole->givePermissionTo($permission);
        $user->assignRole($superAdminRole);
        $warehouse = $this->createWarehouse();
        $this->attachUserWarehouse($user, $warehouse);

        $newUser = $this->createUser();

        // WHEN
        $response = $this->withHeaders(['Accept' => 'application/json'])
            ->json('put', route('user.update', $newUser), $credentials);

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
    public function create_failed_warehouseManager_response403()
    {
        // GIVEN
        $user = $this->createUserAndBe('main@bloomex.ca');
        $managerRole = $this->createRole('Warehouse manager');
        $permission = Permission::create(['name' => 'users.create', 'guard_name' => 'api']);
        $managerRole->givePermissionTo($permission);

        $packagerRole = $this->createRole('Packer');
        $user->assignRole([$packagerRole]);

        $warehouse = $this->createWarehouse();
        $this->attachUserWarehouse($user, $warehouse);

        $newUser = $this->createUser();
        $data = [
            'warehouses' => [$warehouse->warehouse_id],
            'roles' => [$managerRole->name]
        ];

        // WHEN
        $response = $this->withHeaders(['Accept' => 'application/json'])
            ->json('put', route('user.update', $newUser), $data);

        // THEN
        $this->assertEquals(403, $response->getStatusCode());
    }

    /**
     * @test
     */
    public function create_failed_admin_response403()
    {
        // GIVEN
        $user = $this->createUserAndBe('main@bloomex.ca');
        $managerRole = $this->createRole('Warehouse manager');
        $permission = Permission::create(['name' => 'users.create', 'guard_name' => 'api']);
        $managerRole->givePermissionTo($permission);
        $user->assignRole([$managerRole]);

        $adminRole = $this->createRole('Admin');
        $warehouse = $this->createWarehouse();
        $this->attachUserWarehouse($user, $warehouse);

        $newUser = $this->createUser();
        $data = [
            'warehouses' => [$warehouse->warehouse_id],
            'roles' => [$adminRole->name]
        ];

        // WHEN
        $response = $this->withHeaders(['Accept' => 'application/json'])
            ->json('put', route('user.update', $newUser), $data);

        // THEN
        $this->assertEquals(403, $response->getStatusCode());
    }

    /**
     * @test
     */
    public function create_failed_wrongWarehouse_response403()
    {
        // GIVEN
        $user = $this->createUserAndBe('main@bloomex.ca');
        $managerRole = $this->createRole('Warehouse manager');
        $permission = Permission::create(['name' => 'users.create', 'guard_name' => 'api']);
        $user->givePermissionTo($permission);
        $user->assignRole([$managerRole]);
        $warehouse = $this->createWarehouse();
        $this->attachUserWarehouse($user, $warehouse);

        $wrongWarehouse = $this->createWarehouse();

        $newUser = $this->createUser();
        $data = [
            'warehouses' => [$wrongWarehouse->warehouse_id],
            'roles' => [$managerRole->name]
        ];

        // WHEN
        $response = $this->withHeaders(['Accept' => 'application/json'])
            ->json('put', route('user.update', $newUser), $data);

        // THEN
        $this->assertEquals(403, $response->getStatusCode());
    }
}
