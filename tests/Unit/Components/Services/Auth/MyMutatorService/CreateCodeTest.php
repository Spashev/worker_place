<?php

namespace Tests\Unit\Components\Services\Auth\MyMutatorService;

use App\Components\Auth\Services\MyMutatorService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class CreateCodeTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * @test
     */
    public function success_manager_responseSixLength()
    {
        // GIVEN
        $user = $this->createUserAndBe();
        $managerRole = $this->createRole('Warehouse manager');
        $user->assignRole($managerRole);

        /** @var MyMutatorService $tested_method */
        $tested_method = $this->app->make(MyMutatorService::class);

        // WHEN
        $response = $tested_method->createCode();

        // THEN
        $this->assertEquals(6, strlen($response));
    }

    /**
     * @test
     */
    public function success_packager_responseFourLength()
    {
        // GIVEN
        $user = $this->createUserAndBe();
        $packagerRole = $this->createRole('Packer');
        $user->assignRole($packagerRole);

        /** @var MyMutatorService $tested_method */
        $tested_method = $this->app->make(MyMutatorService::class);

        // WHEN
        $response = $tested_method->createCode();

        // THEN
        $this->assertEquals(4, strlen($response));
    }
}