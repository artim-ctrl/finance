<?php

declare(strict_types = 1);

namespace App\Http\Controllers\Balance;

use App\Http\Controllers\Controller;
use App\Http\Requests\Balance\StoreData;
use App\Http\Requests\Balance\UpdateData;
use App\Http\Resources\Balance\BalanceCollection;
use App\Http\Resources\Balance\BalanceResource;
use App\Models\Balance;
use App\Repositories\Balance\History\BalanceHistoryRepository;
use Illuminate\Http\JsonResponse;
use Response;

final class BalanceController extends Controller
{
    public function index(): BalanceCollection
    {
        return BalanceCollection::make(Balance::whereUserId(auth()->id())->get());
    }

    public function show(Balance $balance): BalanceResource
    {
        $balance->load(relations: 'history');

        return BalanceResource::make($balance);
    }

    public function store(StoreData $data): BalanceResource
    {
        $balance = Balance::create(
            attributes: $data->additional(['user_id' => auth()->id()])->all(),
        );

        return BalanceResource::make($balance);
    }

    public function update(UpdateData $data, Balance $balance): BalanceResource
    {
        $balance->update(['amount' => $data->amount]);

        return BalanceResource::make($balance);
    }

    public function destroy(Balance $balance, BalanceHistoryRepository $balanceHistoryRepository): JsonResponse
    {
        $balance->forceDelete();

        // TODO: do I need this ?
        $balanceHistoryRepository->forceDeleteByBalanceId($balance->id);

        return Response::jsonNoContent();
    }
}
