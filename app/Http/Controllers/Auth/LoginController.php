<?php

declare(strict_types = 1);

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginData;
use App\Http\Resources\User\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

final class LoginController extends Controller
{
    public function __invoke(LoginData $data): JsonResponse
    {
        if (! Auth::attempt($data->only('email', 'password')->all())) {
            throw ValidationException::withMessages([
                'email' => ['Email or password is incorrect'],
            ]);
        }

        /** @var User $user */
        $user = auth()->user();

        $token = $user->createToken($data->tokenName);

        // TODO: use "additional" method
        return response()->json([
            'token' => $token->plainTextToken,
            'user' => UserResource::make($user),
        ]);
    }
}
