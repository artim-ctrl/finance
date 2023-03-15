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
     * @return ExchangeResource
     * @throws Throwable
     */
    public function store(StoreData $data): ExchangeResource
    {
        try {
            DB::beginTransaction();

            // TODO: refactor to services and repositories
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

            $balanceFrom->update(['amount' => $balanceFrom->amount - $data->amountFrom]);
            $balanceTo->update(['amount' => $balanceTo->amount + $data->amountTo]);

            $validated = array_merge($data->all(), ['user_id' => $userId]);

            /** @var Exchange $exchange */
            $exchange = Exchange::create($validated);

            DB::commit();
        } catch (Throwable $exception) {
            DB::rollBack();

            throw $exception;
        }

        return ExchangeResource::make($exchange);
    }
}
