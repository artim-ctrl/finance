<?php

namespace App\Http\Controllers\Exchange;

use App\Http\Controllers\Controller;
use App\Http\Resources\Exchange\ExchangeCollection;
use App\Http\Resources\Exchange\ExchangeResource;
use App\Models\Exchange;
use Illuminate\Http\Request;

class ExchangeController extends Controller
{
    public function index(Request $request): ExchangeCollection
    {
        $exchanges = Exchange::query()
            ->where('user_id', $request->user()->id)
            ->get();

        return ExchangeCollection::make($exchanges);
    }

    public function store(Request $request): ExchangeResource
    {
        $validated = $request->validate([
            'currency_id_from' => 'required|integer|exists:currencies,id',
            'amount_from' => 'required|numeric',
            'currency_id_to' => 'required|integer|exists:currencies,id',
            'amount_to' => 'required|numeric',
            'exchanged_at' => 'required|date',
        ]);

        $validated = array_merge($validated, ['user_id' => $request->user()->id]);

        $exchange = Exchange::create($validated);

        return ExchangeResource::make($exchange);
    }
}
