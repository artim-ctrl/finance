<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\Profile\UpdateData;
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
     * @param UpdateData $data
     * @return UserResource
     */
    public function update(UpdateData $data): UserResource
    {
        /** @var User $user */
        $user = auth()->user();

        $user->update(['name' => $data->name]);

        return UserResource::make($user);
    }
}
