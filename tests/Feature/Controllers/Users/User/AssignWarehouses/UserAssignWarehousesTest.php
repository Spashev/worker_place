<?php

namespace Tests\Feature\Controllers\Users\User\AssignWarehouses;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class UserAssignWarehousesTest extends TestCase
{
    use DatabaseTransactions;
    use UserAssignWarehousesTrait;

    /**
     * @test
     */
    public function assignWarehouses_successWarehouseBelongCreatorSuperAdmin_response201()
    {
        // GIVEN
        $user = $this->createUserAndBe();
        /** @var Role $superAdminRole */
        $superAdminRole = $this->createRole('Super admin');
        $user->assignRole($superAdminRole);
        $warehouse = $this->createWarehouse();
        $this->attachUserWarehouse($user, $warehouse);

        $userForWarehouses = $this->createUser();

        $data = [
            'warehouses' => [$warehouse->warehouse_id]
        ];

        // WHEN
        $response = $this->withHeaders(['Accept' => 'application/json'])
            ->json('put', route('user.user.warehouses', ['user' => $userForWarehouses->id]), $data);

        // THEN
        $this->assertEquals(201, $response->getStatusCode());
    }

    /**
     * @test
     */
    public function assignWarehouses_successWarehouseNotBelongCreatorSuperAdmin_response201()
    {
        // GIVEN
        $user = $this->createUserAndBe();
        /** @var Role $superAdminRole */
        $superAdminRole = $this->createRole('Super admin');
        $user->assignRole($superAdminRole);
        $warehouse = $this->createWarehouse();
        $this->attachUserWarehouse($user, $warehouse);

        $userForWarehouses = $this->createUser();
        $warehouseNew = $this->createWarehouse();

        $data = [
            'warehouses' => [$warehouseNew->warehouse_id]
        ];

        // WHEN
        $response = $this->withHeaders(['Accept' => 'application/json'])
            ->json('put', route('user.user.warehouses', ['user' => $userForWarehouses->id]), $data);

        // THEN
        $this->assertEquals(201, $response->getStatusCode());
    }

    /**
     * @test
     */
    public function assignWarehouses_successWarehouseBelongCreatorAdmin_response201()
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

        $userForWarehouse = $this->createUser();

        $data = [
            'warehouses' => [$warehouse->warehouse_id]
        ];

        // WHEN
        $response = $this->withHeaders(['Accept' => 'application/json'])
            ->json('put', route('user.user.warehouses', ['user' => $userForWarehouse->id]), $data);

        // THEN
        $this->assertEquals(201, $response->getStatusCode());
    }

    /**
     * @test
     */
    public function assignWarehouses_failedWarehouseNotBelongCreatorAdmin_response201()
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

        $userForWarehouse = $this->createUser();

        $warehouseNew = $this->createWarehouse();
        $data = [
            'warehouses' => [$warehouseNew->warehouse_id]
        ];

        // WHEN
        $response = $this->withHeaders(['Accept' => 'application/json'])
            ->json('put', route('user.user.warehouses', ['user' => $userForWarehouse->id]), $data);

        // THEN
        $this->assertEquals(403, $response->getStatusCode());
    }


    /**
     * @test
     */
    public function assignWarehouses_successWarehouseBelongCreatorManager_response201()
    {
        // GIVEN
        $user = $this->createUserAndBe();
        /** @var Role $adminRole */
        $managerRole = $this->createRole('Warehouse manager');
        $permission = Permission::create(['name' => 'users.create', 'guard_name' => 'api']);
        $managerRole->givePermissionTo($permission);
        $user->assignRole($managerRole);
        $warehouse = $this->createWarehouse();
        $this->attachUserWarehouse($user, $warehouse);

        $userForWarehouse = $this->createUser();

        $data = [
            'warehouses' => [$warehouse->warehouse_id]
        ];

        // WHEN
        $response = $this->withHeaders(['Accept' => 'application/json'])
            ->json('put', route('user.user.warehouses', ['user' => $userForWarehouse->id]), $data);

        // THEN
        $this->assertEquals(201, $response->getStatusCode());
    }

    /**
     * @test
     */
    public function assignWarehouses_failedWarehouseNotBelongCreatorManager_response201()
    {
        // GIVEN
        $user = $this->createUserAndBe();
        /** @var Role $adminRole */
        $managerRole = $this->createRole('Warehouse manager');
        $permission = Permission::create(['name' => 'users.create', 'guard_name' => 'api']);
        $managerRole->givePermissionTo($permission);
        $user->assignRole($managerRole);
        $warehouse = $this->createWarehouse();
        $this->attachUserWarehouse($user, $warehouse);

        $userForWarehouse = $this->createUser();
        $warehouseNew = $this->createWarehouse();
        $data = [
            'warehouses' => [$warehouseNew->warehouse_id]
        ];

        // WHEN
        $response = $this->withHeaders(['Accept' => 'application/json'])
            ->json('put', route('user.user.warehouses', ['user' => $userForWarehouse->id]), $data);

        // THEN
        $this->assertEquals(403, $response->getStatusCode());
    }

    /**
     * @dataProvider getWrongCredentials
     * @param array $credentials
     * @param array $errors
     * @test
     */
    public function ssignWarehouses_failed_wrongData_response422(array $credentials, array $errors)
    {
        // GIVEN
        $user = $this->createUserAndBe('main@bloomex.ca');
        /** @var Role $managerRole */
        $managerRole = $this->createRole('Warehouse manager');
        $permission = Permission::create(['name' => 'users.create', 'guard_name' => 'api']);
        $managerRole->givePermissionTo($permission);
        $user->assignRole($managerRole);
        $warehouse = $this->createWarehouse();
        $this->attachUserWarehouse($user, $warehouse);

        $userForWarehouse = $this->createUser();

        // WHEN
        $response = $this->withHeaders(['Accept' => 'application/json'])
            ->json('put', route('user.user.warehouses', ['user' => $userForWarehouse->id]), $credentials);

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
    public function assignWarehouses_failedWarehouseNoAccess_response403()
    {
        // GIVEN
        $user = $this->createUserAndBe();
        /** @var Role $adminRole */
        $managerRole = $this->createRole('Warehouse manager');
        $permission = Permission::create(['name' => 'users.view', 'guard_name' => 'api']);
        $managerRole->givePermissionTo($permission);
        $user->assignRole($managerRole);
        $warehouse = $this->createWarehouse();
        $this->attachUserWarehouse($user, $warehouse);

        $userForWarehouse = $this->createUser();

        $data = [
            'warehouses' => [$warehouse->warehouse_id]
        ];

        // WHEN
        $response = $this->withHeaders(['Accept' => 'application/json'])
            ->json('put', route('user.user.warehouses', ['user' => $userForWarehouse->id]), $data);

        // THEN
        $this->assertEquals(403, $response->getStatusCode());
    }

    /**
     * @test
     */
    public function assignWarehouses_failedNoAccessByRole_response403()
    {
        // GIVEN
        $user = $this->createUserAndBe();
        /** @var Role $adminRole */
        $packagerRole = $this->createRole('Packer');
        $user->assignRole($packagerRole);
        $warehouse = $this->createWarehouse();
        $this->attachUserWarehouse($user, $warehouse);

        $userForWarehouse = $this->createUser();

        $data = [
            'warehouses' => [$warehouse->warehouse_id]
        ];

        // WHEN
        $response = $this->withHeaders(['Accept' => 'application/json'])
            ->json('put', route('user.user.warehouses', ['user' => $userForWarehouse->id]), $data);

        // THEN
        $this->assertEquals(403, $response->getStatusCode());
    }
}
