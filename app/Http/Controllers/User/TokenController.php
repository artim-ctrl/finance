<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Resources\User\PersonalAccessTokenResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TokenController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        /** @var User $user */
        $user = $request->user();

        $tokens = $user->tokens()->get();

        return response()->json([
            'data' => [
                'current_token_id' => $user->currentAccessToken()->id,
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
