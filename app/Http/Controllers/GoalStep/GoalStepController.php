<?php

declare(strict_types = 1);

namespace App\Http\Controllers\GoalStep;

use App\Http\Controllers\Controller;
use App\Http\Requests\GoalStep\StoreData;
use App\Http\Requests\GoalStep\UpdateData;
use App\Http\Resources\Goal\GoalCollection;
use App\Http\Resources\GoalStep\GoalStepResource;
use App\Models\Goal;
use App\Models\GoalStep;
use Illuminate\Http\JsonResponse;

final class GoalStepController extends Controller
{
    public function index(int $goalId): GoalCollection
    {
        $goalSteps = GoalStep::leftJoin('goals', 'goal_steps.goal_id', '=', 'goals.id')
            ->whereGoalId($goalId)
            ->where(column: 'goals.user_id', operator: auth()->id())
            ->get();

        return GoalCollection::make($goalSteps);
    }

    public function store(StoreData $data, int $goalId): GoalStepResource
    {
        $goal = Goal::whereId($goalId)->whereUserId(auth()->id())->firstOrFail();

        $goalStep = $goal->steps()->create(
            attributes: $data->additional([
                'amount' => null,
            ])->all(),
        );

        return GoalStepResource::make($goalStep);
    }

    public function update(UpdateData $data, int $goalId, int $id): GoalStepResource
    {
        $goal = Goal::whereId($goalId)->whereUserId(auth()->id())->firstOrFail();

        $goalStep = GoalStep::whereId($id)->whereGoalId($goal->id)->firstOrFail();

        $goalStep->update(['amount' => $data->amount]);

        return GoalStepResource::make($goalStep);
    }

    public function destroy(int $goalId, int $id): JsonResponse
    {
        $goal = Goal::whereId($goalId)->whereUserId(auth()->id())->firstOrFail();

        $goalStep = GoalStep::whereId($id)->whereGoalId($goal->id)->firstOrFail();

        $goalStep->forceDelete();

        return response()->json([
            'status' => 'ok',
        ]);
    }
}
