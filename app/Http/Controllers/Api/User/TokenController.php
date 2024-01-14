<?php

declare(strict_types = 1);

namespace App\Http\Controllers\Api\User;

use App\Http\Resources\User\PersonalAccessTokenResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Laravel\Sanctum\PersonalAccessToken;

final readonly class TokenController
{
    public function index(): JsonResponse
    {
        /** @var User $user */
        $user = auth()->user();

        /** @var PersonalAccessToken $personalAccessToken */
        $personalAccessToken = $user->currentAccessToken();

        return response()->json([
            'data' => [
                'current_token_id' => $personalAccessToken->id,
                'tokens' => PersonalAccessTokenResource::collection($user->tokens),
            ],
        ]);
    }

    public function delete(int $id): JsonResponse
    {
        /** @var User $user */
        $user = auth()->user();

        $user->tokens()->whereId($id)->delete();

        return response()->json([
            'status' => 'ok',
        ]);
    }
}
