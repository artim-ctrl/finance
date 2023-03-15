<?php

namespace App\Http\Controllers\Balance;

use App\Http\Controllers\Controller;
use App\Http\Requests\Balance\StoreData;
use App\Http\Requests\Balance\UpdateData;
use App\Http\Resources\Balance\BalanceCollection;
use App\Http\Resources\Balance\BalanceResource;
use App\Models\Balance;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;

class BalanceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return BalanceCollection
     */
    public function index(): BalanceCollection
    {
        /** @var array<int, Balance> $balances */
        $balances = Balance::query()
            ->where('user_id', auth()->id())
            ->get()->all();

        return BalanceCollection::make($balances);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreData $data
     * @return BalanceResource
     */
    public function store(StoreData $data): BalanceResource
    {
        $validated = array_merge($data->all(), ['user_id' => auth()->id()]);

        /** @var Balance $balance */
        $balance = Balance::create($validated);

        return BalanceResource::make($balance);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateData $data
     * @param int $id
     * @return BalanceResource
     */
    public function update(UpdateData $data, int $id): BalanceResource
    {
        /** @var Balance $balance */
        $balance = Balance::findOrFail($id);
        if ($balance->user_id !== auth()->id()) {
            throw (new ModelNotFoundException())->setModel(get_class($balance), $id);
        }

        $balance->update([
            'amount' => $data->amount,
        ]);

        return BalanceResource::make($balance);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function destroy(int $id): JsonResponse
    {
        /** @var Balance $balance */
        $balance = Balance::findOrFail($id);
        if ($balance->user_id !== auth()->id()) {
            throw (new ModelNotFoundException())->setModel(get_class($balance), $id);
        }

        $balance->forceDelete();

        return response()->json([
            'status' => 'ok',
        ]);
    }
}
