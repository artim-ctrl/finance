<?php

namespace App\Http\Controllers\ExpenseType;

use App\Exceptions\ExpenseType\ExpenseTypeAlreadyExistsException;
use App\Http\Controllers\Controller;
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

    public function store(Request $request): ExpenseTypeResource
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        if (
            ExpenseType::query()
                ->where('user_id', $request->user()->id)
                ->where('name', $validated['name'])
                ->exists()
        ) {
            throw new ExpenseTypeAlreadyExistsException('Expense type with name ['.$validated['name'].'] already exists.');
        }

        $validated = array_merge($validated, ['user_id' => $request->user()->id]);

        $expenseType = ExpenseType::create($validated);

        return ExpenseTypeResource::make($expenseType);
    }
}
