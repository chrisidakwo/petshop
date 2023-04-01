<?php

namespace Tests\Feature\Http\Controllers\User;

use App\Models\File;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserControllerTest extends TestCase
{
    use RefreshDatabase;

    public function testItShouldCreateANewUserWhenRequestDataIsValid(): void
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

        $response = $this->postJson(route('api.user.store'), $requestData);

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

    public function testItUpdatesUserRecord(): void
    {
        $user = $this->createPredictableRegularUser();

        $this->actingAs($user);

        $requestData = [
            'first_name' => 'JohnCyo',
            'last_name' => 'Doe',
            'email' => 'john.doe@email.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'address' => 'Plot 27, Haile Selassie Street, Asokoro',
            'phone_number' => '09039000000',
            'avatar' => File::factory()->create()->uuid,
        ];

        $response = $this->putJson(route('api.user.update', $user->uuid), $requestData);

        $response->assertStatus(200);

        $this->assertTrue($user->wasChanged('first_name'));
        $this->assertTrue($user->wasChanged('phone_number'));
        $this->assertNotNull($response->json('data.avatar'));

        $response->assertJsonStructure([
            'data' => [
                'uuid',
                'token',
            ]
        ]);
    }
}
