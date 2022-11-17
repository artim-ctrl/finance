<?php

namespace App\Http\Controllers\Goal;

use App\Exceptions\Goal\GoalNotFoundException;
use App\Http\Controllers\Controller;
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

    public function show(int $id): GoalResource
    {
        $goal = Goal::query()
            ->where('id', $id)
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
     * @param Request $request
     * @return GoalResource
     */
    public function store(Request $request): GoalResource
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $validated = array_merge($validated, ['user_id' => $request->user()->id]);

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
        $goal = Goal::findOrFail($id);

        $goal->forceDelete();

        return response()->json([
            'status' => 'ok',
        ]);
    }
}
