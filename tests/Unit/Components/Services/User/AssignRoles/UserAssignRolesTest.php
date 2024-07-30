<?php

namespace Tests\Unit\Components\Services\User\AssignRoles;

use App\Components\User\Service\UserMutatorService;
use App\Exceptions\ForbiddenException;
use App\Models\User;
use Bloomex\Common\Blca\Models\BlcaUser;
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
    public function assignRole_success_superAdmin_userHasRole()
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
        $credentials = $this->makeCredentials([
            'roles' => [$adminRole->name]
        ]);

        /** @var UserMutatorService $tested_method */
        $tested_method = $this->app->make(UserMutatorService::class);

        // WHEN
        $response = $tested_method->assignRoles($credentials, $userForRole);

        // THEN
        $this->assertDatabaseCount(BlcaUser::class, 2);
        $this->assertInstanceOf(BlcaUser::class, $response);
        $this->assertEquals('Admin', $response->getRoleNames()->first());
        $this->assertCount(1, $response->getRoleNames());
    }

    /**
     * @test
     */
    public function assignRole_success_admin_userHasRoleAndPermissions()
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

        /** @var Role $packager */
        $packager = $this->createRole('Packer');

        $userForRole = $this->createUser();
        $credentials = $this->makeCredentials([
            'roles' => [$packager->name, $adminRole->name]
        ]);

        /** @var UserMutatorService $tested_method */
        $tested_method = $this->app->make(UserMutatorService::class);

        // WHEN
        /** @var User $response */
        $response = $tested_method->assignRoles($credentials, $userForRole);

        // THEN
        $this->assertDatabaseCount(BlcaUser::class, 2);
        $this->assertInstanceOf(BlcaUser::class, $response);
        $this->assertCount(2, $response->getRoleNames());
        $this->assertTrue($response->hasPermissionTo($permission));
    }

    /**
     * @test
     */
    public function assignRole_failed_admin_trySuperAdmin()
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

        /** @var Role $superAdmin */
        $superAdmin = $this->createRole('Super admin');

        $userForRole = $this->createUser();
        $credentials = $this->makeCredentials([
            'roles' => [$superAdmin->name]
        ]);

        /** @var UserMutatorService $tested_method */
        $tested_method = $this->app->make(UserMutatorService::class);

        try {
            // WHEN
            /** @var User $response */
            $tested_method->assignRoles($credentials, $userForRole);
        } catch (ForbiddenException $exception) {
            // THEN
            $this->assertEquals('wrong_roles', $exception->getMessage());
            $this->assertFalse($userForRole->hasRole($superAdmin));
            return;
        }

        $this->fail('Expected ForbiddenException was not thrown.');
    }
}
