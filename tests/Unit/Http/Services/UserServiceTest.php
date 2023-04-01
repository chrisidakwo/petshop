<?php

namespace Tests\Unit\Http\Services;

use App\Http\Services\UserService;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserServiceTest extends TestCase
{
    use RefreshDatabase;

    protected UserService $userService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->userService = resolve(UserService::class);
    }

    public function testItCorrectlyAppliesFiltersWhenListingUsers(): void
    {
        $this->createPredictableRegularUser([
            'first_name' => 'Test User 1',
            'email' => 'test.user1@email.com',
            'is_marketing' => 1,
        ]);

        $this->createPredictableRegularUser([
            'first_name' => 'Test User 2',
            'email' => 'test.user2@email.com',
            'is_marketing' => 1,
        ]);

        User::factory(5)->create();

        $result = $this->userService->list([
            'first_name' => 'test',
            'marketing' => 1,
        ], 1, 5, 'first_name', true);

        $this->assertCount(2, $result);
        $this->assertEquals('Test User 2', $result->items()[0]->first_name);
        $this->assertEquals('Test User 1', $result->items()[1]->first_name);
    }
}
