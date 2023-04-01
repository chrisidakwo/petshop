<?php

namespace Tests\Feature\Http\Controllers;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Testing\FileFactory;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class FileControllerTest extends TestCase
{
    use RefreshDatabase;

    public function testItUploadsFileAndCreateANewFileRecord(): void
    {
        $user = $this->createPredictableAdminUser();

        $this->actingAs($user);

        Storage::fake('public');

        $file = (new FileFactory)->image('test_image.png');
        $hashName = $file->hashName();

        $response = $this->postJson(route('api.file.upload'), [
            'file' => $file,
        ]);

        $response->assertStatus(201)
            ->assertJson([
                'data' => [
                    'type' => 'image/png',
                ]
            ]);

        $this->assertSame("public/pet-shop/{$hashName}", $response->json('data.path'));

        Storage::disk('public')->assertExists("pet-shop/{$hashName}");
    }
}
