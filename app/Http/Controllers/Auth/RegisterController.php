<?php

declare(strict_types = 1);

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterData;
use App\Http\Resources\User\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;

final class RegisterController extends Controller
{
    public function __invoke(RegisterData $data): JsonResponse
    {
        /** @var User $user */
        $user = User::create([
            ...$data->only('name', 'email')->all(),
            'password' => Hash::make($data->password),
        ]);

        $token = $user->createToken($data->tokenName);

        // TODO: use "additional" method
        return response()->json([
            'token' => $token->plainTextToken,
            'user' => UserResource::make($user),
        ]);
    }
}
