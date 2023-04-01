<?php

namespace Tests\Feature\Http\Controllers\Admin;

use App\Models\File;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminControllerTest extends TestCase
{
    use RefreshDatabase;

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
}
