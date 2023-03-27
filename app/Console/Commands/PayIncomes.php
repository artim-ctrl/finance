<?php

namespace App\Console\Commands;

use App\Models\Balance;
use App\Models\Income;
use App\Repositories\Balance\History\BalanceHistoryRepository;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;

class PayIncomes extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'incomes:pay';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Pay users\'s incomes';

    public function __construct(protected BalanceHistoryRepository $balanceHistoryRepository)
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     */
    public function __invoke(): void
    {
        $date = now();

        $query = Income::query()
            ->where('day_receiving', $date->day)
            ->orderBy('id');

        $query->chunk(10000, function (Collection $chunk) {
            $chunk->each(function (Income $income) {
                /** @var Balance $balance */
                $balance = Balance::query()
                    ->where('user_id', $income->user_id)
                    ->where('currency_id', $income->currency_id)
                    ->first();

                $amountFrom = $balance->amount;

                $balance->update(['amount' => $balance->amount + $income->amount]);

                $this->balanceHistoryRepository->createByIncome(
                    balance: $balance,
                    amountFrom: $amountFrom,
                    income: $income,
                    doneAt: $balance->updated_at,
                );
            });
        });
    }
}
