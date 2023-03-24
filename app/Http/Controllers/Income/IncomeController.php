<?php

namespace App\Http\Controllers\Income;

use App\Http\Controllers\Controller;
use App\Http\Requests\Income\StoreData;
use App\Http\Resources\Income\IncomeCollection;
use App\Http\Resources\Income\IncomeResource;
use App\Models\Income;
use App\Repositories\Balance\History\BalanceHistoryRepository;
use Illuminate\Http\JsonResponse;

class IncomeController extends Controller
{
    public function index(): IncomeCollection
    {
        $incomes = Income::query()->where('user_id', auth()->id())->get()->all();

        return IncomeCollection::make($incomes);
    }

    public function store(StoreData $data): IncomeResource
    {
        $validated = array_merge($data->all(), ['user_id' => auth()->id()]);

        /** @var Income $income */
        $income = Income::create($validated);

        return IncomeResource::make($income);
    }

    public function destroy(int $id, BalanceHistoryRepository $balanceHistoryRepository): JsonResponse
    {
        /** @var Income $income */
        $income = Income::findOrFail($id);

        $income->forceDelete();

        $balanceHistoryRepository->forceDeleteByIncome($income->id);

        return response()->json([
            'status' => 'ok',
        ]);
    }
}
