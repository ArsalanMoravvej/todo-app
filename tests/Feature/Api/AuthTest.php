<?php

use App\Models\User;
use Laravel\Sanctum\Sanctum;

test('users can register and receive token', function () {
    $userData = [
        'name' => 'Test User',
        'email' => 'test@example.com',
        'password' => 'password',
        'password_confirmation' => 'password'
    ];

    $response = $this->postJson('/api/v1/register', $userData);

    $response->assertStatus(201)
        ->assertJsonStructure([
            'data' => ['id', 'name', 'email'],
            'access_token'
        ]);

    $this->assertDatabaseHas('users', [
        'email' => 'test@example.com'
    ]);
});

test('users can login and receive token', function () {
    $user = User::factory()->create([
        'email' => 'test@example.com',
        'password' => bcrypt('password')
    ]);

    $response = $this->postJson('/api/v1/login', [
        'email' => 'test@example.com',
        'password' => 'password'
    ]);

    $response->assertStatus(200)
        ->assertJsonStructure([
            'data' => ['id', 'name', 'email'],
            'access_token'
        ]);
});

test('users cannot login with invalid password', function () {
    $user = User::factory()->create();

    $response = $this->postJson('/api/v1/login', [
        'email' => $user->email,
        'password' => 'wrong-password'
    ]);

    $response->assertStatus(401)
        ->assertJson(['message' => 'Invalid credentials']);
});

test('users can logout', function () {
    $user = User::factory()->create();
    Sanctum::actingAs($user);

    $response = $this->postJson('/api/v1/logout');

    $response->assertStatus(200)
        ->assertJson(['message' => 'Logged out']);

    $this->assertCount(0, $user->tokens);
});
