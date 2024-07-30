<?php

namespace Tests\Unit\Components\Services\User\Create;

use App\Components\User\Service\UserMutatorService;
use App\Exceptions\ForbiddenException;
use App\Models\User;
use Bloomex\Common\Blca\Models\BlcaUser;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class UserCreateTest extends TestCase
{
    use DatabaseTransactions;
    use UserCreateTrait;

    /**
     * @test
     */
    public function create_responseCreatedUser_dbHasTwo_oneRole()
    {
        // GIVEN
        $user = $this->createUserAndBe();
        $superAdminRole = $this->createRole('Super admin');
        $adminRole = $this->createRole('Admin');
        $user->assignRole($superAdminRole);
        $warehouse = $this->createWarehouse();
        $this->attachUserWarehouse($user, $warehouse);

        $notAttachedWarehouse = $this->createWarehouse();

        $credentials = $this->makeCredentials([
            'name' => 'Some cool name',
            'email' => 'new-user@bloomex.ca',
            'warehouses' => [$notAttachedWarehouse->warehouse_id],
            'roles' => [$adminRole->name]
        ]);

        /** @var UserMutatorService $tested_method */
        $tested_method = $this->app->make(UserMutatorService::class);

        // WHEN
        /** @var User $response */
        $response = $tested_method->create($credentials, $user);

        // THEN
        $this->assertDatabaseCount(BlcaUser::class, 2);
        $this->assertInstanceOf(BlcaUser::class, $response);
        $this->assertEquals('Admin', $response->getRoleNames()->first());
        $this->assertCount(1, $response->getRoleNames());
    }

    /**
     * @test
     */
    public function create_responseCreated_dbHasTwo_twoRoles()
    {
        // GIVEN
        $user = $this->createUserAndBe();
        $superAdminRole = $this->createRole('Super admin');
        $adminRole = $this->createRole('Admin');
        $user->assignRole($superAdminRole);
        $warehouse = $this->createWarehouse();
        $this->attachUserWarehouse($user, $warehouse);

        $notAttachedWarehouse = $this->createWarehouse();

        $credentials = $this->makeCredentials([
            'name' => 'Some cool name',
            'email' => 'new-user@bloomex.ca',
            'warehouses' => [$notAttachedWarehouse->warehouse_id],
            'roles' => [$adminRole->name, $superAdminRole->name]
        ]);

        /** @var UserMutatorService $tested_method */
        $tested_method = $this->app->make(UserMutatorService::class);

        // WHEN
        $response = $tested_method->create($credentials, $user);

        // THEN
        $this->assertDatabaseCount(BlcaUser::class, 2);
        $this->assertInstanceOf(BlcaUser::class, $response);
        $this->assertCount(2, $response->getRoleNames());
    }

    /**
     * @test
     */
    public function create_responseException_warehouseNotAttached()
    {
        // GIVEN
        $user = $this->createUserAndBe();
        $adminRole = $this->createRole('Admin');
        $user->assignRole($adminRole);
        $warehouse = $this->createWarehouse();
        $managerRole = $this->createRole('Warehouse manager');
        $this->attachUserWarehouse($user, $warehouse);

        $notAttachedWarehouse = $this->createWarehouse();

        $credentials = $this->makeCredentials([
            'name' => 'Some cool name',
            'email' => 'new-user@bloomex.ca',
            'warehouses' => [$notAttachedWarehouse->warehouse_id],
            'roles' => [$managerRole->name]
        ]);

        /** @var UserMutatorService $tested_method */
        $tested_method = $this->app->make(UserMutatorService::class);

        // WHEN
        try {
            $tested_method->create($credentials, $user);
        } catch (ForbiddenException $exception) {
            // THEN
            $this->assertEquals('wrong_warehouse', $exception->getMessage());
            $this->assertDatabaseCount(BlcaUser::class, 1);
            return;
        }

        $this->fail('Expected ForbiddenException was not thrown.');
    }

    /**
     * @test
     */
    public function create_responseCreated_warehouseNotAttached_roleSuperAdmin()
    {
        // GIVEN
        $user = $this->createUserAndBe();
        $superAdminRole = $this->createRole('Super admin');
        $user->assignRole($superAdminRole);
        $warehouse = $this->createWarehouse();
        $managerRole = $this->createRole('Warehouse manager');
        $this->attachUserWarehouse($user, $warehouse);

        $notAttachedWarehouse = $this->createWarehouse();

        $credentials = $this->makeCredentials([
            'name' => 'Some cool name',
            'email' => 'new-user@bloomex.ca',
            'warehouses' => [$notAttachedWarehouse->warehouse_id],
            'roles' => [$managerRole->name]
        ]);

        /** @var UserMutatorService $tested_method */
        $tested_method = $this->app->make(UserMutatorService::class);

        // WHEN
        $response = $tested_method->create($credentials, $user);

        // THEN
        $this->assertDatabaseCount(BlcaUser::class, 2);
        $this->assertInstanceOf(User::class, $response);
        $this->assertEquals($notAttachedWarehouse->warehouse_id, $response->warehouses->pluck('warehouse_id')->first());
    }
}