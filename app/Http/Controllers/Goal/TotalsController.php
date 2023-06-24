<?php

declare(strict_types = 1);

namespace App\Http\Controllers\Goal;

use App\Http\Controllers\Controller;
use App\Http\Requests\Goal\Totals\IndexData;
use App\Models\Currency;
use App\Models\Goal;
use App\Services\GoalStep\DifferencesGettingService;
use App\Services\GoalStep\TotalsGettingService;
use Illuminate\Http\JsonResponse;

final class TotalsController extends Controller
{
    public function __invoke(
        IndexData $data,
        int $goalId,
        TotalsGettingService $totalsGettingService,
        DifferencesGettingService $differencesGettingService,
    ): JsonResponse {
        $goal = Goal::whereId($goalId)
            ->whereUserId(auth()->id())
            ->with(relations: ['steps', 'steps.estimatedCurrency', 'steps.currency'])
            ->firstOrFail();

        $currencies = Currency::all();

        $totalsByCurrency = $totalsGettingService->getByCurrency($currencies, $goal);
        $totalsAll = $totalsGettingService->getAll($currencies, (array) $data->courses, $goal);

        $differencesByCurrency = $differencesGettingService->getByCurrency($currencies, $goal);
        $differencesAll = $differencesGettingService->getAll($currencies, (array) $data->courses, $goal);

        $leftByCurrency = $totalsGettingService->getByCurrency($currencies, $goal, left: true);
        $leftAll = $totalsGettingService->getAll($currencies, (array) $data->courses, $goal, left: true);

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
                'left' => [
                    'byCurrency' => $leftByCurrency,
                    'all' => $leftAll,
                ],
            ],
        ]);
    }
}
