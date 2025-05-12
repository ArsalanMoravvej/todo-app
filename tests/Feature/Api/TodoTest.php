<?php

use App\Models\Todo;
use App\Models\User;
use Illuminate\Auth\AuthenticationException;
use Laravel\Sanctum\Sanctum;

test('Home Route', function () {
    $response = $this->get('/');
    $response->assertStatus(200);
});

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

describe('Authenticated API Access', function () {
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
                    'title' => 'New Todo',
                    'description' => 'Todo description',
                    'status' => 'todo'
                ]
            ]);

        $this->assertDatabaseHas('todos', [
            'title' => 'New Todo',
            'user_id' => $this->user->id
        ]);
    });
});
