<?php

declare(strict_types = 1);

namespace App\Http\Controllers\Api\Auth;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Laravel\Sanctum\PersonalAccessToken;
use Response;

final readonly class LogoutController
{
    public function __invoke(): JsonResponse
    {
        /** @var User $user */
        $user = auth()->user();

        /** @var PersonalAccessToken $personalAccessToken */
        $personalAccessToken = $user->currentAccessToken();

        $personalAccessToken->delete();

        return Response::jsonNoContent();
    }
}
