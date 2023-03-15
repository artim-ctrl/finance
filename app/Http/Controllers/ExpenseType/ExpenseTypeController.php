<?php

namespace App\Http\Controllers\ExpenseType;

use App\Exceptions\ExpenseType\ExpenseTypeAlreadyExistsException;
use App\Http\Controllers\Controller;
use App\Http\Requests\ExpenseType\StoreData;
use App\Http\Resources\ExpenseType\ExpenseTypeCollection;
use App\Http\Resources\ExpenseType\ExpenseTypeResource;
use App\Models\ExpenseType;
use Illuminate\Http\Request;

class ExpenseTypeController extends Controller
{
    public function index(Request $request): ExpenseTypeCollection
    {
        $expenseTypes = ExpenseType::query()
            ->whereNull('user_id')
            ->orWhere('user_id', $request->user()->id)
            ->get()->all();

        return ExpenseTypeCollection::make($expenseTypes);
    }

    public function store(StoreData $data): ExpenseTypeResource
    {
        if (
            ExpenseType::query()
                ->where('user_id', auth()->id())
                ->where('name', $data->name)
                ->exists()
        ) {
            throw new ExpenseTypeAlreadyExistsException('Expense type with name ['.$data->name.'] already exists.');
        }

        $validated = array_merge($data->all(), ['user_id' => auth()->id()]);

        $expenseType = ExpenseType::create($validated);

        return ExpenseTypeResource::make($expenseType);
    }
}
