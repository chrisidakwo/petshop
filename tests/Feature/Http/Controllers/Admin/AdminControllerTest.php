<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Controllers\Admin;

use App\Http\Services\UserService;
use App\Models\File;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Mockery;
use Tests\TestCase;

class AdminControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function testItCreatesANewUserWhenRequestDataIsValid()
    {
        $user = $this->createPredictableAdminUser();

        $this->actingAs($user);

        $requestData = [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john.doe@email.com',
            'password' => 'admin',
            'password_confirmation' => 'admin',
            'address' => 'Plot 27, Haile Selassie Street, Asokoro',
            'phone_number' => '09039018902',
            'avatar' => File::factory()->create()->uuid,
        ];

        $response = $this->postJson(route('api.admin.store'), $requestData);

        $response->assertStatus(201)
            ->assertJsonPath('data.first_name', 'John')
            ->assertJsonPath('data.email', 'john.doe@email.com')
            ->assertJsonPath('data.is_marketing', 0);

        $response->assertJsonStructure([
            'data' => [
                'uuid',
                'is_marketing',
                'token',
            ]
        ]);
    }

    public function testItValidatesOnInvalidRequestData(): void
    {
        $user = $this->createPredictableAdminUser();

        $this->actingAs($user);

        $requestData = [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john.doe',
            'password' => 'admin',
            'address' => 'Plot 27, Haile Selassie Street, Asokoro',
            'phone_number' => '09039018902',
            'avatar' => 'invalid-uuid-string-23390-0399',
        ];

        $response = $this->postJson(route('api.admin.store'), $requestData);

        $response->assertStatus(422)
            ->assertJsonValidationErrors([
                'email' => ['The email field must be a valid email address.'],
                'password' => ['The password field confirmation does not match.'],
                'avatar' => ['The avatar field must be a valid UUID.'],
            ]);
    }

    public function testThatOnlyAdminUsersCanCreateAnAdminUser(): void
    {
        $user = $this->createPredictableRegularUser();

        $this->actingAs($user);

        $response = $this->postJson(route('api.admin.store'), []);

        $response->assertStatus(403)
            ->assertJson([
                'error' => 'Cannot access resource',
            ]);
    }

    public function testItUpdatesUserRecord(): void
    {
        $authUser = $this->createPredictableAdminUser();

        File::factory()->create([
            'uuid' => '98d34b4b-7a94-4916-bc0e-34b0191b8216',
        ]);

        $user = $this->createPredictableAdminUser([
            'email' => 'user@gmail.com',
            'first_name' => 'John',
            'last_name' => 'Doe',
            'avatar' => '98d34b4b-7a94-4916-bc0e-34b0191b8216',
            'phone_number' => '07039010023',
            'is_marketing' => 0,
        ]);

        $this->assertEquals('98d34b4b-7a94-4916-bc0e-34b0191b8216', $user->avatar);

        $this->actingAs($authUser);

        $response = $this->putJson(route('api.admin.update', $user->uuid), [
            'first_name' => 'Name',
            'last_name' => 'Changed',
            'email' => 'another.email@gmail.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'address' => $this->faker->streetAddress(),
            'phone_number' => '09039028901',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'uuid',
                'email',
                'token',
            ]);

        // avatar and is_marketing should not change
        $this->assertSame($user->avatar, $response->json('avatar'));
        $this->assertSame($user->is_marketing, $response->json('is_marketing'));

        $this->assertNotSame($user->first_name, $response->json('first_name'));
        $this->assertNotSame($user->last_name, $response->json('last_name'));
        $this->assertNotSame($user->phone_number, $response->json('phone_number'));
    }

    public function testItShouldErrorWhenRequestDataContainsPreExistingEmailAddress(): void
    {
        $admin1 = $this->createPredictableAdminUser([
            'email' => 'admin1@gmail.com'
        ]);

        $admin2 = $this->createPredictableAdminUser([
            'email' => 'admin2@gmail.com'
        ]);

        $this->createPredictableAdminUser([
            'email' => 'admin3@gmail.com'
        ]);

        $this->actingAs($admin2);

        $response = $this->putJson(route('api.admin.update', $admin1->uuid), [
            'first_name' => 'Name',
            'last_name' => 'Changed',
            'email' => 'admin3@gmail.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'address' => $this->faker->streetAddress(),
            'phone_number' => '09039028901',
        ]);

        $response->assertStatus(422)->assertJsonValidationErrors([
            'email' => [
                'The email has already been taken',
            ]
        ]);
    }

    public function testItDeletesUser(): void
    {
        $authUser = $this->createPredictableAdminUser();

        $user = $this->createPredictableAdminUser([
            'email' => 'delete.user@yahoo.com',
        ]);

        $this->assertDatabaseCount('users', 2);

        $this->actingAs($authUser);

        $response = $this->deleteJson(route('api.admin.delete', $user->uuid));

        $this->assertDatabaseCount('users', 1);
        $this->assertDatabaseMissing('users', [
            'email' => 'delete.user@yahoo.com',
        ]);

        $response->assertStatus(200);
    }

    public function testItShouldNotDeleteAuthUser(): void
    {
        $authUser = $this->createPredictableAdminUser();

        $this->actingAs($authUser);

        $response = $this->deleteJson(route('api.admin.delete', $authUser->uuid));

        $response->assertStatus(403)
            ->assertJson([
                'error' => 'This action is unauthorized.',
            ]);
    }

    public function testItShouldNotDeleteDefaultAdmin(): void
    {
        $user = User::factory()->create([
            'first_name' => 'Admin',
            'last_name' => 'Buckhill',
            'email' => 'admin@buckhill.co.uk',
            'password' => bcrypt('admin'),
            'is_admin' => 1,
        ])->refresh();

        $authUser = $this->createPredictableAdminUser();

        $this->actingAs($authUser);

        $response = $this->deleteJson(route('api.admin.delete', $user->uuid));

        $response->assertStatus(403)
            ->assertJson([
                'error' => 'This action is unauthorized.',
            ]);
    }

    public function testItReturnsErrorIfDeleteActionFails()
    {
        $userService = Mockery::mock(UserService::class);
        $this->app->bind(UserService::class, fn () => $userService);

        $authUser = $this->createPredictableAdminUser();

        $user = $this->createPredictableAdminUser([
            'email' => 'user@outlook.com',
        ]);

        $userService->shouldReceive('delete')
            ->once()
            ->andReturn(false);

        $this->actingAs($authUser);

        $response = $this->deleteJson(route('api.admin.delete', $user->uuid));

        $response->assertStatus(500)
            ->assertJson([
                'error' => 'Could not delete user',
            ]);
    }
}
