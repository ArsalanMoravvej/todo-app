<?php

use App\Models\Todo;
use App\Models\User;
use Illuminate\Auth\AuthenticationException;
use Laravel\Sanctum\Sanctum;

test('Home Route', function () {
    $response = $this->get('/');
    $response->assertStatus(200);
});


beforeEach(function () {
    // Run migrations
    $this->withoutExceptionHandling();

    // Create test users
    $this->user1 = User::factory()->create();
    $this->user2 = User::factory()->create();

    // Create test posts
    $this->posts1 = Todo::factory()->count(3)->create([
        'user_id' => $this->user1->id
    ]);

    $this->posts2 = Todo::factory()->count(2)->create([
        'user_id' => $this->user2->id
    ]);
});

test('can fetch all todos', function () {
    // Authenticate the user
    Sanctum::actingAs($this->user1);
    $response = $this->getJson('/api/v1/todos');

    // Assert
    $response->assertStatus(200)
        ->assertJsonCount(3)
        ->assertJsonStructure([
            'data' => [
                '*' => ['id', 'user_id', 'title', 'description', 'status', 'priority']
            ]
        ]);
});

test('guests cannot access todos api', function () {
    $this->expectException(AuthenticationException::class);
    $this->withHeader('Accept', 'application/json')
        ->get('/api/v1/todos')
        ->assertUnauthorized();
});

