<?php /** @noinspection PhpMultipleClassDeclarationsInspection */

namespace App\Http\Controllers\Api\V1;

use App\Filters\V1\TodoQueryFilter;
use App\Http\Controllers\Controller;
use App\Http\Requests\V1\GetTodosRequest;
use App\Http\Requests\V1\StoreTodoRequest;
use App\Http\Requests\V1\UpdateTodoRequest;
use App\Http\Resources\V1\TodoCollection;
use App\Http\Resources\V1\TodoResource;
use App\Models\Todo;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 *  APIs for managing todos resources
 *
 * @group Todos Management
 */
class TodoController extends Controller
{
    /**
     * Get All Todos
     *
     * Returns a list of the current user's Todos.
     *
     * @authenticated
     * @apiResourceCollection  App\Http\Resources\V1\TodoCollection
     * @apiResourceModel App\Models\Todo
     */
    public function index(GetTodosRequest $request, TodoQueryFilter $filter): TodoCollection
    {

        $filtered_todos = $filter->apply(Todo::query());
        $paginated_todos = $filtered_todos
            ->paginate($request->query('limit') ?? 15)
            ->appends($request->query());
        return new TodoCollection($paginated_todos);
    }

    /**
     * Get a Todo given the ID
     *
     * Returns a specific Todo belonging to the current user given its ID.
     *
     * @authenticated
     * @queryParam userIncluded optional boolean Includes user's information in results if passed as true. Example: false
     * @apiResource  App\Http\Resources\V1\TodoResource
     * @apiResourceModel App\Models\Todo
     */
    public function show(Todo $todo, Request $request): TodoResource
    {
        if ($request->has('userIncluded')) {
            $todo = $todo->loadMissing('user');
        }
        return new TodoResource($todo);
    }

    /**
     * Post a new Todo
     *
     * Returns the newly created Todo
     *
     * @authenticated
     * @apiResource  App\Http\Resources\V1\TodoResource
     * @apiResourceModel App\Models\Todo
     */
    public function store(StoreTodoRequest  $request): TodoResource
    {
        $todo = $request->user()->todos()->create($request->validated());
        return new TodoResource($todo->refresh());
    }

    /**
     * Update a Todo given the id
     *
     * Returns the Updated Todo
     *
     * @param UpdateTodoRequest $request
     * @param Todo $todo
     * @return TodoResource
     */
    public function update(UpdateTodoRequest $request, Todo $todo): TodoResource
    {
        $todo->update($request->validated());
        return new TodoResource($todo);
    }

    public function destroy(Todo $todo): JsonResponse
    {
        $todo->delete();
        return response()->json(null, 204);
    }
}
