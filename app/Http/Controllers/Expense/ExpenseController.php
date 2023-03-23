<?php

namespace App\Http\Controllers\Expense;

use App\Exceptions\Balance\BalanceNotEnoughException;
use App\Exceptions\Balance\BalanceNotFoundException;
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

class ExpenseController extends Controller
{
    public function index(): ExpenseCollection
    {
        $expenses = Expense::query()
            ->where('user_id', auth()->id())
            ->get()->all();

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
                ! ExpenseType::query()
                    ->where('id', $data->expenseTypeId)
                    /** @phpstan-ignore-next-line */
                    ->where(fn (Builder $query) => $query->whereNull('user_id')->orWhere('user_id', $userId))
                    ->exists()
            ) {
                throw new ExpenseTypeNotExistsException('Expense type not found.');
            }

            /** @var Balance|null $balance */
            $balance = Balance::query()
                ->where('id', $data->balanceId)
                ->where('user_id', $userId)
                ->first();
            if (null === $balance) {
                throw new BalanceNotFoundException('User\'s balance not found.'); // TODO: Change to config usage? I mean /lang directory
            }

            $amountFrom = $balance->amount;

            $validated = array_merge($data->all(), ['user_id' => $userId]);
            if (! $data->forHistory) {
                if ($balance->amount < $data->amount) {
                    throw new BalanceNotEnoughException('There are not enough funds on the balance.');
                }

                // TODO: refactor to increase method in Eloquent
                $balance->update([
                    'amount' => $balance->amount - $data->amount,
                ]);
            }

            /** @var Expense $expense */
            $expense = Expense::create($validated);

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
