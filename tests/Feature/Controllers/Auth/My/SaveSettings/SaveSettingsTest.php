<?php

namespace Tests\Feature\Controllers\Auth\My\SaveSettings;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\Feature\Controllers\Auth\Auth\Login\LoginTrait;
use Tests\TestCase;

class SaveSettingsTest extends TestCase
{
    use DatabaseTransactions;
    use SaveSettingsTrait;

    /**
     * @test
     */
    public function success_response201()
    {
        // GIVEN
        $this->createUserAndBe();
        $credentials = $this->createCredentials();

        // WHEN
        $response = $this->withHeaders(['Accept' => 'application/json'])
            ->json('POST', route('my.settings'), $credentials);

        // THEN
        $this->assertEquals(201, $response->getStatusCode());
    }
}
