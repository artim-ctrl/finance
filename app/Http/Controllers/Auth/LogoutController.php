<?php

declare(strict_types = 1);

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Laravel\Sanctum\PersonalAccessToken;

final class LogoutController extends Controller
{
    public function __invoke(): JsonResponse
    {
        /** @var User $user */
        $user = auth()->user();

        /** @var PersonalAccessToken $personalAccessToken */
        $personalAccessToken = $user->currentAccessToken();

        $personalAccessToken->delete();

        return response()->json([
            'status' => 'ok',
        ]);
    }
}
