<?php

namespace Tests\Feature\Controllers\Auth\Setting\RemoveSetting;

use Bloomex\Common\Blca\Models\BlcaSetting;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class RemoveSettingTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * @test
     */
    public function removeSetting_success_admin_response200()
    {
        // GIVEN
        $user = $this->createUserAndBe();
        $adminRole = $this->createRole('Admin');
        $permission = Permission::create(['name' => 'settings.delete', 'guard_name' => 'api']);
        $user->givePermissionTo($permission);
        $user->assignRole($adminRole);
        /** @var BlcaSetting $setting */
        $settingCollection = $this->createSettings($user, 'string', 'test');


        // WHEN
        $response = $this->withHeaders(['Accept' => 'application/json'])
            ->json('delete', route('settings.remove', ['setting' => $settingCollection->first()->id]));

        // THEN
        $this->assertEquals(200, $response->getStatusCode());
    }

    /**
     * @test
     */
    public function removeSetting_success_superAdmin_response200()
    {
        // GIVEN
        $user = $this->createUserAndBe();
        $superAdminRole = $this->createRole('Super Admin');
        $permission = Permission::create(['name' => 'settings.*', 'guard_name' => 'api']);
        $user->givePermissionTo($permission);
        $user->assignRole($superAdminRole);

        /** @var BlcaSetting $setting */
        $settingCollection = $this->createSettings($user, 'string', 'test');


        // WHEN
        $response = $this->withHeaders(['Accept' => 'application/json'])
            ->json('delete', route('settings.remove', ['setting' => $settingCollection->first()->id]));

        // THEN
        $this->assertEquals(200, $response->getStatusCode());
    }

    /**
     * @test
     */
    public function removeSettings_filedAccess_response403()
    {
        // GIVEN
        $user = $this->createUserAndBe();
        $manager = $this->createRole('Warehouse manager');
        Permission::create(['name' => 'settings.create', 'guard_name' => 'api']);
        $user->assignRole($manager);

        /** @var BlcaSetting $setting */
        $settingCollection = $this->createSettings($user, 'string', 'test');


        // WHEN
        $response = $this->withHeaders(['Accept' => 'application/json'])
            ->json('delete', route('settings.remove', ['setting' => $settingCollection->first()->id]));

        // THEN
        $this->assertEquals(403, $response->getStatusCode());
    }

    /**
     * @test
     */
    public function removeSetting_failed_admin_response404()
    {
        // GIVEN
        $user = $this->createUserAndBe();
        $adminRole = $this->createRole('Admin');
        $permission = Permission::create(['name' => 'settings.delete', 'guard_name' => 'api']);
        $user->givePermissionTo($permission);
        $user->assignRole($adminRole);
        $this->createSettings($user, 'string', 'test');


        // WHEN
        $response = $this->withHeaders(['Accept' => 'application/json'])
            ->json('delete', route('settings.remove', ['setting' => -123]));

        // THEN
        $this->assertEquals(404, $response->getStatusCode());
    }
}