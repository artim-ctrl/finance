<?php

declare(strict_types = 1);

namespace App\Http\Controllers\Api\Auth;

use App\Http\Requests\Auth\RegisterData;
use App\Http\Resources\User\UserResource;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

final readonly class RegisterController
{
    public function __invoke(RegisterData $data): UserResource
    {
        /** @var User $user */
        $user = User::create([
            'name' => $data->name,
            'email' => $data->email,
            'password' => Hash::make($data->password),
        ]);

        $token = $user->createToken($data->tokenName);

        return UserResource::make($user)->additional(['data' => [
            'token' => $token->plainTextToken,
        ]]);
    }
}
