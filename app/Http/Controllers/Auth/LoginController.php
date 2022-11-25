<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Resources\User\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Laravel\Sanctum\NewAccessToken;

class LoginController extends Controller
{
    public function __invoke(LoginRequest $request): JsonResponse
    {
        if (! Auth::attempt($request->only(['email', 'password']))) {
            return response()->json([
                'error' => 'Credentials are wrong',
            ], 400);
        }

        $user = User::query()
            ->where('email', $request->input('email'))
            ->first();

        /** @var NewAccessToken $token */
        $token = $user->createToken($request->input('token-name'));

        return response()->json([
            'token' => $token->plainTextToken,
            'user' => UserResource::make($user),
        ]);
    }
}
