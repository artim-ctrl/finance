<?php

declare(strict_types = 1);

namespace App\Http\Controllers\Income;

use App\Http\Controllers\Controller;
use App\Http\Requests\Income\StoreData;
use App\Http\Resources\Income\IncomeCollection;
use App\Http\Resources\Income\IncomeResource;
use App\Models\Income;
use App\Repositories\Balance\History\BalanceHistoryRepository;
use Illuminate\Http\JsonResponse;

final class IncomeController extends Controller
{
    public function index(): IncomeCollection
    {
        $incomes = Income::whereUserId(auth()->id())->get();

        return IncomeCollection::make($incomes);
    }

    public function store(StoreData $data): IncomeResource
    {
        $income = Income::create(
            attributes: $data->additional(['user_id' => auth()->id()])->all(),
        );

        return IncomeResource::make($income);
    }

    public function destroy(int $id, BalanceHistoryRepository $balanceHistoryRepository): JsonResponse
    {
        $income = Income::findOrFail($id);

        $income->forceDelete();

        $balanceHistoryRepository->forceDeleteByIncome($income->id);

        return response()->json([
            'status' => 'ok',
        ]);
    }
}
