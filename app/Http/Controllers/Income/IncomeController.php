<?php

namespace App\Http\Controllers\Income;

use App\Http\Controllers\Controller;
use App\Http\Resources\Income\IncomeCollection;
use App\Http\Resources\Income\IncomeResource;
use App\Models\Income;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class IncomeController extends Controller
{
    public function index(Request $request): IncomeCollection
    {
        $incomes = Income::query()->where('user_id', $request->user()->id)->get()->all();

        return IncomeCollection::make($incomes);
    }

    public function store(Request $request): IncomeResource
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'day_receiving' => 'required|integer|max:31',
            'currency_id' => 'required|exists:currencies,id',
            'amount' => 'required|numeric',
        ]);

        $validated = array_merge($validated, ['user_id' => $request->user()->id]);

        $income = Income::create($validated);

        return IncomeResource::make($income);
    }

    public function destroy(int $id): JsonResponse
    {
        /** @var Income $income */
        $income = Income::findOrFail($id);

        $income->forceDelete();

        return response()->json([
            'status' => 'ok',
        ]);
    }
}
