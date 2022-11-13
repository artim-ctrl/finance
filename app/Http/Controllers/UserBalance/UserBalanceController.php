<?php

namespace App\Http\Controllers\UserBalance;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserBalance\UserBalanceCollection;
use App\Http\Resources\UserBalance\UserBalanceResource;
use App\Models\UserBalance;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserBalanceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return UserBalanceCollection
     */
    public function index(Request $request): UserBalanceCollection
    {
        $userBalances = UserBalance::query()
            ->where('user_id', $request->user()->id)
            ->get()->all();

        return UserBalanceCollection::make($userBalances);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return UserBalanceResource
     */
    public function store(Request $request): UserBalanceResource
    {
        $validated = $request->validate([
            'currency_id' => 'required|exists:currencies,id',
            'amount' => 'required|numeric',
        ]);

        $validated = array_merge($validated, ['user_id' => $request->user()->id]);

        $userBalance = UserBalance::create($validated);

        return UserBalanceResource::make($userBalance);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param int $id
     * @return UserBalanceResource
     */
    public function update(Request $request, int $id): UserBalanceResource
    {
        /** @var UserBalance $userBalance */
        $userBalance = UserBalance::findOrFail($id);
        if ($userBalance->user_id !== $request->user()->id) {
            throw (new ModelNotFoundException())->setModel(get_class($userBalance), $id);
        }

        $request->validate([
            'amount' => 'required|numeric',
        ]);

        $userBalance->update([
            'amount' => $request->input('amount'),
        ]);

        return UserBalanceResource::make($userBalance);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     */
    public function destroy(Request $request, int $id): JsonResponse
    {
        /** @var UserBalance $userBalance */
        $userBalance = UserBalance::findOrFail($id);
        if ($userBalance->user_id !== $request->user()->id) {
            throw (new ModelNotFoundException())->setModel(get_class($userBalance), $id);
        }

        $userBalance->forceDelete();

        return response()->json([
            'status' => 'ok',
        ]);
    }
}
