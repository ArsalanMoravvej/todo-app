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

class AuthController extends Controller
{
    public function register(RegisterUserRequest $request): UserResource
    {
        $user = User::create($request->validated());
        $token = $user->createToken($user->email)->plainTextToken;

        return (new UserResource($user))->additional([
            'access_token' => $token,
        ]);
    }

    /** @noinspection PhpParamsInspection */
    public function login(LoginUserRequest $request): UserResource|JsonResponse
    {
        $credentials = $request->validated();
        $user = User::where('email', $credentials['email'])->first();
        $CorrectPassword = Hash::check($credentials['password'], $user->password);

        if (! $CorrectPassword)
        {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        $user->tokens()->delete();
        $token = $user->createToken($user->email)->plainTextToken;
        return (new UserResource($user))->additional([
            'access_token' => $token,
        ]);
    }
    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'Logged out']);
    }
}
