<?php

declare(strict_types = 1);

namespace App\Http\Controllers\Balance;

use App\Exceptions\Balance\BalanceAlreadyExistsException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Balance\StoreData;
use App\Http\Requests\Balance\UpdateData;
use App\Http\Resources\Balance\BalanceCollection;
use App\Http\Resources\Balance\BalanceResource;
use App\Models\Balance;
use App\Repositories\Balance\History\BalanceHistoryRepository;
use Illuminate\Http\JsonResponse;

final class BalanceController extends Controller
{
    public function index(): BalanceCollection
    {
        $balances = Balance::whereUserId(auth()->id())->get();

        return BalanceCollection::make($balances);
    }

    public function show(int $id): BalanceResource
    {
        $balance = Balance::with(relations: 'history')
            ->whereId($id)
            ->whereUserId(auth()->id())
            ->firstOrFail();

        return BalanceResource::make($balance);
    }

    /**
     * @throws BalanceAlreadyExistsException
     */
    public function store(StoreData $data): BalanceResource
    {
        if (
            Balance::whereUserId(auth()->id())
                ->whereCurrencyId($data->currencyId)
                ->exists()
        ) {
            throw new BalanceAlreadyExistsException('Balance already exists');
        }

        $balance = Balance::create(
            attributes: $data->additional(['user_id' => auth()->id()])->all(),
        );

        return BalanceResource::make($balance);
    }

    public function update(UpdateData $data, int $id): BalanceResource
    {
        // TODO: test it
        $balance = Balance::whereUserId(auth()->id())->findOrFail($id);

        $balance->update([
            'amount' => $data->amount,
        ]);

        return BalanceResource::make($balance);
    }

    public function destroy(int $id, BalanceHistoryRepository $balanceHistoryRepository): JsonResponse
    {
        /** @var Balance $balance */
        $balance = Balance::whereUserId(auth()->id())->findOrFail($id);

        $balance->forceDelete();

        // TODO: do I need this ?
        $balanceHistoryRepository->forceDeleteByBalanceId($balance->id);

        return response()->json([
            'status' => 'ok',
        ]);
    }
}
