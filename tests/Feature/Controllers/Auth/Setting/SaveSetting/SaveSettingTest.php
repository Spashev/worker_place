<?php

namespace Tests\Feature\Controllers\Auth\Setting\SaveSetting;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class SaveSettingTest extends TestCase
{
    use DatabaseTransactions;
    use SaveSettingTrait;

    /**
     * @test
     */
    public function storeSetting_success_admin_response201()
    {
        // GIVEN
        $user = $this->createUserAndBe();
        $adminRole = $this->createRole('Admin');
        $permission = Permission::create(['name' => 'settings.create', 'guard_name' => 'api']);
        $user->givePermissionTo($permission);
        $user->assignRole($adminRole);

        $data = [
            'key' => 'new_name',
            'value' => 'text',
            'type' => 'string',
        ];

        // WHEN
        $response = $this->withHeaders(['Accept' => 'application/json'])
            ->json('post', route('settings.store'), $data);

        // THEN
        $this->assertEquals(201, $response->getStatusCode());
    }

    /**
     * @test
     */
    public function storeSetting_success_superAdmin_response201()
    {
        // GIVEN
        $user = $this->createUserAndBe();
        $superAdminRole = $this->createRole('Super Admin');
        $permission = Permission::create(['name' => 'settings.*', 'guard_name' => 'api']);
        $user->givePermissionTo($permission);
        $user->assignRole($superAdminRole);

        $data = [
            'key' => 'new_name',
            'value' => 'text',
            'type' => 'string',
        ];

        // WHEN
        $response = $this->withHeaders(['Accept' => 'application/json'])
            ->json('post', route('settings.store'), $data);

        // THEN
        $this->assertEquals(201, $response->getStatusCode());
    }

    /**
     * @test
     */
    public function storeSettings_filedAccess_response403()
    {
        // GIVEN
        $user = $this->createUserAndBe();
        $manager = $this->createRole('Warehouse manager');
        Permission::create(['name' => 'settings.create', 'guard_name' => 'api']);
        $user->assignRole($manager);

        $data = [
            'key' => 'new_name',
            'value' => 'text',
            'type' => 'string',
        ];

        // WHEN
        $response = $this->withHeaders(['Accept' => 'application/json'])
            ->json('post', route('settings.store'), $data);

        // THEN
        $this->assertEquals(403, $response->getStatusCode());
    }

    /**
     * @dataProvider getWrongCredentials
     * @param array $credentials
     * @param array $errors
     * @test
     */
    public function storeSettings_filed_notValid_response422(array $credentials, array $errors)
    {
        // GIVEN
        $user = $this->createUserAndBe();
        $superAdminRole = $this->createRole('Super admin');
        $permission = Permission::create(['name' => 'settings.*', 'guard_name' => 'api']);
        $user->givePermissionTo($permission);
        $user->assignRole($superAdminRole);

        // WHEN
        $response = $this->withHeaders(['Accept' => 'application/json'])
            ->json('post', route('settings.store'), $credentials);

        // THEN
        $response->assertJsonValidationErrors($errors);
        $this->assertEquals(422, $response->getStatusCode());
        $this->assertJson($response->baseResponse->getContent());
        $response->assertJsonStructure([
            'message',
        ]);
    }
}