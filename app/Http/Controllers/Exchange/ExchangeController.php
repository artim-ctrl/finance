<?php

declare(strict_types = 1);

namespace App\Http\Controllers\Exchange;

use App\Exceptions\Balance\BalanceNotEnoughException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Exchange\StoreData;
use App\Http\Resources\Exchange\ExchangeCollection;
use App\Http\Resources\Exchange\ExchangeResource;
use App\Models\Balance;
use App\Models\Exchange;
use App\Repositories\Balance\History\BalanceHistoryRepository;
use Illuminate\Support\Facades\DB;
use Throwable;

final class ExchangeController extends Controller
{
    public function index(): ExchangeCollection
    {
        $exchanges = Exchange::whereUserId(auth()->id())->get();

        return ExchangeCollection::make($exchanges);
    }

    /**
     * @throws Throwable
     */
    public function store(StoreData $data, BalanceHistoryRepository $balanceHistoryRepository): ExchangeResource
    {
        try {
            DB::beginTransaction();

            // TODO: refactor to services and repositories
            // TODO: refactor to use increase method in Eloquent
            $userId = auth()->id();

            $balanceFrom = Balance::whereId($data->balanceIdFrom)
                ->whereUserId($userId)
                ->firstOrFail();

            $balanceTo = Balance::whereId($data->balanceIdTo)
                ->whereUserId($userId)
                ->firstOrFail();

            if ($balanceFrom->amount < $data->amountFrom) {
                throw new BalanceNotEnoughException(message: 'There are not enough funds on the balance.');
            }

            $amountFromMinus = $balanceFrom->amount;
            $amountFromPlus = $balanceTo->amount;

            $balanceFrom->update(['amount' => $balanceFrom->amount - $data->amountFrom]);
            $balanceTo->update(['amount' => $balanceTo->amount + $data->amountTo]);

            $exchange = Exchange::create(attributes: $data->additional(['user_id' => $userId])->all());

            $balanceHistoryRepository->createByExchangeMinus(
                balance: $balanceFrom,
                amountFrom: $amountFromMinus,
                exchange: $exchange,
                doneAt: $exchange->exchanged_at,
            );

            $balanceHistoryRepository->createByExchangePlus(
                balance: $balanceTo,
                amountFrom: $amountFromPlus,
                exchange: $exchange,
                doneAt: $exchange->exchanged_at,
            );

            DB::commit();
        } catch (Throwable $exception) {
            DB::rollBack();

            throw $exception;
        }

        return ExchangeResource::make($exchange);
    }
}
