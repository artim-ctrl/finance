<?php

namespace App\Http\Controllers\Balance;

use App\Http\Controllers\Controller;
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
        /** @var Balance[] $balances */
        $balances = Balance::query()
            ->where('user_id', $request->user()->id)
            ->get()->all();

        return BalanceCollection::make($balances);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return BalanceResource
     */
    public function store(Request $request): BalanceResource
    {
        $validated = $request->validate([
            'currency_id' => 'required|exists:currencies,id',
            'amount' => 'required|numeric',
        ]);

        $validated = array_merge($validated, ['user_id' => $request->user()->id]);

        /** @var Balance $balance */
        $balance = Balance::create($validated);

        return BalanceResource::make($balance);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param int $id
     * @return BalanceResource
     */
    public function update(Request $request, int $id): BalanceResource
    {
        /** @var Balance $balance */
        $balance = Balance::findOrFail($id);
        if ($balance->user_id !== $request->user()->id) {
            throw (new ModelNotFoundException())->setModel(get_class($balance), $id);
        }

        $request->validate([
            'amount' => 'required|numeric',
        ]);

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
