<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginData;
use App\Http\Resources\User\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Laravel\Sanctum\NewAccessToken;

class LoginController extends Controller
{
    public function __invoke(LoginData $data): JsonResponse
    {
        if (! Auth::attempt($data->only('email', 'password')->all())) {
            return response()->json([
                'error' => 'Credentials are wrong',
            ], 400);
        }

        $user = User::query()
            ->where('email', $data->email)
            ->first();

        /** @var NewAccessToken $token */
        $token = $user->createToken($data->tokenName);

        return response()->json([
            'token' => $token->plainTextToken,
            'user' => UserResource::make($user),
        ]);
    }
}
