<?php

declare(strict_types = 1);

namespace App\Http\Controllers\Api\Auth;

use App\Http\Requests\Auth\LoginData;
use App\Http\Resources\User\UserResource;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

final readonly class LoginController
{
    public function __invoke(LoginData $data): UserResource
    {
        if (! Auth::attempt($data->only('email', 'password')->all())) {
            throw ValidationException::withMessages([
                'email' => ['Email or password is incorrect'],
            ]);
        }

        /** @var User $user */
        $user = auth()->user();

        $token = $user->createToken($data->tokenName);

        return UserResource::make($user)->additional(['data' => [
            'token' => $token->plainTextToken,
        ]]);
    }
}
