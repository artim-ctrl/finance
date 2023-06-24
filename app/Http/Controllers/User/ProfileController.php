<?php

declare(strict_types = 1);

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\Profile\UpdateData;
use App\Http\Resources\User\UserResource;
use App\Models\User;

final class ProfileController extends Controller
{
    public function show(): UserResource
    {
        /** @var User $user */
        $user = auth()->user();

        return UserResource::make($user);
    }

    public function update(UpdateData $data): UserResource
    {
        /** @var User $user */
        $user = auth()->user();

        $user->update(['name' => $data->name]);

        return UserResource::make($user);
    }
}
