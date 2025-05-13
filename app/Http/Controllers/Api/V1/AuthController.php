<?php /** @noinspection PhpMultipleClassDeclarationsInspection */

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\V1\LoginUserRequest;
use App\Http\Requests\V1\RegisterUserRequest;
use App\Http\Resources\V1\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use function Laravel\Prompts\password;
use function Pest\Laravel\json;

/**
 *  APIs for managing todos resources
 *
 * @group Auth Management
 */
class AuthController extends Controller
{
    /**
     * Register
     *
     * Register the new user and return its access token.
     *
     * @unauthenticated
     * @apiResource App\Http\Resources\V1\UserResource
     * @apiResourceModel App\Models\User
     * @param RegisterUserRequest $request
     * @return UserResource
     */
    public function register(RegisterUserRequest $request): UserResource
    {
        $user = User::create($request->validated());
        $token = $user->createToken($user->email)->plainTextToken;

        return (new UserResource($user))->additional([
            'access_token' => $token,
        ]);
    }

    /** @noinspection PhpParamsInspection */
    /**
     * Login
     *
     * Log the user in and return its access token.
     *
     * @unauthenticated
     * @apiResource App\Http\Resources\V1\UserResource
     * @apiResourceModel App\Models\User
     * @param LoginUserRequest $request
     * @return UserResource | JsonResponse
    */
    public function login(LoginUserRequest $request): UserResource|JsonResponse
    {
        $credentials = $request->validated();
        $user = User::where('email', $credentials['email'])->first();
        $checkPassword = Hash::check($credentials['password'], $user->password);

        if (! $checkPassword)
        {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        $user->tokens()->delete();
        $token = $user->createToken($user->email)->plainTextToken;
        return (new UserResource($user))->additional([
            'access_token' => $token,
        ]);
    }

    /**
     * Logout
     *
     * Returns a list of the current user's todos.
     *
     * @authenticated
     * @response 200{
     *     "message" : "Logged out"
     * }
     * @param Request $request
     * @return JsonResponse
     */
    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'Logged out']);
    }
}
