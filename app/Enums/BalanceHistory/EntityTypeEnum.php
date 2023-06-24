<?php

declare(strict_types = 1);

namespace App\Enums\BalanceHistory;

enum EntityTypeEnum: string
{
    case EXPENSES = 'expenses';
    case EXCHANGES = 'exchanges';
    case LOANS = 'loans';
    case INCOMES = 'incomes';
}
