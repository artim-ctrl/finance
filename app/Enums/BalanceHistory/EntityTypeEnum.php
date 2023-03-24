<?php

namespace App\Enums\BalanceHistory;

enum EntityTypeEnum: string
{
    case EXPENSES = 'expenses';
    case EXCHANGES = 'exchanges';
    case LOANS = 'loans';
    case INCOMES = 'incomes';
}
