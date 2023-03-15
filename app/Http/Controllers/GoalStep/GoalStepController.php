<?php

namespace App\Http\Controllers\GoalStep;

use App\Exceptions\Goal\GoalNotFoundException;
use App\Exceptions\GoalStep\GoalStepNotFoundException;
use App\Http\Controllers\Controller;
use App\Http\Requests\GoalStep\StoreData;
use App\Http\Requests\GoalStep\UpdateData;
use App\Http\Resources\Goal\GoalCollection;
use App\Http\Resources\GoalStep\GoalStepResource;
use App\Models\Goal;
use App\Models\GoalStep;
use Illuminate\Http\JsonResponse;

class GoalStepController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param int $goalId
     * @return GoalCollection
     */
    public function index(int $goalId): GoalCollection
    {
        $goalSteps = GoalStep::query()
            ->leftJoin('goals', 'goal_steps.goal_id', '=', 'goals.id')
            ->where('goal_id', $goalId)
            ->where('goals.user_id', auth()->id())
            ->get()->all();

        return GoalCollection::make($goalSteps);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreData $data
     * @param int $goalId
     * @return GoalStepResource
     */
    public function store(StoreData $data, int $goalId): GoalStepResource
    {
        if (! Goal::query()->where('id', $goalId)->where('user_id', auth()->id())->exists()) {
            throw new GoalNotFoundException('Goal not exist');
        }

        $validated = array_merge($data->all(), [
            'user_id' => auth()->id(),
            'goal_id' => $goalId,
            'amount' => null,
        ]);

        $goalStep = GoalStep::create($validated);

        return GoalStepResource::make($goalStep);
    }

    public function update(UpdateData $data, int $goalId, int $id): GoalStepResource
    {
        if (! Goal::query()->where('id', $goalId)->where('user_id', auth()->id())->exists()) {
            throw new GoalNotFoundException('Goal not exist');
        }

        /** @var GoalStep|null $goalStep */
        $goalStep = GoalStep::query()->where('id', $id)->first();
        if (null === $goalStep || $goalStep->goal_id !== $goalId) {
            throw new GoalStepNotFoundException('Goal step not found.');
        }

        $goalStep->update(['amount' => $data->amount]);

        return GoalStepResource::make($goalStep);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $goalId
     * @param int $id
     * @return JsonResponse
     */
    public function destroy(int $goalId, int $id): JsonResponse
    {
        if (! Goal::query()->where('id', $goalId)->where('user_id', auth()->id())->exists()) {
            throw new GoalNotFoundException('Goal not exist');
        }

        /** @var GoalStep|null $goalStep */
        $goalStep = GoalStep::query()->where('id', $id)->first();
        if (null === $goalStep || $goalStep->goal_id !== $goalId) {
            throw new GoalStepNotFoundException('Goal step not found.');
        }

        $goalStep->forceDelete();

        return response()->json([
            'status' => 'ok',
        ]);
    }
}
