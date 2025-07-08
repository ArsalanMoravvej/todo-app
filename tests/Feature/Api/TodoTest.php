    <?php

    use App\Models\Todo;
    use App\Models\User;
    use Laravel\Sanctum\Sanctum;
    describe('Authenticated & Unauthorized API Access', function () {
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

        test('user cannot view another users todo', function () {
            $otherTodo = $this->otherUserTodos[0];

            $response = $this->getJson("/api/v1/todos/{$otherTodo->id}");

            $response->assertStatus(403);
        });

        test('user cannot update another users todo', function () {
            $otherTodo = $this->otherUserTodos[0];

            $response = $this->patchJson("/api/v1/todos/{$otherTodo->id}", [
                'title' => 'Hacked Title'
            ]);

            $response->assertStatus(403);
        });

        test('user cannot delete another users todo', function () {
            $otherTodo = $this->otherUserTodos[0];

            $response = $this->deleteJson("/api/v1/todos/{$otherTodo->id}");

            $response->assertStatus(403);
        });

    });
    describe('Authenticated & Authorized API Access', function () {
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

        test('user can only see their own set of todos', function () {
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

        test('user can view their own todo', function () {
            $todo = $this->todos[0];

            $response = $this->getJson("/api/v1/todos/{$todo->id}");

            $response->assertStatus(200)
                ->assertJson([
                    'data' => [
                        'id' => $todo->id,
                        'title' => $todo->title,
                        'description' => $todo->description,
                        'status' => $todo->status,
                    ]
                ]);
        });
        test('user can patch update their own todo', function () {
            $todo = $this->todos[0];

            $response = $this->patchJson("/api/v1/todos/{$todo->id}", [
                'title' => 'Updated Title',
                'description' => 'Updated description'
            ]);

            $response->assertStatus(200)
                ->assertJson([
                    'data' => [
                        'title' => 'Updated Title',
                        'description' => 'Updated description'
                    ]
                ]);

            $this->assertDatabaseHas('todos', [
                'id' => $todo->id,
                'title' => 'Updated Title',
                'description' => 'Updated description'
            ]);
        });

        test('user can delete their own todo', function () {
            $todo = $this->todos[0];

            $response = $this->deleteJson("/api/v1/todos/{$todo->id}");

            $response->assertStatus(204);

            $this->assertSoftDeleted('todos', [
                'id' => $todo->id
            ]);
        });

        test('filtering todos works', function () {
            // Create todos with different statuses
            $this->user->todos()->delete();
            Todo::factory()->create([
                'user_id' => $this->user->id,
                'status' => 'done',
                'priority' => 2
            ]);

            $response = $this->getJson('/api/v1/todos?status=done');

            $response->assertStatus(200)
                ->assertJsonCount(1, 'data');

            $response = $this->getJson('/api/v1/todos?priority=2');

            $response->assertStatus(200)
                ->assertJsonCount(1, 'data');
        });

        test('can include user relationship', function () {
            $response = $this->getJson('/api/v1/todos?userIncluded');

            $response->assertStatus(200)
                ->assertJsonPath('data.0.user.id', $this->user->id);
        });

    });

