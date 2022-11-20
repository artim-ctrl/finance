<?php

namespace App\Http\Controllers\Goal;

use App\Exceptions\Goal\GoalNotFoundException;
use App\Http\Controllers\Controller;
use App\Http\Resources\Goal\GoalCollection;
use App\Http\Resources\Goal\GoalResource;
use App\Models\Currency;
use App\Models\Goal;
use App\Models\GoalStep;
use App\Services\Course\Course;
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

    public function show(Request $request, int $id): JsonResponse
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

        $currencies = Currency::all();

        // TODO: move totals to separate method
        // TODO: move to services
        $totalsByCurrency = [];
        $currencies->each(function(Currency $currency) use ($goal, &$totalsByCurrency) {
            $totalsByCurrency[$currency->code] = $goal->steps
                ->filter(fn(GoalStep $step) => $step->estimatedCurrency->code === $currency->code)
                ->pluck('estimated_amount')
                ->sum();
        });

        $totalsAll = [];
        $currencies->each(function(Currency $currency) use ($goal, &$totalsAll) {
            $totalsAll[$currency->code] = round(
                $goal->steps
                    ->map(fn (GoalStep $goalStep) =>
                        Course::getCourse($goalStep->estimatedCurrency->code, $currency->code, $goalStep->estimated_amount)
                    )
                    ->sum(),
                2
            );
        });

        $differencesByCurrency = [];
        $currencies->each(function (Currency $currency) use ($goal, &$differencesByCurrency){
            $differencesByCurrency[$currency->code] = round(
                $goal->steps
                    ->filter(fn (GoalStep $goalStep) => $goalStep->currency->code === $currency->code && $goalStep->amount !== null)
                    ->map(fn (GoalStep $goalStep) => $goalStep->estimated_amount - $goalStep->amount)
                    ->sum(),
                2
            );
        });

        $differencesAll = [];
        $currencies->each(function (Currency $currency) use ($goal, &$differencesAll) {
            $map = fn (GoalStep $goalStep) =>
                Course::getCourse(
                    $goalStep->estimatedCurrency->code,
                    $currency->code,
                    $goalStep->estimated_amount
                ) - Course::getCourse(
                    $goalStep->currency->code,
                    $currency->code,
                    $goalStep->amount
                );

            $differencesAll[$currency->code] = round(
                $goal->steps
                    ->filter(fn (GoalStep $goalStep) => $goalStep->amount !== null)
                    ->map($map)
                    ->sum(),
                2
            );
        });

        return response()->json([
            'data' => [
                'goal' => GoalResource::make($goal),
                'totals' => [
                    'byCurrency' => $totalsByCurrency,
                    'all' => $totalsAll,
                ],
                'differences' => [
                    'byCurrency' => $differencesByCurrency,
                    'all' => $differencesAll,
                ],
            ],
        ]);
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
