<?php

namespace Tests\Unit\Components\Services\User\AssignWarehouses;

use App\Components\User\Service\UserMutatorService;
use App\Exceptions\ForbiddenException;
use App\Models\User;
use Bloomex\Common\Blca\Models\BlcaUser;
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
    public function assignWarehouses_success_superAdmin_userHasWarehouse()
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

        $userForRole = $this->createUser();
        $credentials = $this->makeCredentials([
            'warehouses' => [$warehouse->warehouse_id]
        ]);

        /** @var UserMutatorService $tested_method */
        $tested_method = $this->app->make(UserMutatorService::class);

        // WHEN
        $response = $tested_method->assignWarehouses($credentials, $userForRole);

        // THEN
        $this->assertDatabaseCount(BlcaUser::class, 2);
        $this->assertInstanceOf(BlcaUser::class, $response);
        $this->assertEquals($warehouse->warehouse_id, $response->warehouses->first()->warehouse_id);
        $this->assertCount(1, $response->warehouses);
    }

    /**
     * @test
     */
    public function assignWarehouses_success_superAdminOtherWarehouse_userHasWarehouse()
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

        $userForRole = $this->createUser();
        $warehouseNoAccessFromCreator = $this->createWarehouse();
        $credentials = $this->makeCredentials([
            'warehouses' => [$warehouseNoAccessFromCreator->warehouse_id]
        ]);

        /** @var UserMutatorService $tested_method */
        $tested_method = $this->app->make(UserMutatorService::class);

        // WHEN
        $response = $tested_method->assignWarehouses($credentials, $userForRole);

        // THEN
        $this->assertDatabaseCount(BlcaUser::class, 2);
        $this->assertInstanceOf(BlcaUser::class, $response);
        $this->assertEquals($warehouseNoAccessFromCreator->warehouse_id, $response->warehouses->first()->warehouse_id);
        $this->assertCount(1, $response->warehouses);
    }

    /**
     * @test
     */
    public function assignWarehouses_success_admin_userHasWarehouse()
    {
        // GIVEN
        $user = $this->createUserAndBe();
        /** @var Role $adminRole */
        $adminRole = $this->createRole('Admin');
        $permission = Permission::create(['name' => 'users.*', 'guard_name' => 'api']);
        $adminRole->givePermissionTo($permission);
        $user->assignRole($adminRole);
        $warehouse = $this->createWarehouse();
        $this->attachUserWarehouse($user, $warehouse);

        $userForRole = $this->createUser();
        $credentials = $this->makeCredentials([
            'warehouses' => [$warehouse->warehouse_id]
        ]);

        /** @var UserMutatorService $tested_method */
        $tested_method = $this->app->make(UserMutatorService::class);

        // WHEN
        $response = $tested_method->assignWarehouses($credentials, $userForRole);

        // THEN
        $this->assertDatabaseCount(BlcaUser::class, 2);
        $this->assertInstanceOf(BlcaUser::class, $response);
        $this->assertEquals($warehouse->warehouse_id, $response->warehouses->first()->warehouse_id);
        $this->assertCount(1, $response->warehouses);
    }

    /**
     * @test
     */
    public function assignWarehouses_failed_admin_userHasNoWarehouse()
    {
        // GIVEN
        $user = $this->createUserAndBe();
        /** @var Role $adminRole */
        $adminRole = $this->createRole('Admin');
        $permission = Permission::create(['name' => 'users.*', 'guard_name' => 'api']);
        $adminRole->givePermissionTo($permission);
        $user->assignRole($adminRole);
        $warehouse = $this->createWarehouse();
        $this->attachUserWarehouse($user, $warehouse);

        $userForWarehouse = $this->createUser();
        $warehouseNew = $this->createWarehouse();
        $credentials = $this->makeCredentials([
            'warehouses' => [$warehouseNew->warehouse_id]
        ]);

        /** @var UserMutatorService $tested_method */
        $tested_method = $this->app->make(UserMutatorService::class);

        try {
            // WHEN
            /** @var User $response */
            $tested_method->assignWarehouses($credentials, $userForWarehouse);
        } catch (ForbiddenException $exception) {
            // THEN
            $this->assertEquals('wrong_warehouse', $exception->getMessage());
            $this->assertCount(0, $userForWarehouse->warehouses);
            return;
        }

        $this->fail('Expected ForbiddenException was not thrown.');
    }
}
