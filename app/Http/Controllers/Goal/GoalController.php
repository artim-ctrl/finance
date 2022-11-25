<?php

namespace App\Http\Controllers\Goal;

use App\Exceptions\Goal\GoalNotFoundException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Goal\StoreRequest;
use App\Http\Resources\Goal\GoalCollection;
use App\Http\Resources\Goal\GoalResource;
use App\Models\Goal;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class GoalController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return GoalCollection
     */
    public function index(Request $request): GoalCollection
    {
        $goals = Goal::query()
            ->where('user_id', $request->user()->id)
            ->with('steps')
            ->get()->all();

        return GoalCollection::make($goals);
    }

    public function show(Request $request, int $id): GoalResource
    {
        /** @var Goal $goal */
        $goal = Goal::query()
            ->where('id', $id)
            ->where('user_id', $request->user()->id)
            ->with('steps')
            ->first();
        if ($goal === null) {
            throw new GoalNotFoundException('Goal not found.');
        }

        return GoalResource::make($goal);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreRequest $request
     * @return GoalResource
     */
    public function store(StoreRequest $request): GoalResource
    {
        $validated = $request->validated();

        $validated = array_merge($validated, ['user_id' => $request->user()->id]);

        /** @var Goal $goal */
        $goal = Goal::create($validated);

        return GoalResource::make($goal);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function destroy(int $id): JsonResponse
    {
        /** @var Goal $goal */
        $goal = Goal::query()->where('id', $id)->first();
        if ($goal === null) {
            throw new GoalNotFoundException('Goal not found.');
        }

        $goal->forceDelete();

        return response()->json([
            'status' => 'ok',
        ]);
    }
}
