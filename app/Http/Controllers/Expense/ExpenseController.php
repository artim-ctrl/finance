<?php

declare(strict_types = 1);

namespace App\Http\Controllers\Expense;

use App\Exceptions\Balance\BalanceNotEnoughException;
use App\Exceptions\ExpenseType\ExpenseTypeNotExistsException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Expense\StoreData;
use App\Http\Resources\Expense\ExpenseCollection;
use App\Http\Resources\Expense\ExpenseResource;
use App\Models\Balance;
use App\Models\Expense;
use App\Models\ExpenseType;
use App\Repositories\Balance\History\BalanceHistoryRepository;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Throwable;

final class ExpenseController extends Controller
{
    public function index(): ExpenseCollection
    {
        $expenses = Expense::whereUserId(auth()->id())->get();

        return ExpenseCollection::make($expenses);
    }

    /**
     * @throws Throwable
     */
    public function store(StoreData $data, BalanceHistoryRepository $balanceHistoryRepository): ExpenseResource
    {
        DB::beginTransaction();

        try {
            $userId = auth()->id();

            if (
                ! ExpenseType::whereId($data->expenseTypeId)
                    /** @phpstan-ignore-next-line */
                    ->where(fn (Builder $query) => $query->whereNull(columns: 'user_id')->orWhere(column: 'user_id', operator: $userId))
                    ->exists()
            ) {
                throw new ExpenseTypeNotExistsException(message: 'Expense type not found.');
            }

            $balance = Balance::whereId($data->balanceId)
                ->whereUserId($userId)
                ->firstOrFail();

            $amountFrom = $balance->amount;

            if (! $data->forHistory) {
                if ($balance->amount < $data->amount) {
                    throw new BalanceNotEnoughException(message: 'There are not enough funds on the balance.');
                }

                // TODO: refactor to increase method in Eloquent
                $balance->update([
                    'amount' => $balance->amount - $data->amount,
                ]);
            }

            $expense = Expense::create(attributes: $data->additional(['user_id' => $userId])->all());

            $balanceHistoryRepository->createByExpense(
                amountFrom: $amountFrom,
                amountTo: $balance->amount,
                balanceId: $balance->id,
                expenseId: $expense->id,
                doneAt: $expense->created_at,
            );

            DB::commit();
        } catch (Throwable $exception) {
            DB::rollBack();

            throw $exception;
        }

        return ExpenseResource::make($expense);
    }
}
