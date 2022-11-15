<?php

namespace App\Http\Controllers\Exchange;

use App\Exceptions\Balance\BalanceNotEnoughException;
use App\Exceptions\Balance\BalanceNotFoundException;
use App\Http\Controllers\Controller;
use App\Http\Resources\Exchange\ExchangeCollection;
use App\Http\Resources\Exchange\ExchangeResource;
use App\Models\Balance;
use App\Models\Exchange;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Throwable;

class ExchangeController extends Controller
{
    public function index(Request $request): ExchangeCollection
    {
        $exchanges = Exchange::query()
            ->where('user_id', $request->user()->id)
            ->get();

        return ExchangeCollection::make($exchanges);
    }

    /**
     * TODO: try to use the request as dynamic-typed class
     *
     * @param Request $request
     * @return ExchangeResource
     */
    public function store(Request $request): ExchangeResource
    {
        $validated = $request->validate([
            'balance_id_from' => 'required|integer',
            'amount_from' => 'required|numeric',
            'balance_id_to' => 'required|integer',
            'amount_to' => 'required|numeric',
            'exchanged_at' => 'required|date',
        ]);

        try {
            DB::beginTransaction();

            // TODO: refactor to services and repositories
            /** @var User $user */
            $user = $request->user();

            /** @var Balance $balanceFrom */
            $balanceFrom = Balance::query()
                ->where('id', $validated['balance_id_from'])
                ->where('user_id', $user->id)
                ->get()->first();
            if ($balanceFrom === null) {
                throw new BalanceNotFoundException('User\'s balance FROM not found.');
            }

            /** @var Balance $balanceTo */
            $balanceTo = Balance::query()
                ->where('id', $validated['balance_id_to'])
                ->where('user_id', $user->id)
                ->get()->first();
            if ($balanceTo === null) {
                throw new BalanceNotFoundException('User\'s balance TO not found.');
            }

            if ($balanceFrom->amount < $validated['amount_from']) {
                throw new BalanceNotEnoughException('There are not enough funds on the balance.');
            }

            $balanceFrom->update(['amount' => $balanceFrom->amount - $validated['amount_from']]);
            $balanceTo->update(['amount' => $balanceTo->amount + $validated['amount_to']]);

            $validated = array_merge($validated, ['user_id' => $user->id]);
        } catch (Throwable $exception) {
            DB::rollBack();

            throw $exception;
        }

        DB::commit();

        /** @var Exchange $exchange */
        $exchange = Exchange::create($validated);

        return ExchangeResource::make($exchange);
    }
}
