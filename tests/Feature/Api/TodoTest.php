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
    $this->user = User::factory()->create();
    $this->otherUser = User::factory()->create();

    $this->todos = Todo::factory()->count(3)->create([
        'user_id' => $this->user->id
    ]);

    $this->otherUserTodos = Todo::factory()->count(2)->create([
        'user_id' => $this->otherUser->id
    ]);

    Sanctum::actingAs($this->user);
});

test('user can only see their own todos', function () {
    $response = $this->getJson('/api/v1/todos');

    $response->assertStatus(200)
        ->assertJsonCount(3, 'data')
        ->assertJsonStructure([
            'data' => [
                '*' => ['id', 'title', 'status', 'priority']
            ],
            'links',
            'meta'
        ]);
});

test('user can create a todo', function () {
    $todoData = [
        'title' => 'New Todo',
        'description' => 'Todo description'
    ];

    $response = $this->postJson('/api/v1/todos', $todoData);

    $response->assertStatus(201)
        ->assertJson([
            'data' => [
                'title' => 'New Todo'
            ]
        ]);

    $this->assertDatabaseHas('todos', [
        'title' => 'New Todo',
        'user_id' => $this->user->id
    ]);
});



//test('guests cannot access todos api', function () {
//    $this->expectException(AuthenticationException::class);
//    $this->withHeader('Accept', 'application/json')
//        ->get('/api/v1/todos')
//        ->assertUnauthorized();
//});
