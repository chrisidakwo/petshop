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

        $response->assertStatus(200)
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
}
