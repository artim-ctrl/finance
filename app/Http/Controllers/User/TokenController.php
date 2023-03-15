<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Resources\User\PersonalAccessTokenResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Sanctum\PersonalAccessToken;

class TokenController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        /** @var User $user */
        $user = $request->user();

        $tokens = $user->tokens()->get();

        /** @var PersonalAccessToken $personalAccessToken */
        $personalAccessToken = $user->currentAccessToken();

        return response()->json([
            'data' => [
                /** @phpstan-ignore-next-line  */
                'current_token_id' => $personalAccessToken->id,
                'tokens' => PersonalAccessTokenResource::collection($tokens),
            ],
        ]);
    }

    public function delete(int $id): JsonResponse
    {
        /** @var User $user */
        $user = Auth::user();

        $user->tokens()->where('id', $id)->delete();

        return response()->json([
            'status' => 'ok',
        ]);
    }
}
