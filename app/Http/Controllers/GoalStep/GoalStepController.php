<?php

namespace App\Http\Controllers\GoalStep;

use App\Http\Controllers\Controller;
use App\Http\Resources\Goal\GoalCollection;
use App\Http\Resources\GoalStep\GoalStepResource;
use App\Models\GoalStep;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

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
            ->rightJoin('goals', 'goal_steps.goal_id', '=', 'goals.id')
            ->where('goal_id', $goalId)
            ->where('goals.user_id', $request->user()->id)
            ->get()->all();

        return GoalCollection::make($goalSteps);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @param int $goalId
     * @return GoalStepResource
     */
    public function store(Request $request, int $goalId): GoalStepResource
    {
        // TODO: validate goalId for access
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'estimated_currency_id' => 'required|exists:currencies,id',
            'estimated_amount' => 'required|numeric',
        ]);

        $validated = array_merge($validated, [
            'user_id' => $request->user()->id,
            'goal_id' => $goalId,
            'currency_id' => null,
            'amount' => null,
        ]);

        $goalStep = GoalStep::create($validated);

        return GoalStepResource::make($goalStep);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     */
    public function destroy(Request $request, int $id): JsonResponse
    {
        /** @var GoalStep $goalStep */
        $goalStep = GoalStep::findOrFail($id);
        if ($goalStep->goal->user_id !== $request->user()->id) {
            throw (new ModelNotFoundException())->setModel(get_class($goalStep), $id);
        }

        $goalStep->forceDelete();

        return response()->json([
            'status' => 'ok',
        ]);
    }
}
