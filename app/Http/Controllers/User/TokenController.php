<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Resources\User\PersonalAccessTokenCollection;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TokenController extends Controller
{
    public function index(Request $request): PersonalAccessTokenCollection
    {
        /** @var User $user */
        $user = $request->user();

        $tokens = $user->tokens()->get();

        return PersonalAccessTokenCollection::make($tokens);
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
