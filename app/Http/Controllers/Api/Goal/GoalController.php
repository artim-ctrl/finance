<?php

declare(strict_types = 1);

namespace App\Http\Controllers\Api\Goal;

use App\Http\Requests\Goal\StoreData;
use App\Http\Resources\Goal\GoalCollection;
use App\Http\Resources\Goal\GoalResource;
use App\Models\Goal;
use Illuminate\Http\JsonResponse;

final readonly class GoalController
{
    public function index(): GoalCollection
    {
        $goals = Goal::whereUserId(auth()->id())
            ->with(relations: 'steps')
            ->get();

        return GoalCollection::make($goals);
    }

    public function show(int $id): GoalResource
    {
        /** @var Goal|null $goal */
        $goal = Goal::whereId($id)
            ->whereUserId(auth()->id())
            ->with(relations: 'steps')
            ->firstOrFail();

        return GoalResource::make($goal);
    }

    public function store(StoreData $data): GoalResource
    {
        $goal = Goal::create(
            attributes: $data->additional(['user_id' => auth()->id()])->all(),
        );

        return GoalResource::make($goal);
    }

    public function destroy(int $id): JsonResponse
    {
        $goal = Goal::findOrFail($id);

        $goal->forceDelete();

        return response()->json([
            'status' => 'ok',
        ]);
    }
}
