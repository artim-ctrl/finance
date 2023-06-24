<?php

declare(strict_types = 1);

namespace App\Console\Commands;

use App\Models\Balance;
use App\Models\Income;
use App\Repositories\Balance\History\BalanceHistoryRepository;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;

final class PayIncomes extends Command
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

    /**
     * Execute the console command.
     */
    public function __invoke(): void
    {
        $date = now();

        $query = Income::whereDayReceiving($date->day)->orderBy('id');

        $query->chunk(10000, static function (Collection $chunk) {
            $chunk->each(static function (Income $income) {
                $balance = Balance::whereUserId($income->user_id)
                    ->whereCurrencyId($income->currency_id)
                    ->firstOrFail();

                $amountFrom = $balance->amount;

                $balance->update(['amount' => $balance->amount + $income->amount]);

                app(abstract: BalanceHistoryRepository::class)->createByIncome(
                    balance: $balance,
                    amountFrom: $amountFrom,
                    income: $income,
                    doneAt: $balance->updated_at,
                );
            });
        });
    }
}
