<?php

declare(strict_types = 1);

namespace App\Repositories\Balance\History;

use App\Enums\BalanceHistory\ActionEnum;
use App\Enums\BalanceHistory\EntityTypeEnum;
use App\Models\Balance;
use App\Models\BalanceHistory;
use App\Models\Exchange;
use App\Models\Income;
use App\Models\Loan;
use Carbon\Carbon;

final readonly class BalanceHistoryRepository
{
    public function createByExpense(
        float $amountFrom,
        float $amountTo,
        int $balanceId,
        int $expenseId,
        ?Carbon $doneAt = null,
    ): BalanceHistory {
        return BalanceHistory::create([
            'action' => ActionEnum::MINUS,
            'balance_id' => $balanceId,
            'amount_from' => $amountFrom,
            'amount_to' => $amountTo,
            'entity_type' => EntityTypeEnum::EXPENSES,
            'entity_id' => $expenseId,
            'done_at' => $doneAt ?? now(),
        ]);
    }

    public function createByIncome(
        Balance $balance,
        float $amountFrom,
        Income $income,
        ?Carbon $doneAt = null,
    ): BalanceHistory {
        return BalanceHistory::create([
            'action' => ActionEnum::PLUS,
            'balance_id' => $balance->id,
            'amount_from' => $amountFrom,
            'amount_to' => $balance->amount,
            'entity_type' => EntityTypeEnum::INCOMES,
            'entity_id' => $income->id,
            'done_at' => $doneAt ?? now(),
        ]);
    }

    public function createByExchangeMinus(
        Balance $balance,
        float $amountFrom,
        Exchange $exchange,
        ?Carbon $doneAt = null,
    ): BalanceHistory {
        return BalanceHistory::create([
            'action' => ActionEnum::MINUS,
            'balance_id' => $balance->id,
            'amount_from' => $amountFrom,
            'amount_to' => $balance->amount,
            'entity_type' => EntityTypeEnum::EXCHANGES,
            'entity_id' => $exchange->id,
            'done_at' => $doneAt ?? now(),
        ]);
    }

    public function createByExchangePlus(
        Balance $balance,
        float $amountFrom,
        Exchange $exchange,
        ?Carbon $doneAt = null,
    ): BalanceHistory {
        return BalanceHistory::create([
            'action' => ActionEnum::PLUS,
            'balance_id' => $balance->id,
            'amount_from' => $amountFrom,
            'amount_to' => $balance->amount,
            'entity_type' => EntityTypeEnum::EXCHANGES,
            'entity_id' => $exchange->id,
            'done_at' => $doneAt ?? now(),
        ]);
    }

    public function createByLoan(
        Balance $balance,
        float $amountFrom,
        Loan $loan,
        ?Carbon $doneAt = null,
    ): BalanceHistory {
        return BalanceHistory::create([
            'action' => ActionEnum::MINUS,
            'balance_id' => $balance->id,
            'amount_from' => $amountFrom,
            'amount_to' => $balance->amount,
            'entity_type' => EntityTypeEnum::LOANS,
            'entity_id' => $loan->id,
            'done_at' => $doneAt ?? now(),
        ]);
    }

    public function forceDeleteByBalanceId(int $balanceId): bool
    {
        $deleted = BalanceHistory::whereBalanceId($balanceId)->forceDelete();

        return 0 !== $deleted;
    }

    public function forceDeleteByIncome(int $incomeId): bool
    {
        $deleted = BalanceHistory::query()
            ->whereEntityType(EntityTypeEnum::INCOMES)
            ->whereEntityId($incomeId)
            ->forceDelete();

        return 0 !== $deleted;
    }

    public function forceDeleteByLoan(int $loanId): bool
    {
        $deleted = BalanceHistory::query()
            ->whereEntityType(EntityTypeEnum::LOANS)
            ->whereEntityId($loanId)
            ->forceDelete();

        return 0 !== $deleted;
    }
}
