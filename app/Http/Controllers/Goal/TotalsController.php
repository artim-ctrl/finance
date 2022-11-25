<?php

namespace App\Http\Controllers\Goal;

use App\Exceptions\Goal\GoalNotFoundException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Goal\Totals\IndexRequest;
use App\Models\Currency;
use App\Models\Goal;
use App\Models\GoalStep;
use App\Services\Course\Course;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TotalsController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param IndexRequest $request
     * @param int $goalId
     * @return JsonResponse
     */
    public function __invoke(IndexRequest $request, int $goalId): JsonResponse
    {
        $courses = $request->input('courses');

        /** @var Goal $goal */
        $goal = Goal::query()
            ->where('id', $goalId)
            ->where('user_id', $request->user()->id)
            ->with(['steps', 'steps.estimatedCurrency', 'steps.currency'])
            ->first();
        if ($goal === null) {
            throw new GoalNotFoundException('Goal not found.');
        }

        $currencies = Currency::all();

        // TODO: move to services
        $totalsByCurrency = [];
        $currencies->each(function (Currency $currency) use ($goal, &$totalsByCurrency) {
            $totalsByCurrency[$currency->code] = $goal->steps
                ->filter(fn(GoalStep $step) => $step->estimatedCurrency->code === $currency->code)
                ->pluck('estimated_amount')
                ->sum();
        });

        $totalsAll = [];
        $currencies->each(function (Currency $currency) use ($goal, &$totalsAll, $courses) {
            $totalsAll[$currency->code] = round(
                $goal->steps
                    ->map(fn(GoalStep $goalStep) =>
                        (($courses[$goalStep->estimatedCurrency->code][$currency->code] ?? 0) * $goalStep->estimated_amount)
                            ?: Course::getCourse($goalStep->estimatedCurrency->code, $currency->code, $goalStep->estimated_amount))
                    ->sum(),
                2
            );
        });

        $differencesByCurrency = [];
        $currencies->each(function (Currency $currency) use ($goal, &$differencesByCurrency) {
            $differencesByCurrency[$currency->code] = round(
                $goal->steps
                    ->filter(fn(GoalStep $goalStep) => $goalStep->amount !== null && $goalStep->currency !== null && $goalStep->currency->code === $currency->code)
                    ->map(fn(GoalStep $goalStep) => $goalStep->estimated_amount - $goalStep->amount)
                    ->sum(),
                2
            );
        });

        $differencesAll = [];
        $currencies->each(function (Currency $currency) use ($goal, &$differencesAll, $courses) {
            $map = fn(GoalStep $goalStep) => (($courses[$goalStep->estimatedCurrency->code][$currency->code] ?? 0) * $goalStep->estimated_amount) ?: Course::getCourse(
                    $goalStep->estimatedCurrency->code,
                    $currency->code,
                    $goalStep->estimated_amount
                ) - (($courses[$goalStep->currency->code][$currency->code] ?? 0) * $goalStep->estimated_amount) ?: Course::getCourse(
                    $goalStep->currency->code,
                    $currency->code,
                    $goalStep->amount
                );

            $differencesAll[$currency->code] = round(
                $goal->steps
                    ->filter(fn(GoalStep $goalStep) => $goalStep->amount !== null && $goalStep->currency !== null)
                    ->map($map)
                    ->sum(),
                2
            );
        });

        return response()->json([
            'data' => [
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
}
