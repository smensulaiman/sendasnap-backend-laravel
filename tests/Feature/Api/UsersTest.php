<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('returns 401 when unauthenticated', function () {
    $response = $this->getJson('/api/v1/users');

    $response->assertStatus(401);
    $response->assertJson([
        'success' => false,
        'message' => 'Unauthenticated',
    ]);
});

it('returns empty users array when no users exist', function () {
    $user = User::factory()->create();
    $token = $user->createToken('test-token')->plainTextToken;

    $response = $this->withHeaders([
        'Authorization' => 'Bearer '.$token,
    ])->getJson('/api/v1/users');

    $response->assertSuccessful();
    $response->assertJson([
        'success' => true,
        'message' => 'Users retrieved successfully',
        'data' => [
            'users' => [],
        ],
    ]);
});

it('returns users array with records when authenticated', function () {
    $user = User::factory()->create();
    $token = $user->createToken('test-token')->plainTextToken;

    // Create additional users
    User::factory()->count(3)->create();

    $response = $this->withHeaders([
        'Authorization' => 'Bearer '.$token,
    ])->getJson('/api/v1/users');

    $response->assertSuccessful();
    $json = $response->json();

    expect($json)->toHaveKeys(['success', 'message', 'data', 'meta']);
    expect($json['success'])->toBeTrue();
    expect($json['message'])->toBe('Users retrieved successfully');
    expect($json['data'])->toHaveKey('users');
    expect($json['data']['users'])->toBeArray();
    expect($json['data']['users'])->toHaveCount(4); // 1 original + 3 created

    // Verify user structure
    $firstUser = $json['data']['users'][0];
    expect($firstUser)->toHaveKeys(['id', 'name', 'email', 'phone', 'role', 'created_at']);
    expect($firstUser)->not->toHaveKey('password');
});
