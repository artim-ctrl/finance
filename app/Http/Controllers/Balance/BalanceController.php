<?php

namespace App\Http\Controllers\Balance;

use App\Http\Controllers\Controller;
use App\Http\Requests\Balance\StoreRequest;
use App\Http\Requests\Balance\UpdateRequest;
use App\Http\Resources\Balance\BalanceCollection;
use App\Http\Resources\Balance\BalanceResource;
use App\Models\Balance;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BalanceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return BalanceCollection
     */
    public function index(Request $request): BalanceCollection
    {
        /** @var array<Balance> $balances */
        $balances = Balance::query()
            ->where('user_id', $request->user()->id)
            ->get()->all();

        return BalanceCollection::make($balances);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreRequest $request
     * @return BalanceResource
     */
    public function store(StoreRequest $request): BalanceResource
    {
        $validated = array_merge($request->validated(), ['user_id' => $request->user()->id]);

        /** @var Balance $balance */
        $balance = Balance::create($validated);

        return BalanceResource::make($balance);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateRequest $request
     * @param int $id
     * @return BalanceResource
     */
    public function update(UpdateRequest $request, int $id): BalanceResource
    {
        /** @var Balance $balance */
        $balance = Balance::findOrFail($id);
        if ($balance->user_id !== $request->user()->id) {
            throw (new ModelNotFoundException())->setModel(get_class($balance), $id);
        }

        $balance->update([
            'amount' => $request->input('amount'),
        ]);

        return BalanceResource::make($balance);
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
        /** @var Balance $balance */
        $balance = Balance::findOrFail($id);
        if ($balance->user_id !== $request->user()->id) {
            throw (new ModelNotFoundException())->setModel(get_class($balance), $id);
        }

        $balance->forceDelete();

        return response()->json([
            'status' => 'ok',
        ]);
    }
}
