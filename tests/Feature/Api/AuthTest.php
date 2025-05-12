<?php

use App\Models\User;
use App\Models\Todo;
use Laravel\Sanctum\Sanctum;

describe('Unauthenticated API Access', function () {
    test('guests cannot access todos index', function () {
        $response = $this->getJson('/api/v1/todos');
        $response->assertStatus(401);
    });

    test('guests cannot create todos', function () {
        $todoData = [
            'title' => 'New Todo',
            'description' => 'Todo description'
        ];

        $response = $this->postJson('/api/v1/todos', $todoData);
        $response->assertStatus(401);
    });

    test('guests cannot view specific todo', function () {
        $todo = Todo::factory()->create();

        $response = $this->getJson("/api/v1/todos/{$todo->id}");
        $response->assertStatus(401);
    });

    test('guests cannot update todo', function () {
        $todo = Todo::factory()->create();

        $response = $this->putJson("/api/v1/todos/{$todo->id}", [
            'title' => 'Updated Title'
        ]);
        $response->assertStatus(401);
    });

    test('guests cannot delete todo', function () {
        $todo = Todo::factory()->create();

        $response = $this->deleteJson("/api/v1/todos/{$todo->id}");
        $response->assertStatus(401);
    });
});


describe('Authentication API Tests', function () {
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
});
