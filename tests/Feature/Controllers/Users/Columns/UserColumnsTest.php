<?php

namespace Tests\Feature\Controllers\Users\Columns;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class UserColumnsTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * @test
     */
    public function set_success_response200()
    {
        // GIVEN
        $user_one = $this->createUserAndBe();
        $warehouse = $this->createWarehouse();
        $this->attachUserWarehouse($user_one, $warehouse);
        $data = [
            'model' => 'User',
            'columns' => json_encode([
                'id',
                'name',
                'created_at'
            ])
        ];

        // WHEN
        $response = $this->withHeaders(['Accept' => 'application/json'])
            ->json('post', route('user.column.store'), $data);

        // THEN
        $this->assertEquals(200, $response->getStatusCode());
    }
}