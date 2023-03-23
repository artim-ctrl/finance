<?php

namespace App\Http\Controllers\Balance\History;

use App\Exceptions\Balance\BalanceNotFoundException;
use App\Http\Controllers\Controller;
use App\Http\Resources\Balance\History\HistoryCollection;
use App\Models\Balance;
use App\Models\BalanceHistory;

class HistoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param int $balanceId
     * @return HistoryCollection
     */
    public function index(int $balanceId): HistoryCollection
    {
        $userId = auth()->id();

        if (
            ! Balance::query()
                ->where('id', $balanceId)
                ->where('user_id', $userId)
                ->exists()
        ) {
            throw new BalanceNotFoundException('Balance not found');
        }

        /** @var array<int, BalanceHistory> $balances */
        $balances = BalanceHistory::query()
            ->leftJoin('balances', 'balance_id', '=', 'balances.id')
            ->where('balance_id', $balanceId)
            ->where('balances.user_id', $userId)
            ->get()->all();

        return HistoryCollection::make($balances);
    }
}
