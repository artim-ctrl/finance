<?php

declare(strict_types = 1);

namespace App\Http\Controllers\Loan;

use App\Http\Controllers\Controller;
use App\Http\Requests\Loan\StoreData;
use App\Http\Resources\Loan\LoanCollection;
use App\Http\Resources\Loan\LoanResource;
use App\Models\Loan;
use App\Repositories\Balance\History\BalanceHistoryRepository;
use Illuminate\Http\JsonResponse;

final class LoanController extends Controller
{
    public function index(): LoanCollection
    {
        $loans = Loan::whereUserId(auth()->id())->get();

        return LoanCollection::make($loans);
    }

    public function store(StoreData $data): LoanResource
    {
        $loan = Loan::create(
            attributes: $data->additional(['user_id' => auth()->id()])->all(),
        );

        return LoanResource::make($loan);
    }

    public function destroy(int $id, BalanceHistoryRepository $balanceHistoryRepository): JsonResponse
    {
        $loan = Loan::findOrFail($id);

        $loan->forceDelete();

        $balanceHistoryRepository->forceDeleteByLoan($loan->id);

        return response()->json([
            'status' => 'ok',
        ]);
    }
}
