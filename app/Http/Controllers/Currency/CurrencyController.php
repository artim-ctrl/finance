<?php

declare(strict_types = 1);

namespace App\Http\Controllers\Currency;

use App\Http\Controllers\Controller;
use App\Http\Resources\Currency\CurrencyCollection;
use App\Models\Currency;

final class CurrencyController extends Controller
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
