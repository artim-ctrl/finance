<?php

namespace App\Http\Controllers\Expense;

use App\Exceptions\Balance\BalanceNotEnoughException;
use App\Exceptions\Balance\BalanceNotFoundException;
use App\Exceptions\ExpenseType\ExpenseTypeNotExistsException;
use App\Http\Controllers\Controller;
use App\Http\Resources\Expense\ExpenseCollection;
use App\Http\Resources\Expense\ExpenseResource;
use App\Models\Balance;
use App\Models\Expense;
use App\Models\ExpenseType;
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

    public function store(Request $request): ExpenseResource
    {
        $validated = $request->validate([ // TODO: move to request-class
            'name' => 'required|string|max:255',
            'expense_type_id' => 'required|integer',
            'balance_id' => 'required|integer',
            'amount' => 'required|numeric',
        ]);

        DB::beginTransaction();

        try {
            if (
                ! ExpenseType::query()
                    ->whereNull('user_id')
                    ->orWhere('user_id', $request->user()->id)
                    ->exists()
            ) {
                throw new ExpenseTypeNotExistsException('Expense type not found.');
            }

            /** @var Balance $balance */
            $balance = Balance::query()
                ->where('user_id', $request->user()->id)
                ->first();
            if ($balance === null) {
                throw new BalanceNotFoundException('User\'s balance not found.'); // TODO: Change to config usage?
            }

            if ($balance->amount < $validated['amount']) {
                throw new BalanceNotEnoughException('There are not enough funds on the balance.');
            }

            $validated = array_merge($validated, ['user_id' => $request->user()->id]);

            $balance->update([
                'amount' => $balance->amount - $validated['amount'],
            ]);

            /** @var Expense $expense */
            $expense = Expense::create($validated);
        } catch (Throwable $exception) {
            DB::rollBack();

            throw $exception;
        }

        DB::commit();

        return ExpenseResource::make($expense);
    }
}
