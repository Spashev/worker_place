<?php

namespace Tests\Unit\Components\Services\User\Update;

use App\Components\User\Service\UserMutatorService;
use App\Exceptions\ForbiddenException;
use App\Models\User;
use Bloomex\Common\Blca\Models\BlcaUser;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class UserUpdateTest extends TestCase
{
    use DatabaseTransactions;
    use UserUpdateTrait;

    /**
     * @test
     */
    public function update_responseUpdatedUser_dbHasTwo_oneRole()
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
            'warehouses' => [$notAttachedWarehouse->warehouse_id],
            'roles' => [$adminRole->name]
        ]);
        $newUser = $this->createUser();

        /** @var UserMutatorService $tested_method */
        $tested_method = $this->app->make(UserMutatorService::class);

        // WHEN
        /** @var User $response */
        $response = $tested_method->update($credentials, $newUser);

        // THEN
        $this->assertDatabaseCount(BlcaUser::class, 2);
        $this->assertInstanceOf(BlcaUser::class, $response);
        $this->assertEquals('Admin', $response->getRoleNames()->first());
        $this->assertCount(1, $response->getRoleNames());
    }

    /**
     * @test
     */
    public function update_responseUpdated_dbHasTwo_twoRoles()
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
            'warehouses' => [$notAttachedWarehouse->warehouse_id],
            'roles' => [$adminRole->name, $superAdminRole->name]
        ]);
        $newUser = $this->createUser();

        /** @var UserMutatorService $tested_method */
        $tested_method = $this->app->make(UserMutatorService::class);

        // WHEN
        /** @var User $response */
        $response = $tested_method->update($credentials, $newUser);

        // THEN
        $this->assertDatabaseCount(BlcaUser::class, 2);
        $this->assertInstanceOf(BlcaUser::class, $response);
        $this->assertCount(2, $response->getRoleNames());
    }

    /**
     * @test
     */
    public function update_responseException_warehouseNotAttached()
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
            'warehouses' => [$notAttachedWarehouse->warehouse_id],
            'roles' => [$managerRole->name]
        ]);
        $newUser = $this->createUser();

        /** @var UserMutatorService $tested_method */
        $tested_method = $this->app->make(UserMutatorService::class);

        // WHEN
        try {
            $tested_method->update($credentials, $newUser);
        } catch (ForbiddenException $exception) {
            // THEN
            $this->assertEquals('wrong_warehouse', $exception->getMessage());
            $this->assertDatabaseCount(BlcaUser::class, 2);
            return;
        }

        $this->fail('Expected ForbiddenException was not thrown.');
    }

    /**
     * @test
     */
    public function update_responseUpdated_warehouseNotAttached_roleSuperAdmin()
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
            'warehouses' => [$notAttachedWarehouse->warehouse_id],
            'roles' => [$managerRole->name]
        ]);
        $newUser = $this->createUser();

        /** @var UserMutatorService $tested_method */
        $tested_method = $this->app->make(UserMutatorService::class);

        // WHEN
        $response = $tested_method->update($credentials, $newUser);

        // THEN
        $this->assertDatabaseCount(BlcaUser::class, 2);
        $this->assertInstanceOf(User::class, $response);
        $this->assertEquals($notAttachedWarehouse->warehouse_id, $response->warehouses->pluck('warehouse_id')->first());
    }
}