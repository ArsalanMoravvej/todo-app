<?php /** @noinspection PhpMultipleClassDeclarationsInspection */

namespace App\Http\Controllers\Api\V1;

use App\Filters\V1\TodoQueryFilter;
use App\Http\Controllers\Controller;
use App\Http\Requests\V1\StoreTodoRequest;
use App\Http\Requests\V1\UpdateTodoRequest;
use App\Http\Resources\V1\TodoCollection;
use App\Http\Resources\V1\TodoResource;
use App\Models\Todo;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TodoController extends Controller
{
    public function index(Request $request, TodoQueryFilter $filter): TodoCollection
    {

        $filtered_todos = $filter->apply(Todo::query());
        $paginated_todos = $filtered_todos
            ->paginate($request->query('limit') ?? 15)
            ->appends($request->query());
        return new TodoCollection($paginated_todos);
    }

    public function store(StoreTodoRequest  $request): TodoResource
    {
        $todo = Todo::create($request->validated());
        return new TodoResource($todo);
    }

    public function show(Todo $todo, Request $request): TodoResource
    {

        if ($request->has('userIncluded')) {
            return new TodoResource($todo->loadMissing('user'));
        }
        return new TodoResource($todo);
    }

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
