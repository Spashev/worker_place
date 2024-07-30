<?php

namespace Tests\Unit\Components\Services\Auth\Setting;

use App\Components\Auth\Services\SettingService;
use App\Http\Requests\Auth\SettingRequest;
use Bloomex\Common\Blca\Models\BlcaSetting;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class SaveSettingTest extends TestCase
{
    use DatabaseTransactions;
    /**
     * @test
     */
    public function storeSetting_success_dbHasOne()
    {
        // GIVEN
        $this->createUserAndBe();
        $data = [
            'key' => 'new_name',
            'value' => 'text',
            'type' => 'string',
        ];
        $request = new SettingRequest($data);
        $request->setContainer(app())->validateResolved();

        /** @var SettingService $tested_method */
        $tested_method = $this->app->make(SettingService::class);

        // WHEN
        $tested_method->saveSetting($request);

        // THEN
        $this->assertDatabaseCount(BlcaSetting::class, 1);
    }
}