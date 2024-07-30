<?php

namespace Tests\Feature\Controllers\Users\User\Create;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Crypt;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class UserCreateTest extends TestCase
{
    use DatabaseTransactions;
    use UserCreateTrait;

    /**
     * @test
     */
    public function create_success_superAdmin_response201()
    {
        // GIVEN
        $user = $this->createUserAndBe();
        $superAdminRole = $this->createRole('Super admin');
        $permission = Permission::create(['name' => 'users.*', 'guard_name' => 'api']);
        $user->givePermissionTo($permission);
        $adminRole = $this->createRole('Admin');
        $user->assignRole($superAdminRole);
        $warehouse = $this->createWarehouse();
        $this->attachUserWarehouse($user, $warehouse);

        $notAttachedWarehouse = $this->createWarehouse();

        $data = [
            'name' => 'bloomex user',
            'email' => 'new-user@bloomex.ca',
            'warehouses' => [$notAttachedWarehouse->warehouse_id],
            'roles' => [$adminRole->name]
        ];

        // WHEN
        $response = $this->withHeaders(['Accept' => 'application/json'])
            ->json('post', route('user.create'), $data);

        // THEN
        $this->assertEquals(201, $response->getStatusCode());
    }

    /**
     * @test
     */
    public function create_success_admin_response201()
    {
        // GIVEN
        $user = $this->createUserAndBe();
        $adminRole = $this->createRole('Admin');
        $permission = Permission::create(['name' => 'users.create', 'guard_name' => 'api']);
        $user->givePermissionTo($permission);
        $user->assignRole($adminRole);
        $warehouse = $this->createWarehouse();
        $this->attachUserWarehouse($user, $warehouse);

        $data = [
            'name' => 'bloomex user',
            'email' => 'new-user@bloomex.ca',
            'warehouses' => [$warehouse->warehouse_id],
            'roles' => [$this->role->name]
        ];

        // WHEN
        $response = $this->withHeaders(['Accept' => 'application/json'])
            ->json('post', route('user.create'), $data);

        // THEN
        $this->assertEquals(201, $response->getStatusCode());
    }

    /**
     * @test
     */
    public function create_success_warehouseManager_response201()
    {
        // GIVEN
        $user = $this->createUserAndBe('main@bloomex.ca');
        $managerRole = $this->createRole('Warehouse manager');
        $permission = Permission::create(['name' => 'users.create', 'guard_name' => 'api']);
        $user->givePermissionTo($permission);
        $packagerRole = $this->createRole('Packer');
        $user->assignRole([$managerRole]);
        $warehouse = $this->createWarehouse();
        $this->attachUserWarehouse($user, $warehouse);

        $data = [
            'name' => 'bloomex user',
            'email' => 'new-user@bloomex.ca',
            'warehouses' => [$warehouse->warehouse_id],
            'roles' => [$packagerRole->name]
        ];

        // WHEN
        $response = $this->withHeaders(['Accept' => 'application/json'])
            ->json('post', route('user.create'), $data);

        // THEN
        $this->assertEquals(201, $response->getStatusCode());
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
        $user->givePermissionTo($permission);
        $adminRole = $this->createRole('Admin');
        $user->assignRole($superAdminRole);
        $warehouse = $this->createWarehouse();
        $this->attachUserWarehouse($user, $warehouse);

        // WHEN
        $response = $this->withHeaders(['Accept' => 'application/json'])
            ->json('post', route('user.create'), $credentials);

        // THEN
        $response->assertJsonValidationErrors($errors);
        $this->assertEquals(422, $response->getStatusCode());
        $this->assertJson($response->baseResponse->getContent());
        $response->assertJsonStructure([
            'message',
        ]);
    }

    /**
     * @dataProvider getWrongCredentialsCorrectData
     * @param array $credentials
     * @param array $errors
     * @test
     */
    public function create_failed_wrongDataAttachCorrect_response422(array $credentials, array $errors)
    {
        // GIVEN
        $user = $this->createUserAndBe();
        $superAdminRole = $this->createRole('Super admin');
        $permission = Permission::create(['name' => 'users.*', 'guard_name' => 'api']);
        $user->givePermissionTo($permission);
        $adminRole = $this->createRole('Admin');
        $user->assignRole($superAdminRole);
        $warehouse = $this->createWarehouse();
        $this->attachUserWarehouse($user, $warehouse);

        // WHEN
        $response = $this->withHeaders(['Accept' => 'application/json'])
            ->json('post', route('user.create'), $credentials);

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
        $user->givePermissionTo($permission);
        $packagerRole = $this->createRole('Packer');
        $user->assignRole([$packagerRole]);
        $warehouse = $this->createWarehouse();
        $this->attachUserWarehouse($user, $warehouse);

        $data = [
            'name' => 'bloomex user',
            'email' => 'new-user@bloomex.ca',
            'warehouses' => [$warehouse->warehouse_id],
            'roles' => [$managerRole->name]
        ];

        // WHEN
        $response = $this->withHeaders(['Accept' => 'application/json'])
            ->json('post', route('user.create'), $data);

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
        $user->givePermissionTo($permission);
        $adminRole = $this->createRole('Admin');
        $user->assignRole([$managerRole]);
        $warehouse = $this->createWarehouse();
        $this->attachUserWarehouse($user, $warehouse);

        $data = [
            'name' => 'bloomex user',
            'email' => 'new-user@bloomex.ca',
            'warehouses' => [$warehouse->warehouse_id],
            'roles' => [$adminRole->name]
        ];

        // WHEN
        $response = $this->withHeaders(['Accept' => 'application/json'])
            ->json('post', route('user.create'), $data);

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

        $data = [
            'name' => 'bloomex user',
            'email' => 'new-user@bloomex.ca',
            'warehouses' => [$wrongWarehouse->warehouse_id],
            'roles' => [$managerRole->name]
        ];

        // WHEN
        $response = $this->withHeaders(['Accept' => 'application/json'])
            ->json('post', route('user.create'), $data);

        // THEN
        $this->assertEquals(403, $response->getStatusCode());
    }
}
