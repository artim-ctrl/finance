<?php

namespace App\Services\Currency;

use Illuminate\Support\Facades\Facade;

/**
 * @method static float rubToTry(float $amount)
 * @method static float rubToUsd(float $amount)
 * @method static float usdToTry(float $amount)
 * @method static float usdToRub(float $amount)
 * @method static float tryToUsd(float $amount)
 * @method static float tryToRub(float $amount)
 *
 * @see CurrencyManager
 */
class Currency extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return CurrencyManager::class;
    }
}
