<?php

declare(strict_types = 1);

namespace App\Console\Commands;

use App\Models\Balance;
use App\Models\Loan;
use App\Repositories\Balance\History\BalanceHistoryRepository;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;

final class PayLoans extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'loans:pay';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Pay loans';

    /**
     * Execute the console command.
     */
    public function __invoke(): void
    {
        $date = now();

        $query = Loan::where('DAYOFMONTH("first_payment")', $date->day)
            ->orderBy('id');

        $query->chunk(10000, static function (Collection $chunk) {
            $chunk->each(static function (Loan $loan) {
                $balance = Balance::whereUserId($loan->user_id)
                    ->whereCurrencyId($loan->currency_id)
                    ->firstOrFail();

                $amountFrom = $balance->amount;

                $balance->update(['amount' => $balance->amount - $loan->amount]);

                app(abstract: BalanceHistoryRepository::class)->createByLoan(
                    balance: $balance,
                    amountFrom: $amountFrom,
                    loan: $loan,
                    doneAt: $balance->updated_at,
                );
            });
        });
    }
}
