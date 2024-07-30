<?php

namespace Tests\Unit\Components\Services\User\List;

use App\Components\User\Service\UserQueryService;
use Bloomex\Common\Blca\Models\BlcaUser;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class UserUsersListTest extends TestCase
{
    use DatabaseTransactions;
    use UserUsersListTrait;

    /**
     * @test
     * @throws \Exception
     */
    public function list_responseAllUsers_dbHasThree()
    {
        // GIVEN
        $user = $this->createUserAndBe();
        $warehouse = $this->createWarehouse();
        $this->attachUserWarehouse($user, $warehouse);

        $userOne = $this->createUser('user_one@bloomec.ca');
        $userTwo = $this->createUser('user_two@bloomec.ca');
        $this->attachUserWarehouse($userOne, $warehouse);
        $this->attachUserWarehouse($userTwo, $warehouse);

        $credentials = $this->makeCredentials([]);

        /** @var UserQueryService $tested_method */
        $tested_method = $this->app->make(UserQueryService::class);

        // WHEN
        $response = $tested_method->list($credentials);

        // THEN
        $this->assertDatabaseCount(BlcaUser::class, 3);
        $this->assertInstanceOf(LengthAwarePaginator::class, $response);
        $this->assertInstanceOf(BlcaUser::class, $response->items()[0]);
        $this->assertCount(3, $response->items());
    }

    /**
     * @test
     * @throws \Exception
     */
    public function list_responseThreeBelongsUsers_dbHasFour()
    {
        // GIVEN
        $user = $this->createUserAndBe();
        $warehouse = $this->createWarehouse();
        $this->attachUserWarehouse($user, $warehouse);

        $userOne = $this->createUser('user_one@bloomec.ca');
        $userTwo = $this->createUser('user_two@bloomec.ca');
        $this->attachUserWarehouse($userOne, $warehouse);
        $this->attachUserWarehouse($userTwo, $warehouse);

        $this->createUser('user_four@bloomec.ca');

        $credentials = $this->makeCredentials([]);

        /** @var UserQueryService $tested_method */
        $tested_method = $this->app->make(UserQueryService::class);

        // WHEN
        $response = $tested_method->list($credentials);

        // THEN
        $this->assertDatabaseCount(BlcaUser::class, 4);
        $this->assertInstanceOf(LengthAwarePaginator::class, $response);
        $this->assertInstanceOf(BlcaUser::class, $response->items()[0]);
        $this->assertCount(3, $response->items());
    }

    /**
     * @test
     */
    public function listOrders_responseSearchById_oneMatches()
    {
        // GIVEN
        $user_one = $this->createUserAndBe();
        $warehouse = $this->createWarehouse();
        $this->attachUserWarehouse($user_one, $warehouse);

        $userOne = $this->createUser('user_one@bloomec.ca');
        $userTwo = $this->createUser('user_two@bloomec.ca');
        $this->attachUserWarehouse($userOne, $warehouse);
        $this->attachUserWarehouse($userTwo, $warehouse);

        $this->createUser('user_four@bloomec.ca');

        $user_id = $userOne->id;

        $credentials = $this->makeCredentials(['id' => (string) $user_id]);

        /** @var UserQueryService $tested_method */
        $tested_method = $this->app->make(UserQueryService::class);

        // WHEN
        $response = $tested_method->list($credentials);

        // THEN
        $this->assertDatabaseCount(BlcaUser::class, 4);
        $this->assertEquals($user_id, $response->items()[0]->id);
        $this->assertCount(1, $response);
    }

    /**
     * @test
     */
    public function listOrders_responseSearchById_searchUserNotFromAuth_noMatches()
    {
        // GIVEN
        $user_one = $this->createUserAndBe();
        $warehouse = $this->createWarehouse();
        $this->attachUserWarehouse($user_one, $warehouse);

        $userOne = $this->createUser('user_one@bloomec.ca');
        $userTwo = $this->createUser('user_two@bloomec.ca');
        $this->attachUserWarehouse($userOne, $warehouse);
        $this->attachUserWarehouse($userTwo, $warehouse);

        $wrongUser = $this->createUser('user_four@bloomec.ca');

        $user_id = $wrongUser->id;

        $credentials = $this->makeCredentials(['id' => (string) $user_id]);

        /** @var UserQueryService $tested_method */
        $tested_method = $this->app->make(UserQueryService::class);

        // WHEN
        $response = $tested_method->list($credentials);

        // THEN
        $this->assertDatabaseCount(BlcaUser::class, 4);
        $this->assertCount(0, $response);
    }
}