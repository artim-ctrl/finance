<?php

declare(strict_types = 1);

namespace App\Enums\BalanceHistory;

enum ActionEnum: string
{
    case MINUS = 'minus';
    case PLUS = 'plus';
}
