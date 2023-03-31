<?php

namespace Tests\Feature\Http\Controllers\Admin;

use App\Models\User;
use Illuminate\Foundation\Testing\Concerns\InteractsWithAuthentication;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class UserControllerTest extends TestCase
{
    use RefreshDatabase, InteractsWithAuthentication, WithFaker;

    public function testItCanListUsersAndApplySearchFilters(): void
    {
        User::factory(15)->create();
        User::factory(2)->create([
            'is_marketing' => true,
        ]);

        $user = $this->createPredictableAdminUser();
        $this->actingAs($user);

        $response = $this->getJson(route('api.admin.users.listing', [
            'limit' => 5,
            'page' => 1,
            'marketing' => 1,
        ]));

        $response->assertStatus(200)
            ->assertJson([
                'current_page' => 1,
                'per_page' => 5,
                'to' => 5,
            ]);

        $this->assertGreaterThanOrEqual(2, count($response->json('data')));
    }

    public function testCannotAccessRouteIfUserIsNotAdmin(): void
    {
        $user = $this->createPredictableRegularUser();
        $this->actingAs($user);

        $response = $this->getJson(route('api.admin.users.listing', [
            'limit' => 5,
            'page' => 1,
            'marketing' => 1,
        ]));

        $response->assertStatus(403)
            ->assertJson([
                'error' => 'Cannot access resource',
            ]);
    }
}
