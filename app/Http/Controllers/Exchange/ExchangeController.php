<?php

namespace App\Http\Controllers\Exchange;

use App\Exceptions\Balance\BalanceNotEnoughException;
use App\Exceptions\Balance\BalanceNotFoundException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Exchange\StoreData;
use App\Http\Resources\Exchange\ExchangeCollection;
use App\Http\Resources\Exchange\ExchangeResource;
use App\Models\Balance;
use App\Models\Exchange;
use App\Repositories\Balance\History\BalanceHistoryRepository;
use Illuminate\Support\Facades\DB;
use Throwable;

class ExchangeController extends Controller
{
    public function index(): ExchangeCollection
    {
        $exchanges = Exchange::query()
            ->where('user_id', auth()->id())
            ->get();

        return ExchangeCollection::make($exchanges);
    }

    /**
     * @param StoreData $data
     * @param BalanceHistoryRepository $balanceHistoryRepository
     * @return ExchangeResource
     * @throws Throwable
     */
    public function store(StoreData $data, BalanceHistoryRepository $balanceHistoryRepository): ExchangeResource
    {
        try {
            DB::beginTransaction();

            // TODO: refactor to services and repositories
            // TODO: refactor to use increase method in Eloquent
            $userId = auth()->id();

            /** @var Balance|null $balanceFrom */
            $balanceFrom = Balance::query()
                ->where('id', $data->balanceIdFrom)
                ->where('user_id', $userId)
                ->first();
            if (null === $balanceFrom) {
                throw new BalanceNotFoundException('User\'s balance FROM not found.');
            }

            /** @var Balance|null $balanceTo */
            $balanceTo = Balance::query()
                ->where('id', $data->balanceIdTo)
                ->where('user_id', $userId)
                ->first();
            if (null === $balanceTo) {
                throw new BalanceNotFoundException('User\'s balance TO not found.');
            }

            if ($balanceFrom->amount < $data->amountFrom) {
                throw new BalanceNotEnoughException('There are not enough funds on the balance.');
            }

            $amountFromMinus = $balanceFrom->amount;
            $amountFromPlus = $balanceTo->amount;

            $balanceFrom->update(['amount' => $balanceFrom->amount - $data->amountFrom]);
            $balanceTo->update(['amount' => $balanceTo->amount + $data->amountTo]);

            $validated = array_merge($data->all(), ['user_id' => $userId]);

            /** @var Exchange $exchange */
            $exchange = Exchange::create($validated);

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
