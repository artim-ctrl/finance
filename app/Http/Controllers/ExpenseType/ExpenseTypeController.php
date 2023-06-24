<?php

declare(strict_types = 1);

namespace App\Http\Controllers\ExpenseType;

use App\Http\Controllers\Controller;
use App\Http\Requests\ExpenseType\StoreData;
use App\Http\Resources\ExpenseType\ExpenseTypeCollection;
use App\Http\Resources\ExpenseType\ExpenseTypeResource;
use App\Models\ExpenseType;

final class ExpenseTypeController extends Controller
{
    public function index(): ExpenseTypeCollection
    {
        $expenseTypes = ExpenseType::whereNull(columns: 'user_id')
            ->orWhere(column: 'user_id', operator: auth()->id())
            ->get();

        return ExpenseTypeCollection::make($expenseTypes);
    }

    public function store(StoreData $data): ExpenseTypeResource
    {
        $expenseType = ExpenseType::create(attributes: $data->additional(['user_id' => auth()->id()])->all());

        return ExpenseTypeResource::make($expenseType);
    }
}
