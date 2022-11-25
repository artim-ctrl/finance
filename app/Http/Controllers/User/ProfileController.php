<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\Profile\UpdateRequest;
use App\Http\Resources\User\UserResource;
use App\Models\User;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    /**
     * @param Request $request
     * @return UserResource
     */
    public function show(Request $request): UserResource
    {
        return UserResource::make($request->user());
    }

    /**
     * @param UpdateRequest $request
     * @return UserResource
     */
    public function update(UpdateRequest $request): UserResource
    {
        $validated = $request->validated();

        /** @var User $user */
        $user = $request->user();

        $user->update(['name' => $validated['name']]);

        return UserResource::make($user);
    }
}
