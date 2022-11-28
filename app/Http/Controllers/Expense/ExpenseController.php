<?php

namespace App\Http\Controllers\Expense;

use App\Exceptions\Balance\BalanceNotEnoughException;
use App\Exceptions\Balance\BalanceNotFoundException;
use App\Exceptions\ExpenseType\ExpenseTypeNotExistsException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Expense\StoreRequest;
use App\Http\Resources\Expense\ExpenseCollection;
use App\Http\Resources\Expense\ExpenseResource;
use App\Models\Balance;
use App\Models\Expense;
use App\Models\ExpenseType;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Throwable;

class ExpenseController extends Controller
{
    public function index(Request $request): ExpenseCollection
    {
        $expenses = Expense::query()
            ->where('user_id', $request->user()->id)
            ->get()->all();

        return ExpenseCollection::make($expenses);
    }

    public function store(StoreRequest $request): ExpenseResource
    {
        $validated = $request->validated();

        DB::beginTransaction();

        try {
            if (
                ! ExpenseType::query()
                    ->where('id', $validated['expense_type_id'])
                    ->where(fn(Builder $query) => $query->whereNull('user_id')->orWhere('user_id', $request->user()->id))
                    ->exists()
            ) {
                throw new ExpenseTypeNotExistsException('Expense type not found.');
            }

            /** @var Balance $balance */
            $balance = Balance::query()
                ->where('id', $validated['balance_id'])
                ->where('user_id', $request->user()->id)
                ->first();
            if ($balance === null) {
                throw new BalanceNotFoundException('User\'s balance not found.'); // TODO: Change to config usage? I mean /lang directory
            }

            $validated = array_merge($validated, ['user_id' => $request->user()->id]);
            if ($validated['spent_at'] !== null && ! $validated['for_history']) {
                if ($balance->amount < $validated['amount']) {
                    throw new BalanceNotEnoughException('There are not enough funds on the balance.');
                }

                $balance->update([
                    'amount' => $balance->amount - $validated['amount'],
                ]);
            }

            /** @var Expense $expense */
            $expense = Expense::create($validated);

            DB::commit();
        } catch (Throwable $exception) {
            DB::rollBack();

            throw $exception;
        }

        return ExpenseResource::make($expense);
    }
}
