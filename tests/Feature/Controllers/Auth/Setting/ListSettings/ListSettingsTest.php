<?php

namespace Tests\Feature\Controllers\Auth\Setting\ListSettings;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class ListSettingsTest extends TestCase
{
    use DatabaseTransactions;
    use ListSettingsTrait;

    /**
     * @test
     */
    public function storeSetting_success_admin_response200()
    {
        // GIVEN
        $user = $this->createUserAndBe();
        $this->createSettings($user, 'integer', 2);
        $this->createSettings($user, 'integer', 15);
        $this->createSettings($user, 'string', 'value data');

        // WHEN
        $response = $this->withHeaders(['Accept' => 'application/json'])
            ->json('get', route('settings.list'));

        // THEN
        $this->assertEquals(200, $response->getStatusCode());
    }

    /**
     * @test
     */
    public function storeSetting_success_checkStructure()
    {
        // GIVEN
        $user = $this->createUserAndBe();
        $this->createSettings($user, 'integer', 2);

        // WHEN
        $response = $this->withHeaders(['Accept' => 'application/json'])
            ->json('get', route('settings.list'));

        // THEN
        $this->assertJsonStructureExist($response);
    }

    /**
     * @test
     */
    public function storeSettings_failed_admin_response401()
    {
        // GIVEN
        $user = $this->createUser();
        $this->createSettings($user, 'integer', 2);

        // WHEN
        $response = $this->withHeaders(['Accept' => 'application/json'])
            ->json('get', route('settings.list'));

        // THEN
        $this->assertEquals(401, $response->getStatusCode());
    }
}