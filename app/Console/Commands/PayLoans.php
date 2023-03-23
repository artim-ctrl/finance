<?php

namespace App\Console\Commands;

use App\Models\Balance;
use App\Models\Loan;
use App\Repositories\Balance\History\BalanceHistoryRepository;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;

class PayLoans extends Command
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

    public function __construct(protected BalanceHistoryRepository $balanceHistoryRepository)
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $date = now();

        $query = Loan::query()
            ->where('DAYOFMONTH("first_payment")', $date->day)
            ->orderBy('id');

        $query->chunk(10000, function (Collection $chunk) {
            $chunk->each(function (Loan $loan) {
                /** @var Balance $balance */
                $balance = Balance::query()
                    ->where('user_id', $loan->user_id)
                    ->where('currency_id', $loan->currency_id);

                $amountFrom = $balance->amount;

                $balance->update(['amount' => $balance->amount - $loan->amount]);

                $this->balanceHistoryRepository->createByLoan(
                    balance: $balance,
                    amountFrom: $amountFrom,
                    loan: $loan,
                    doneAt: $balance->updated_at,
                );
            });
        });
    }
}
