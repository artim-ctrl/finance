<?php

namespace App\Console\Commands;

use App\Models\Income;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;

class IncreaseIncomes extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'incomes:increase';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check all incomes and increase';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(): int
    {
        $incomes = Income::query()->whereNotNull('increase_month');
        $incomes->chunk(10000, function (Collection $chunk) {
            $chunk->each(function (Income $income) {
                $income->update([
                    'amount' => $income->amount + $income->increase_amount,
                    'increase_month' => null,
                    'increase_amount' => null,
                ]);
            });
        });

        return Command::SUCCESS;
    }
}
