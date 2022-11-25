<?php

namespace App\Http\Controllers\GoalStep;

use App\Exceptions\Goal\GoalNotFoundException;
use App\Exceptions\GoalStep\GoalStepNotFoundException;
use App\Http\Controllers\Controller;
use App\Http\Requests\GoalStep\StoreRequest;
use App\Http\Resources\Goal\GoalCollection;
use App\Http\Resources\GoalStep\GoalStepResource;
use App\Models\Goal;
use App\Models\GoalStep;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class GoalStepController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @param int $goalId
     * @return GoalCollection
     */
    public function index(Request $request, int $goalId): GoalCollection
    {
        $goalSteps = GoalStep::query()
            ->leftJoin('goals', 'goal_steps.goal_id', '=', 'goals.id')
            ->where('goal_id', $goalId)
            ->where('goals.user_id', $request->user()->id)
            ->get()->all();

        return GoalCollection::make($goalSteps);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreRequest $request
     * @param int $goalId
     * @return GoalStepResource
     */
    public function store(StoreRequest $request, int $goalId): GoalStepResource
    {
        $validated = $request->validated();
        if (! Goal::query()->where('id', $goalId)->where('user_id', $request->user()->id)->exists()) {
            throw new GoalNotFoundException('Goal not exist');
        }

        $validated = array_merge($validated, [
            'user_id' => $request->user()->id,
            'goal_id' => $goalId,
            'amount' => null,
        ]);

        $goalStep = GoalStep::create($validated);

        return GoalStepResource::make($goalStep);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Request $request
     * @param int $goalId
     * @param int $id
     * @return JsonResponse
     */
    public function destroy(Request $request, int $goalId, int $id): JsonResponse
    {
        /** @var GoalStep $goalStep */
        $goalStep = GoalStep::query()
            ->leftJoin('goals', 'goal_steps.goal_id', '=', 'goals.id')
            ->where('goal_steps.id', $id)
            ->where('goals.id', $goalId)
            ->where('goals.user_id', $request->user()->id)
            ->first();
        if ($goalStep === null) {
            throw new GoalStepNotFoundException('Goal step not found.');
        }

        $goalStep->forceDelete();

        return response()->json([
            'status' => 'ok',
        ]);
    }
}
