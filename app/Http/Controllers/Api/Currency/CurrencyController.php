<?php

declare(strict_types = 1);

namespace App\Http\Controllers\Api\Currency;

use App\Http\Resources\Currency\CurrencyCollection;
use App\Models\Currency;

final readonly class CurrencyController
{
    /**
     * Display a listing of the resource.
     *
     * @return CurrencyCollection
     */
    public function index(): CurrencyCollection
    {
        return CurrencyCollection::make(Currency::all());
    }
}
