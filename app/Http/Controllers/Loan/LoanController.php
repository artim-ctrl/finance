<?php

namespace App\Http\Controllers\Loan;

use App\Http\Controllers\Controller;
use App\Http\Requests\Loan\StoreData;
use App\Http\Resources\Loan\LoanCollection;
use App\Http\Resources\Loan\LoanResource;
use App\Models\Loan;
use Illuminate\Http\JsonResponse;

class LoanController extends Controller
{
    public function index(): LoanCollection
    {
        $loans = Loan::query()->where('user_id', auth()->id())->get()->all();

        return LoanCollection::make($loans);
    }

    public function store(StoreData $data): LoanResource
    {
        $validated = array_merge($data->all(), ['user_id' => auth()->id()]);

        /** @var Loan $loan */
        $loan = Loan::create($validated);

        return LoanResource::make($loan);
    }

    public function destroy(int $id): JsonResponse
    {
        /** @var Loan $loan */
        $loan = Loan::findOrFail($id);

        $loan->forceDelete();

        return response()->json([
            'status' => 'ok',
        ]);
    }
}
