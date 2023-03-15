<?php

namespace App\Http\Controllers\Goal;

use App\Exceptions\Goal\GoalNotFoundException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Goal\StoreData;
use App\Http\Resources\Goal\GoalCollection;
use App\Http\Resources\Goal\GoalResource;
use App\Models\Goal;
use Illuminate\Http\JsonResponse;

class GoalController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return GoalCollection
     */
    public function index(): GoalCollection
    {
        $goals = Goal::query()
            ->where('user_id', auth()->id())
            ->with('steps')
            ->get()->all();

        return GoalCollection::make($goals);
    }

    public function show(int $id): GoalResource
    {
        /** @var Goal|null $goal */
        $goal = Goal::query()
            ->where('id', $id)
            ->where('user_id', auth()->id())
            ->with('steps')
            ->first();
        if (null === $goal) {
            throw new GoalNotFoundException('Goal not found.');
        }

        return GoalResource::make($goal);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreData $data
     * @return GoalResource
     */
    public function store(StoreData $data): GoalResource
    {
        $validated = array_merge($data->all(), ['user_id' => auth()->id()]);

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
        /** @var Goal|null $goal */
        $goal = Goal::query()->where('id', $id)->first();
        if (null === $goal) {
            throw new GoalNotFoundException('Goal not found.');
        }

        $goal->forceDelete();

        return response()->json([
            'status' => 'ok',
        ]);
    }
}
