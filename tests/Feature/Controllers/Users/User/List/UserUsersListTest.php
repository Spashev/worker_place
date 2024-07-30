<?php

namespace Tests\Feature\Controllers\Users\User\List;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class UserUsersListTest extends TestCase
{
    use DatabaseTransactions;
    use UserUsersListTrait;

    /**
     * @test
     */
    public function list_success_response200()
    {
        // GIVEN
        $user = $this->createUserAndBe();
        $warehouse = $this->createWarehouse();
        $this->attachUserWarehouse($user, $warehouse);

        $userOne = $this->createUser('user_one@bloomec.ca');
        $userTwo = $this->createUser('user_two@bloomec.ca');
        $this->attachUserWarehouse($userOne, $warehouse);
        $this->attachUserWarehouse($userTwo, $warehouse);

        // WHEN
        $response = $this->withHeaders(['Accept' => 'application/json'])
            ->json('get', route('user.list'));

        // THEN
        $this->assertEquals(200, $response->getStatusCode());
    }

    /**
     * @test
     */
    public function list_success_checkStructure()
    {
        // GIVEN
        $user = $this->createUserAndBe();
        $warehouse = $this->createWarehouse();
        $this->attachUserWarehouse($user, $warehouse);

        $userOne = $this->createUser('user_one@bloomec.ca');
        $userTwo = $this->createUser('user_two@bloomec.ca');
        $this->attachUserWarehouse($userOne, $warehouse);
        $this->attachUserWarehouse($userTwo, $warehouse);

        $this->createUser('user_three@bloomec.ca');

        // WHEN
        $response = $this->withHeaders(['Accept' => 'application/json'])
            ->json('get', route('user.list'));

        // THEN
        $this->assertJsonStructure($response);
    }

    /**
     * @test
     * @dataProvider getOrderIdsString
     * @param array $credentials
     */
    public function list_failed_response400(array $credentials)
    {
        // GIVEN
        $user = $this->createUserAndBe();
        $warehouse = $this->createWarehouse();
        $this->attachUserWarehouse($user, $warehouse);

        $userOne = $this->createUser('user_one@bloomec.ca');
        $userTwo = $this->createUser('user_two@bloomec.ca');
        $this->attachUserWarehouse($userOne, $warehouse);
        $this->attachUserWarehouse($userTwo, $warehouse);

        $this->createUser('user_three@bloomec.ca');

        // WHEN
        $response = $this->withHeaders(['Accept' => 'application/json'])
            ->json('get', route('user.list'), $credentials);

        // THEN
        $response->assertUnprocessable();
        $this->assertJson($response->baseResponse->getContent());
        $response->assertJsonStructure([
            'message',
        ]);
    }
}
