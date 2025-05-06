<?php /** @noinspection PhpMultipleClassDeclarationsInspection */

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\V1\UserResource;
use App\Models\User;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $fields = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = User::create($fields);
        $token = $user->createToken($user->email)->plainTextToken;

        return (new UserResource($user))->additional([
            'access_token' => $token,
        ]);
    }
    public function login(Request $request)
    {
        $user = User::where('id', 3)->first();
        $token = $user->createToken($user->name)->plainTextToken;

        return response()->json(['token' => $token]);
    }
    public function logout(Request $request)
    {
        return $request->user()->currentAccessToken()->delete();
    }
}
