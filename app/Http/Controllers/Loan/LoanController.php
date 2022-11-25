<?php

namespace App\Http\Controllers\Loan;

use App\Http\Controllers\Controller;
use App\Http\Requests\Loan\StoreRequest;
use App\Http\Resources\Loan\LoanCollection;
use App\Http\Resources\Loan\LoanResource;
use App\Models\Loan;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LoanController extends Controller
{
    public function index(Request $request): LoanCollection
    {
        $loans = Loan::query()->where('user_id', $request->user()->id)->get()->all();

        return LoanCollection::make($loans);
    }

    public function store(StoreRequest $request): LoanResource
    {
        $validated = $request->validated();

        $validated = array_merge($validated, ['user_id' => $request->user()->id]);

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
