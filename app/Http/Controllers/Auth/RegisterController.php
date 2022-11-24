<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Resources\User\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class RegisterController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function __invoke(Request $request): JsonResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email',
            'password' => 'required|string|min:8|confirmed',
            'password_confirmation' => 'required|string|min:8',
            'token-name' => 'required|string',
        ]);

        /** @var User $user */
        $user = User::create([
            ...$request->only(['name', 'email']),
            'password' => Hash::make($request->input('password')),
        ]);

        $token = $user->createToken($request->input('token-name'));

        return response()->json([
            'token' => $token->plainTextToken,
            'user' => UserResource::make($user),
        ]);
    }
}
