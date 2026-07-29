<?php

use App\Models\User;
use App\Services\OcrService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

test('guest cannot access ocr api', function () {
    $response = $this->postJson('/api/ocr/read-serial');

    $response->assertStatus(401);
});

test('ocr api requires an image file', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)
        ->postJson('/api/ocr/read-serial', []);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['image']);
});

test('ocr api successfully returns read serial text from service', function () {
    Storage::fake('tmp');
    $user = User::factory()->create();
    $fakeImage = UploadedFile::fake()->image('serial_label.jpg', 600, 400);

    // Mock OcrService to return expected serial without relying on system binary during automated unit test
    $mockService = Mockery::mock(OcrService::class);
    $mockService->shouldReceive('readSerial')
        ->once()
        ->with(Mockery::type(UploadedFile::class))
        ->andReturn('BRJ123456');

    $this->app->instance(OcrService::class, $mockService);

    $response = $this->actingAs($user)
        ->postJson('/api/ocr/read-serial', [
            'image' => $fakeImage,
        ]);

    $response->assertOk()
        ->assertJson([
            'status' => 'success',
            'text' => 'BRJ123456',
        ]);
});

test('ocr api handles service exception gracefully and returns error json', function () {
    Storage::fake('tmp');
    $user = User::factory()->create();
    $fakeImage = UploadedFile::fake()->image('serial_label.jpg', 600, 400);

    $mockService = Mockery::mock(OcrService::class);
    $mockService->shouldReceive('readSerial')
        ->once()
        ->andThrow(new RuntimeException('Tesseract binary not found'));

    $this->app->instance(OcrService::class, $mockService);

    $response = $this->actingAs($user)
        ->postJson('/api/ocr/read-serial', [
            'image' => $fakeImage,
        ]);

    $response->assertStatus(500)
        ->assertJson([
            'status' => 'error',
        ]);
});
